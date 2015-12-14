<?php

class DefaultController extends Controller {

    public function actionIndex() {
        $emailReporter = new EmailReporter();
        $emailReporterForm = new EmailReporterForm();
        
        $emailReporterForm->selectionPeriod = 0;
        $emailReporterForm->isUpdatedOnly = true;
        
        if (($postEmailReporterForm = Yii::app()->request->getParam('EmailReporterForm'))) {
            $emailReporterForm->setAttributes($postEmailReporterForm);
            
            if ($emailReporterForm->save()) {
                $this->refresh();
            }
        }
        
        $emailReporterForm->updatePeriodType = null;
        
        $this->render('index', array(
            'emailReporter' => $emailReporter,
            'emailReporterForm' => $emailReporterForm,
        ));
    }
    
}
