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
        $csvFile;
        
//        $console->operationStart('Downloading');
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
        $console->operationStart('Opening');
        
        if (!($csvFile = fopen($csvPath, 'r'))) {
            $console->error('Can\'t open file ' . $csvPath);
            
            return;
        }
        
        $console->operationEnd();
        $console->writeLine('Reading file');
        $keyword = explode(' ', 'watch movie online');
        $reportHtml = '';
        $reportFile = Yii::app()->getBasePath() . '/../uploads/alexa/report.html';
        $count = 0;
        
        $timeStart = time();
        
        while (($row = fgetcsv($csvFile))) {
            foreach ($keyword as $k) {
                if (($pos = strpos($row[1], $k)) !== false) {
//                        $console->writeLine($keyword . ' is founded in ' . $row[1] . ' on pos ' . $pos);
                    $reportHtml .= '<p>' . $row[0] . '. ' . $row[1] . '</p>';
                    $count++;
                }
            }
        }
        
        $console->writeLine('Readed for ' . (time() - $timeStart) . ' seconds');
        $console->writeLine('Founded ' . $count . ' results');
        
        file_put_contents($reportFile, $reportHtml);
        
        return;
    }
    
}
