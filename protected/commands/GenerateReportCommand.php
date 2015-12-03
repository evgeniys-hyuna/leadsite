<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of GenerateReportCommand
 *
 * @author jomedia_64
 */
class GenerateReportCommand extends CConsoleCommand {
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
        $reportsDirectory = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'reports';
        $currentReportDirectory = $reportsDirectory . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
        $currentLeadsReportPath = $currentReportDirectory . DIRECTORY_SEPARATOR . date(Time::FORMAT_PRETTY) . '.html';
        $currentAlexaReportPath = $currentReportDirectory . DIRECTORY_SEPARATOR . date(Time::FORMAT_PRETTY) . '.zip';
        $alexaTemporaryDirectory = $currentReportDirectory . DIRECTORY_SEPARATOR . 'alexa_tmp';
        
        if (!file_exists($currentReportDirectory)) {
            mkdir($currentReportDirectory, true);
        }
        
        if (!file_exists($alexaTemporaryDirectory)) {
            mkdir($alexaTemporaryDirectory, true);
        }
        
        // TODO
        
        // search leads
        
        // generate report
        
        // clear alexa temp directory
        
        // generate all keywords alexa
        
        // create zip report with updated keywords
        
        
        
        
        
        
        
        $reportFile = Yii::app()->getBasePath() . '/reports/' . date(Time::FORMAT_STANDART) . '.html';
        $alexaTempDirectory = Yii::app()->getBasePath() . '/reports/alexa_temp';
        $reportHtml = '';
        
        // Leads
        $console->writeLine('Leads');
        $console->operationStart('Searching');
        
        $reportHtml .= '<h3>Leads</h3>';
        
        $criteria = new CDbCriteria();
        $criteria->alias = 'site';
        $criteria->with = array(
            'keyword',
        );
        $criteria->addCondition('site.deleted_at IS NULL');
        $criteria->addCondition('keyword.deleted_at IS NULL');
        $criteria->group = 'site.domain';
        $criteria->distinct = true;
        $site = Site::model()->findAll($criteria);
        
        $console->operationEnd();
        $console->progressStart('Collecting', count($site));

        $reportHtml .= '<table>';
        $row = '<tr>';
        $row .= '<th>Domain</th>';
        $row .= '<th>Keyword</th>';
        $row .= '<th>Added On</th>';
        $row .= '</tr>';
        $reportHtml .= $row;
        
        foreach ($site as $s) {
            $console->progressStep();
            
            if (IgnoreList::isInList($s->domain)) {
                continue;
            }
            
            $row = '<tr>';
            $row .= '<td>' . $s->domain . '</td>';
            $row .= '<td>' . $s->keyword->name . '</td>';
            $row .= '<td>' . Time::toPretty($s->created_at) . '</td>';
            $row .= '</tr>';
            $reportHtml .= $row;
        }
        
        $reportHtml .= '</table>';
        file_put_contents($reportFile, $reportHtml);
        
        $console->progressEnd();
        
        // Alexa
        
        $console->writeLine('Alexa');
        $console->operationStart('Initializing');
        
        $reportHtml = '<h3>Alexa</h3>';
        file_put_contents($reportFile, $reportHtml, FILE_APPEND);
        
        $keyword = Keyword::model()->findAll();
        
        if (!file_exists($alexaTempDirectory)) {
            mkdir($alexaTempDirectory);
        }
        
        $console->operationEnd();
        $console->operationStart('Cleaning temporary files');
        
//        $temporaryFiles = scandir($alexaTempDirectory);
//        
//        foreach ($temporaryFiles as $f) {
//            $console->operationStep();
//            
//            if (in_array($f, array('.', '..'))) {
//                continue;
//            }
//            
//            $path = $alexaTempDirectory . DIRECTORY_SEPARATOR . $f;
//            
//            $console->debug('Deleting: ' . $path);
//            
//            unlink();
//        }
        
        $console->operationEnd();
        $console->progressStart('Generating alexa reports', count($keyword));
        
        foreach ($keyword as $k) {
            $console->progressStep();
//            $console->debug($k->name);
//            
//            $reportHtml = '<p>Keyword: ' . $k->name . '</p><br />';
//            $reportHtml .= $k->alexaToHtml(Keyword::ALEXA_SEARCH_METHOD_PARTIAL);
//            
//            file_put_contents($alexaTempDirectory . DIRECTORY_SEPARATOR . $k->name . '.html', $reportHtml);
//            
//            unset($reportHtml);
        }
        
        $console->progressEnd();
        $console->operationStart('Selecting updated keywords');
        
        $updatedKeywords = Yii::app()->db->createCommand()
                ->select('name')
                ->from('lds_keyword')
                ->where('unix_timestamp(created_at) > unix_timestamp(now()) - :period', array(
                    ':period' => Time::SECONDS_IN_DAY,
                ))
                ->orWhere('unix_timestamp(updated_at) > unix_timestamp(now()) - :period', array(
                    ':period' => Time::SECONDS_IN_DAY,
                ))
                ->queryColumn();
        
        $console->operationEnd();
        $console->operationStart('Creating alexa zip achive');
        
        $zipPath = Yii::app()->getBasePath() . '/reports/' . date(Time::FORMAT_STANDART) . ' alexa.zip';
        $zip = new ZipArchive();
        
        if (!$zip->open($zipPath, ZipArchive::CREATE)) {
            die('Can\'t open or create ZIP file' . PHP_EOL);
        }
        
        $console->debug('Searching for files in: ' . $alexaTempDirectory);
        $files = scandir($alexaTempDirectory);
        
        foreach ($files as $f) {
            $console->operationStep();
            
            if (in_array($f, array('.', '..'))) {
                continue;
            }
            
            if (in_array(substr($f, 0, strpos($f, pathinfo($f, PATHINFO_EXTENSION)) - 1), $updatedKeywords)) {
                $path = $alexaTempDirectory . DIRECTORY_SEPARATOR . $f;
                
                $console->debug('Adding to archive: ' . $path);
                
                $zip->addFile($path, $f);
            }
        }
        
        $zip->close();
        
        $console->operationEnd();
        
        // Stamp
        
        $console->writeLine('Stamp');
        
        $reportHtml = '<p><i>Report generated on ' . date(Time::FORMAT_PRETTY) . '</i></p>';
        file_put_contents($reportFile, $reportHtml, FILE_APPEND);
        
        $console->writeLine('Report generated');
        
        return;
    }
    
}
