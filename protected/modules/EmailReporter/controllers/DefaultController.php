<?php

class DefaultController extends Controller {

    public function actionIndex() {
        $emailReporter = new EmailReporter();
        $emailReporterForm = new EmailReporterForm();
        
        $emailReporterForm->selectionPeriod = 0;
        $emailReporterForm->isUpdatedOnly = true;
        
        if (($postEmailReporterForm = Yii::app()->request->getParam('EmailReporterForm'))) {
            $emailReporterForm->setAttributes($postEmailReporterForm);
            
            if ($emailReporterForm->add()) {
                $this->refresh();
            }
        }
        
        $emailReporterForm->updatePeriodType = null;
        
        $this->render('index', array(
            'emailReporter' => $emailReporter,
            'emailReporterForm' => $emailReporterForm,
        ));
    }
    
    public function actionEdit($emailReporterId) {
        $emailReporterForm = new EmailReporterForm();
        $emailReporter = EmailReporter::model()->findByPk($emailReporterId);
        
        if (($postEmailReporterForm = Yii::app()->request->getParam('EmailReporterForm'))) {
            $emailReporterForm->setAttributes($postEmailReporterForm);
            
            if ($emailReporterForm->edit($emailReporterId)) {
                $this->redirect(Yii::app()->createUrl('/EmailReporter/default/index'));
            }
        } else {
            $emailReporterForm->email = $emailReporter->getEmails();
            $emailReporterForm->reportTypes = $emailReporter->getEmailReportTypes();
            $emailReporterForm->updatePeriodType = $emailReporter->emailPeriod->email_period_type_id;
            $emailReporterForm->selectionPeriod = $emailReporter->selection_period;
            $emailReporterForm->isUpdatedOnly = $emailReporter->is_updated_only;
            $emailReporterForm->selectionTags = $emailReporter->getTags();
            
            switch ($emailReporterForm->updatePeriodType) {
                case EmailPeriodType::TYPE_DAYS_OF_THE_WEEK:
                    $emailReporterForm->updatePeriodValueDays = $emailReporter->emailPeriod->value;
                    break;
                case EmailPeriodType::TYPE_DATES_OF_THE_MONTH:
                    $emailReporterForm->updatePeriodValueDates = $emailReporter->emailPeriod->value;
                    break;
                case EmailPeriodType::TYPE_MONTHS_OF_THE_YEAR:
                    $emailReporterForm->updatePeriodValueMonths = $emailReporter->emailPeriod->value;
                    break;
            }
        }
        
        $emailReporterForm->updatePeriodType = null;
        
        $this->render('edit', array(
            'emailReporter' => $emailReporter,
            'emailReporterForm' => $emailReporterForm,
        ));
    }
    
}
