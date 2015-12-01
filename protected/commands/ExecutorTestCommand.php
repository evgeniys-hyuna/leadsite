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
        $console->writeLine('Initializing');
        
        
        
        $se = new YahooSearchEngine();
        $se->search('watch movie online');
        $sites = $se->getPosition(1, 10);
        
        foreach ($sites as $s) {
            CVarDumper::dump($s->attributes, 10, false);
        }
        
        
        CVarDumper::dump('TEST ENDED', 10, false);
        die('Debug Point' . PHP_EOL);
        
        
        
        

        $executor = new Executor();
        
        // Search for task
        $console->writeLine('Searching for tasks');

        try {
            if (!$executor->findTask()) {
                $console->writeLine('No new tasks');

                return;
            }
        } catch (Exception $ex) {
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
            case Keyword::SEARCH_ENGINE_BING:
                $searchEngine = new BingSearchEngine();
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
        
        $executor->stop();
//        
//        // Mark previous results as deleted
//        $previousSiteCriteria = new CDbCriteria();
//        $previousSiteCriteria->alias = 'site';
//        $previousSiteCriteria->addCondition('site.keyword_id = :keyword_id');
//        $previousSiteCriteria->params = array(
//            ':keyword_id' => $executor->keyword_id,
//        );
//        $previousSiteCriteria->order = 'site.executor_id DESC';
//        $previousSiteCriteria->limit = 1;
//        
//        if (($previousSite = Site::model()->find($previousSiteCriteria))) {
//            Site::model()->updateAll(array(
//                'deleted_at' => date(Time::FORMAT_STANDART),
//            ), 'executor_id = :executor_id', array(
//                ':executor_id' => $previousSite->executor_id,
//            ));
//        }
//
//        // Save new results
//        $console->progressStart('Saving results', count($sites));
//
//        foreach ($sites as $s) {
//            $console->progressStep();
//
//            $s->keyword_id = $executor->keyword_id;
//            $s->executor_id = $executor->id;
//            $s->save();
//        }
//
//        $console->progressEnd();
//
//        $executor->keyword->setStatus(Keyword::STATUS_CHECKED);
//        $executor->setStatus(Executor::STATUS_PENDING);
//        
//        if ($executor->status == Executor::STATUS_ERROR) {
//            $executor->setStatus(Executor::STATUS_COOLDOWN);
//            sleep(Settings::getValue(Settings::ABUSE_COOLDOWN));
//        }
//        
//        $executor->stop();
//        $console->writeLine('Execution terminated');
//        
        return;
    }
}
