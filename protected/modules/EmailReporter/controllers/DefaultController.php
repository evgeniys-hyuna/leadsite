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
        
//        CVarDumper::dump($_GET, 10, true);
//        CVarDumper::dump($_POST, 10, true);
        
        if (($postEmailReporterForm = Yii::app()->request->getParam('EmailReporterForm'))) {
            $emailReporterForm->setAttributes($postEmailReporterForm);
            
            $emailReporterForm->validate();
        }
        
        $this->render('add', array(
            'emailReporterForm' => $emailReporterForm,
        ));
    }
    
}
