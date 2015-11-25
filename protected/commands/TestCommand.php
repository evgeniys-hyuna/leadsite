<?php

/**
 * Description of TestCommand
 *
 * @author Siderka Eugene
 */
class TestCommand extends CConsoleCommand {
    
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
        
        $console->debugStart('Entered');
        
//        if (mail('evgeniy.siderka@hyuna.bb', 'test', 'test message')) {
//            $console->writeLine('OK');
//        } else {
//            $console->writeLine('Failure');
//        }
        
        $newExecutors = Site::getNewExecutors('2015-11-19 12:20:00');
        $report = '';
        
        foreach ($newExecutors as $e) {
            $sites = Site::model()->findAllByAttributes(array(
                'executor_id' => $e,
            ));
            
            if (count($sites) < 1) {
                continue;
            }
            
            $report .= String::build('Positions for "{keyword}" ({date_from} - {date_to})', array(
                'keyword' => $sites[0]->keyword->name,
                'date_from' => Time::toPretty($sites[0]->updated_at),
                'date_to' => Time::toPretty($sites[count($sites) - 1]->updated_at),
            )) . PHP_EOL;
            
            foreach ($sites as $s) {
                $report .= String::build('{position}: {site}', array(
                    'position' => $s->position,
                    'site' => String::rebuildUrl($s->link, false, false, true, false),
                )) . PHP_EOL;
            }
        }
        
        $console->writeLine($report);
        
        $console->debugEnd();
        
        return;
    }
}
