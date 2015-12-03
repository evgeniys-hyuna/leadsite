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
        $csvPath = $workingDirectory . '/top-1m.csv';
        // Split
        $csvFile = fopen($csvPath, 'r');
        $partsDirectory = $workingDirectory . '/parts';
        $partDelimiter = 50000;
        $part = 0;
        $partContent = array();
        $i = 0;
        
//        $console->operationStart('Downloading Alexa Rankings archive');
//        
//        if (!File::download($zipUrl, $zipPath)) {
//            $console->error('Can\'t download file ' . $zipUrl);
//            
//            return;
//        }
//        
//        $console->operationEnd();
//        $console->operationStart('Extracting');
//        
//        if (!File::unzip($zipPath, $workingDirectory)) {
//            $console->error('Can\'t unzip file ' . $zipPath);
//            
//            return;
//        }
//        
//        $console->operationEnd();
//        $console->writeLine('Update completed successfully');
        $console->operationStart('Preparing and cleaning parts directory');
        
        if (!file_exists($partsDirectory)) {
            mkdir($partsDirectory);
        }
        
        $files = scandir($partsDirectory);
        
        foreach ($files as $f) {
            $console->operationStep();
            
            if (in_array($f, array('.', '..'))) {
                continue;
            }
            
            unlink($partsDirectory . DIRECTORY_SEPARATOR . $f);
        }
        
        $console->operationEnd();
        $console->operationStart('Splitting');
        
        while (($row = fgetcsv($csvFile))) {
            array_push($partContent, $row);

            if ($i++ >= $partDelimiter - 1) {
                $console->operationStep();
                $i = 0;
                $partPath = $partsDirectory . DIRECTORY_SEPARATOR . ++$part . '.csv';
            
                if (count($partContent) > 0) {
                    $partFile = fopen($partPath, 'a');

                    foreach ($partContent as $pc) {
                        fputcsv($partFile, $pc);
                    }

                    fclose($partFile);
                    unset($partContent);
                    $partContent = array();
                }
            }
        }
        
        $console->operationEnd();
        
        return;
    }
    
}
