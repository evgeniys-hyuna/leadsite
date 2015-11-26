<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of ExecutorCommand
 *
 * @author jomedia_64
 */
class AlexaCommand extends CConsoleCommand {
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
        
//        $alexa = new AlexaSearchEngine();
        
        $console->writeLine('Downloading...');
//        $zipFile = File::download('http://s3.amazonaws.com/alexa-static/top-1m.csv.zip', Yii::app()->basePath . '/reports/download.zip');
        $zipFile = fopen(Yii::app()->basePath . '/reports/download.zip', 'r');
        $alexaRankingsFile = File::unzip($zipFile, Yii::app()->getBasePath() . '/reports/alexa/');
        
        $console->writeLine($zipFile ? 'Success' : 'Failure');
        
        return;
    }
    
}
