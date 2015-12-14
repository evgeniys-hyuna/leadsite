<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of ExecutorCommand
 *
 * @author jomedia_64
 */
class ReportCommand extends CConsoleCommand {
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
        
        
//        Yii::import('application.extensions.phpmailer.JPhpMailer');
//        $mail = new JPhpMailer();
//        $mail->SetFrom('noreply@domani.com');
//        $mail->AddAddress('evgeniy.siderka@hyuna.bb');
//        $mail->MsgHTML('Hello from PHPMailer');
//        $mail->AddAttachment(Settings::getValue(Settings::LAST_REPORT_ALEXA));
//        $mail->AddAttachment(Settings::getValue(Settings::LAST_REPORT_LEADS));
//        
//        $console->writeLine($mail->Send() ? 'OK' : 'Failed');
//        
//        CVarDumper::dump('END', 10, false);
//        die('Debug Point' . PHP_EOL);
        
        $console->writeLine('Searching for tasks');
        
        $report = Report::model()->findAll();
        
        if (count($report) <= 0) {
            $console->writeLine('No tasks');
            
            return;
        }
        
        $console->progressStart('Sending reports', count($report));
        
        foreach ($report as $r) {
            $console->progressStep();
            
            if ($r->isTimeToUpdate()) {
                try {
                    $r->send();
                } catch (Exception $ex) {
                    $console->error($ex->getMessage());
                }
            }
        }
        
        $console->progressEnd();
        
        return;
    }
}
