<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of ExecutorCommand
 *
 * @author jomedia_64
 */
class ExecutorCommand extends CConsoleCommand {
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
        
        $console->writeLine('Updating keywords');
        
        $this->updateKeywordsStatus();
        
        $console->writeLine('Initializing');
        
//        if (Executor::model()->count('deleted_at IS NULL') >= Settings::getValue(Settings::SIMULTANEOUS_EXECUTORS_LIMIT)) {
//            $console->error('Executors limit is reached');
//            
//            return;
//        }
        
        $executor = new Executor();
        
        // Search for task
        $console->writeLine('Searching for tasks');

        try {
            if (!$executor->findTask()) {
                $console->writeLine('No new tasks');
//                $executor->stop();

                return;
            }
        } catch (Exception $ex) {
//            $executor->keyword->setStatus(Keyword::STATUS_PENDING);
//            $executor->setStatus(Executor::STATUS_ERROR);
            $console->error($ex->getMessage());
            
            return;
        }
        
        $executor->save();
        
        // Start check
        $console->writeLine('Checking keyword "' . $executor->keyword->name . '"');

        $executor->keyword->setStatus(Keyword::STATUS_IN_PROGRESS);
        $executor->setStatus(Executor::STATUS_CHECKING);
        
        // Select search engine
        $searchEngine;
        
        switch ($executor->keyword->search_engine) {
            case Keyword::SEARCH_ENGINE_GOOGLE:
                $searchEngine = new GoogleSearchEngine();
                break;
            case Keyword::SEARCH_ENGINE_GOOGLE_ES:
                $searchEngine = new GoogleSearchEngineEs();
                break;
            case Keyword::SEARCH_ENGINE_GOOGLE_IT:
                $searchEngine = new GoogleSearchEngineIt();
                break;
            case Keyword::SEARCH_ENGINE_GOOGLE_FR:
                $searchEngine = new GoogleSearchEngineFr();
                break;
            default:
                $errorMessage = 'Unknown search engine: ' . $executor->keyword->search_engine;
                $executor->keyword->setStatus(Keyword::STATUS_PENDING);
                $executor->status = Executor::STATUS_ERROR;
                $executor->message = $errorMessage;
                $executor->update();
                $console->error($errorMessage);

                return;
        }
        
        $console->writeLine('Using ' . $searchEngine->getSearchEngine() . ' search engine');
        $searchEngine->search($executor->keyword->name);
        $sites;

        try {
            $sites = $searchEngine->getPosition(1, 10);
        } catch (Exception $ex) {
            $executor->keyword->setStatus(Keyword::STATUS_PENDING);
            $executor->status = Executor::STATUS_ERROR;
            $executor->message = $ex->getMessage();
            $executor->update();
            $console->error($ex->getMessage());
            
            return;
        }

        // Save results
        $console->progressStart('Saving results', count($sites));

        foreach ($sites as $s) {
            $console->progressStep();

            $s->keyword_id = $executor->keyword_id;
            $s->executor_id = $executor->id;
            $s->save();
        }

        $console->progressEnd();

        $executor->keyword->setStatus(Keyword::STATUS_CHECKED);
        $executor->setStatus(Executor::STATUS_PENDING);
        
        if ($executor->status == Executor::STATUS_ERROR) {
            $executor->setStatus(Executor::STATUS_COOLDOWN);
            sleep(Settings::getValue(Settings::ABUSE_COOLDOWN));
        }
        
        $executor->stop();
        $console->writeLine('Execution terminated');
        
        return;
    }
    
    private function updateKeywordsStatus() {
        $criteria = new CDbCriteria();
        $criteria->alias = 'keyword';
        $criteria->addCondition('keyword.period > 0');
        $criteria->addNotInCondition('keyword.status', array(
            Keyword::STATUS_PENDING,
            Keyword::STATUS_TAKEN,
            Keyword::STATUS_IN_PROGRESS,
        ));
        
        $keyword = Keyword::model()->findAll($criteria);
        
        foreach ($keyword as $k) {
            if (time() > strtotime($k->checked_at) + $k->period) {
                $k->setStatus(Keyword::STATUS_PENDING);
            }
        }
    }
}
