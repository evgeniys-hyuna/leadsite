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
