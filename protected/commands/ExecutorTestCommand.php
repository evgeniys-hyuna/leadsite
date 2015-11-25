<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of ExecutorCommand
 *
 * @author jomedia_64
 */
class ExecutorTestCommand extends CConsoleCommand {
    public function beforeAction($action, $params) {
        Console::writeLine('Command started');
        
        return parent::beforeAction($action, $params);
    }
    
    public function afterAction($action, $params, $exitCode = 0) {
        Console::writeLine('Command ended');
        
        return parent::afterAction($action, $params, $exitCode);
    }
    
    public function actionIndex($isForced = false, $isDebug = false) {
        $console = Console::getInstance($isForced, $isDebug);
        $terminated = false;
        
        $console->writeLine('Initializing');
        
//        if (Executor::model()->count() >= Settings::getValue(Settings::SIMULTANEOUS_EXECUTORS_LIMIT)) {
//            $console->error('Executors limit is reached');
//            
//            return;
//        }

        $executor = new Executor();
        $executor->save();
        $executorTaskSearchCooldown = Settings::getValue(Settings::EXECUTOR_TASK_SEARCH_COOLDOWN);
        $executorTaskSearchLimit = Settings::getValue(Settings::EXECUTOR_TASK_SEARCH_LIMIT);
        
        while (!$terminated) {
            // Search for task
            $console->operationStart('Searching for tasks');
            $executor->setStatus(Executor::STATUS_SEARCHING);

            try {
                $attempt = 1;
                
                while (!$executor->findTask()) {
                    sleep($executorTaskSearchCooldown);

                    $console->operationStep();
                    
                    if ($attempt++ > $executorTaskSearchLimit) {
                        $console->writeLine('No new tasks');
                        $executor->delete();
                        
                        return;
                    }
                }
                
                $executor->refresh();
            } catch (Exception $ex) {
                $executor->keyword->setStatus(Keyword::STATUS_PENDING);
                $executor->setStatus(Executor::STATUS_ERROR);
                $console->error($ex->getMessage());
                $terminated = true;

                continue;
            }

            $console->operationEnd();

            // Start check
            $console->writeLine('Checking keyword "' . $executor->keyword->name . '"');

            $executor->keyword->setStatus(Keyword::STATUS_IN_PROGRESS);
            $executor->setStatus(Executor::STATUS_CHECKING);
            $googleSearchEngine = new GoogleSearchEngineIt();
            $googleSearchEngine->search($executor->keyword->name);
            $sites;

            try {
                $sites = $googleSearchEngine->getPosition(1, 10);
            } catch (Exception $ex) {
                $executor->keyword->setStatus(Keyword::STATUS_PENDING);
                $executor->status = Executor::STATUS_ERROR;
                $executor->message = $ex->getMessage();
                $executor->update();
                $console->error($ex->getMessage());
                $terminated = true;

                continue;
            }

            // Save results
            $console->progressStart('Saving results', count($sites));

            foreach ($sites as $s) {
                $console->progressStep();

                $s->keyword_id = $executor->keyword_id;
                $s->save();
            }

            $console->progressEnd();
            
            $executor->keyword->setStatus(Keyword::STATUS_CHECKED);
            $executor->setStatus(Executor::STATUS_PENDING);
        }
        
        if ($executor->status == Executor::STATUS_ERROR) {
            $executor->setStatus(Executor::STATUS_COOLDOWN);
            sleep(Settings::getValue(Settings::ABUSE_COOLDOWN));
        }
        
        $executor->delete();
        $console->writeLine('Execution terminated');
        
        return;
    }
}
