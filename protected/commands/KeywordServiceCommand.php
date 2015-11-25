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
        $console->writeLine('Initializing');
        
        $criteria = new CDbCriteria();
        $criteria->alias = 'keyword';
        $criteria->addCondition('keyword.period > 0');
        $criteria->addNotInCondition('keyword.status', array(
            Keyword::STATUS_PENDING,
            Keyword::STATUS_TAKEN,
            Keyword::STATUS_IN_PROGRESS,
        ));
        
        $keyword = Keyword::model()->findAll($criteria);
        
        $console->progressStart('Updating keywords', count($keyword));
        
        foreach ($keyword as $k) {
            $console->progressStep();
            
            if (time() > strtotime($k->checked_at) + $k->period) {
                $k->setStatus(Keyword::STATUS_PENDING);
            }
        }
        
        $console->progressEnd();
    }
}
