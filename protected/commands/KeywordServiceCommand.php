<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

class KeywordServiceCommand extends CConsoleCommand {
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
        
        $console->writeLine('Updating expired...');
        
        $criteria = new CDbCriteria();
        $criteria->addCondition('(unix_timestamp(checked_at) + period) < unix_timestamp()');
        $criteria->addNotInCondition('status', array(
            Keyword::STATUS_PENDING,
            Keyword::STATUS_TAKEN,
            Keyword::STATUS_IN_PROGRESS,
        ));
        
        Keyword::model()->updateAll(array(
            'status' => Keyword::STATUS_PENDING,
        ), $criteria);
        
        $console->writeLine('Fixing InProgress...');
        
        $keyword;

        if (!($keyword = Keyword::model()->findAll('status = \'' . Keyword::STATUS_IN_PROGRESS . '\''))) {
            $console->writeLine('No tasks');
            
            return;
        }
        
        $console->progressStart('Fixing', count($keyword));
        
        foreach ($keyword as $k) {
            $console->progressStep();

            $executor = Executor::model()->findAll('keyword_id = :keyword_id', array(
                ':keyword_id' => $k->id,
            ));

            if (!$executor) {
                $k->setStatus(Keyword::STATUS_PENDING);

                continue;
            }

            foreach ($executor as $e) {
                if (!in_array($e->status, array(
                    Executor::STATUS_CHECKING,
                    Executor::STATUS_COOLDOWN,
                ))) {
                    $e->stop();
                    $k->setStatus(Keyword::STATUS_PENDING);
                }
            }
        }
        
        $console->progressEnd();
    }
    
}
