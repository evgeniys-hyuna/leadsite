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
    private $_console;
    private $_reportsDirectory;
    private $_currentReportDirectory;
    private $_currentLeadsReportPath;
    private $_currentAlexaReportPath;
    private $_alexaTemporaryDirectory;
    
    public function __construct($name, $runner) {
        $this->_reportsDirectory = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'reports';
        $this->_currentReportDirectory = $this->_reportsDirectory . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
        $this->_currentLeadsReportPath = $this->_currentReportDirectory . DIRECTORY_SEPARATOR . date(Time::FORMAT_PRETTY) . '.html';
        $this->_currentAlexaReportPath = $this->_currentReportDirectory . DIRECTORY_SEPARATOR . date(Time::FORMAT_PRETTY) . '.zip';
        $this->_alexaTemporaryDirectory = $this->_reportsDirectory . DIRECTORY_SEPARATOR . 'alexa_tmp';
        
        return parent::__construct($name, $runner);
    }
    
    public function beforeAction($action, $params) {
        Console::writeLine('Command started');
        
        return parent::beforeAction($action, $params);
    }
    
    public function afterAction($action, $params, $exitCode = 0) {
        Console::writeLine('Command ended');
        
        return parent::afterAction($action, $params, $exitCode);
    }
    
    public function actionIndex($isForced = false, $isDebug = false) {
        $this->_console = Console::getInstance($isForced, $isDebug);
        
        if (!file_exists($this->_currentReportDirectory)) {
            $this->_console->debug('Creating directory: ' . $this->_currentReportDirectory);
            mkdir($this->_currentReportDirectory, 0777, true);
        }
        
        if (!file_exists($this->_alexaTemporaryDirectory)) {
            $this->_console->debug('Creating directory: ' . $this->_alexaTemporaryDirectory);
            mkdir($this->_alexaTemporaryDirectory, 0777, true);
        }
        
        /*
         * Leads report
         */
        
        $this->generateLeadsReport();
        
        /*
         * Clear alexa temp directory
         */
        
        $this->cleanAlexaTempDirectory();
        
        /*
         * Generate all keywords alexa report
         */
        
        $this->generateAlexaReports();
        
        /*
         * Alexa report
         */
        
        $this->alexaReport();
        
        return;
    }
    
    private function generateLeadsReport() {
        $this->_console->operationStart('Searching leads');
        
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
        
        $this->_console->operationEnd();
        $this->_console->progressStart('Collecting', count($site));

        $reportHtml = '<h3>Leads</h3><table>';
        $reportHtml .= '<tr><th>Domain</th><th>Keyword</th><th>Added On</th></tr>';;
        
        foreach ($site as $s) {
            $this->_console->progressStep();
            
            if (IgnoreList::isInList($s->domain)) {
                continue;
            }
            
            $reportHtml .= String::build('<tr><td>{domain}</td><td>{keyword}</td><td>{date}</td></tr>', array(
                'domain' => $s->domain,
                'keyword' => $s->keyword->name,
                'date' => Time::toPretty($s->created_at),
            ));
        }
        
        $reportHtml .= '</table>';
        
        file_put_contents($this->_currentLeadsReportPath, $reportHtml);

        $this->_console->progressEnd();
    }
    
    private function cleanAlexaTempDirectory() {
        $temporaryFiles = scandir($this->_alexaTemporaryDirectory);
        
        $this->_console->progressStart('Clearing alexa temporary directory', count($temporaryFiles));

        foreach ($temporaryFiles as $f) {
            $this->_console->progressStep();
            
            if (in_array($f, array('.', '..'))) {
                continue;
            }
            
            $path = $this->_alexaTemporaryDirectory . DIRECTORY_SEPARATOR . $f;
            
            $this->_console->debug('Deleting: ' . $path);
            
            unlink($path);
        }
        
        $this->_console->progressEnd();
    }
    
    private function generateAlexaReports() {
        $keyword = Keyword::model()->findAll();
        
        $this->_console->progressStart('Generating alexa reports', count($keyword));
        
        foreach ($keyword as $k) {
            $this->_console->progressStep();
            $this->_console->debug($k->name);
            
            $reportHtml = '<p>Keyword: ' . $k->name . '</p><br />';
            $reportHtml .= $k->alexaToHtml(Keyword::ALEXA_SEARCH_METHOD_PARTIAL);
            
            file_put_contents($this->_alexaTemporaryDirectory . DIRECTORY_SEPARATOR . $k->name . '.html', $reportHtml);
            
            unset($reportHtml);
        }
        
        $this->_console->progressEnd();
    }
    
    private function alexaReport() {
        $this->_console->operationStart('Selecting updated keywords');
        
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
        
        $this->_console->operationEnd();
        $this->_console->operationStart('Creating alexa zip achive');
        
        $zip = new ZipArchive();
        
        if (!$zip->open($this->_currentAlexaReportPath, ZipArchive::CREATE)) {
            die('Can\'t open or create ZIP file' . PHP_EOL);
        }
        
        $this->_console->debug('Searching for files in: ' . $this->_alexaTemporaryDirectory);
        $files = scandir($this->_alexaTemporaryDirectory);
        
        foreach ($files as $f) {
            $this->_console->operationStep();
            
            if (in_array($f, array('.', '..'))) {
                continue;
            }
            
            if (in_array(substr($f, 0, strpos($f, pathinfo($f, PATHINFO_EXTENSION)) - 1), $updatedKeywords)) {
                $this->_console->debug('Adding to archive: ' . $f);
                $zip->addFile($this->_alexaTemporaryDirectory . DIRECTORY_SEPARATOR . $f, $f);
            }
        }
        
        $zip->close();
        
        $this->_console->operationEnd();
    }
    
}
