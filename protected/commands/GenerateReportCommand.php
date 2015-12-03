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
        $reportFile = Yii::app()->getBasePath() . '/reports/' . date(Time::FORMAT_STANDART) . '.html';
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
        
        $console->operationEnd();
        $console->progressStart('Searching', count($keyword));
        
        foreach ($keyword as $k) {
            $console->progressStep();
            $console->debug($k->name);
            
            $reportHtml = '<p>Keyword: ' . $k->name . '</p><br />';
            $reportHtml .= $k->alexaToHtml(Keyword::ALEXA_SEARCH_METHOD_PARTIAL);
            
            file_put_contents($reportFile, $reportHtml, FILE_APPEND);
            unset($reportHtml);
        }
        
        $console->progressEnd();
        
        // Stamp
        
        $console->writeLine('Stamp');
        
        $reportHtml = '<p><i>Report generated on ' . date(Time::FORMAT_PRETTY) . '</i></p>';
        file_put_contents($reportFile, $reportHtml, FILE_APPEND);
        
        $console->writeLine('Report generated');
        
        return;
    }
}
