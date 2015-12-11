<?php

class DefaultController extends Controller {

    public function actionIndex() {
        $emailReporter = new EmailReporter();
        
        
        
        $this->render('index', array(
            'emailReporter' => $emailReporter,
        ));
    }
    
    public function actionAdd() {
        $emailReporterForm = new EmailReporterForm();
        
        $emailReporterForm->selectionPeriod = 0;
        $emailReporterForm->isUpdatedOnly = true;
        
        if (($postEmailReporterForm = Yii::app()->request->getParam('EmailReporterForm'))) {
            $emailReporterForm->setAttributes($postEmailReporterForm);
            
            if ($emailReporterForm->save()) {
                $this->redirect(Yii::app()->createUrl('EmailReporter/default/index'));
            }
        }
        
        $emailReporterForm->updatePeriodType = null;

        $this->render('add', array(
            'emailReporterForm' => $emailReporterForm,
        ));
    }
    
}
