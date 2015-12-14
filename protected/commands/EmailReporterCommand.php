<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of ExecutorCommand
 *
 * @author jomedia_64
 */
class EmailReporterCommand extends CConsoleCommand {
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
        
        $console->writeLine('Searching for tasks');
        
        $criteria = new CDbCriteria();
        $criteria->alias = 'email_reporter';
        $criteria->with = array(
            'emailReportTypes',
        );
        $criteria->addCondition('email_reporter.deleted_at IS NULL');
        $criteria->addCondition('emailReportTypes.name = :email_report_type');
        $criteria->params = array(
            ':email_report_type' => 'leads',
        );
        
        $emailReporter = array_filter(EmailReporter::model()->findAll($criteria), function ($e) {
            return $e->isUpdateNeeded();
        });
        $emailReporterCount = count($emailReporter);
        
        if ($emailReporterCount <= 0) {
            $console->writeLine('No tasks');
            
            return;
        }
        
        $console->writeLine($emailReporterCount . ' jobs founded');
        $console->operationStart('Sending reports');
        
        foreach ($emailReporter as $er) {
            $console->operationStep();
            $er->send();
        }
        
        $console->operationEnd();
        
        return;
    }
}
