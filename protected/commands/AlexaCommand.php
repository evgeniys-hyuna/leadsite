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
        $workingDirectory = Yii::app()->basePath . '/../uploads/alexa';
        $zipUrl = 'http://s3.amazonaws.com/alexa-static/top-1m.csv.zip';
        $zipPath = $workingDirectory . '/top-1m.csv.zip';
        
        $console->operationStart('Downloading Alexa Rankings archive');
        
        if (!File::download($zipUrl, $zipPath)) {
            $console->error('Can\'t download file ' . $zipUrl);
            
            return;
        }
        
        $console->operationEnd();
        $console->operationStart('Extracting');
        
        if (!File::unzip($zipPath, $workingDirectory)) {
            $console->error('Can\'t unzip file ' . $zipPath);
            
            return;
        }
        
        $console->operationEnd();
        $console->writeLine('Update completed successfully');
        
        return;
    }
    
}
