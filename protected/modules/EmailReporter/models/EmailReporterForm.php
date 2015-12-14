<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailReporterForm
 *
 * @author jomedia_64
 */
class EmailReporterForm extends CFormModel {
    public $email;
    public $reportTypes;
    public $updatePeriodType;
    public $updatePeriodValueDays;
    public $updatePeriodValueDates;
    public $updatePeriodValueMonths;
    public $selectionPeriod;
    public $isUpdatedOnly;
    public $selectionTags;
    
    public function rules() {
        return array(
            array('email, reportTypes, updatePeriodType, updatePeriodValueDays, updatePeriodValueDates, updatePeriodValueMonths, selectionPeriod, isUpdatedOnly, selectionTags', 'safe'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'email' => 'Emails',
            'reportTypes' => 'Report Types',
            'updatePeriodType' => 'Update By',
            'updatePeriodValueDays' => '',
            'updatePeriodValueDates' => '',
            'updatePeriodValueMonths' => '',
            'selectionPeriod' => 'Selection Period',
            'isUpdatedOnly' => 'Updated Only',
            'selectionTags' => 'Selection Tags',
        );
    }
    
    public function validate($attributes = null, $clearErrors = true) {
        /**
         * Email
         */
        
        if (is_array($this->email)) {
            foreach ($this->email as $e) {
                if (!String::isEmail($e)) {
                    $this->addError('email', 'Email "' . $e . '" is incorrect');
                }
            }
        } else {
            $this->addError('email', 'Add at least 1 email');
        }
        
        /**
         * Report Type
         */
        
        if (empty($this->reportTypes)) {
            $this->addError('reportTypes', 'Select at least 1 report type');
        }
        
        /**
         * Update Period Type
         */
        
        switch ($this->updatePeriodType) {
            case EmailPeriodType::TYPE_DAYS_OF_THE_WEEK:
                if (empty($this->updatePeriodValueDays)) {
                    $this->addError('updatePeriodType', 'Update period is empty');
                }
                break;
            case EmailPeriodType::TYPE_DATES_OF_THE_MONTH:
                if (empty($this->updatePeriodValueDates)) {
                    $this->addError('updatePeriodType', 'Update period is empty');
                }
                break;
            case EmailPeriodType::TYPE_MONTHS_OF_THE_YEAR:
                if (empty($this->updatePeriodValueMonths)) {
                    $this->addError('updatePeriodType', 'Update period is empty');
                }
                break;
            default:
                $this->addError('updatePeriodType', 'Incorrect update period');
                break;
        }
        
        /**
         * Selection Period
         */
        
        if (!is_numeric($this->selectionPeriod)) {
            $this->addError('selectionPeriod', 'Value must be numeric');
        }
        
        return parent::validate($attributes, false);
    }
    
    public function add() {
        if (!$this->validate()) {
            return false;
        }
        
        $transaction = Yii::app()->db->beginTransaction();
        
        try {
            /**
             * Email Period
             */

            $emailPeriod = new EmailPeriod();
            $emailPeriod->email_period_type_id = $this->updatePeriodType;

            switch ($this->updatePeriodType) {
                case EmailPeriodType::TYPE_DAYS_OF_THE_WEEK:
                    $emailPeriod->value = $this->updatePeriodValueDays;
                    break;
                case EmailPeriodType::TYPE_DATES_OF_THE_MONTH:
                    $emailPeriod->value = $this->updatePeriodValueDates;
                    break;
                case EmailPeriodType::TYPE_MONTHS_OF_THE_YEAR:
                    $emailPeriod->value = $this->updatePeriodValueMonths;
                    break;
                default:
                    throw new Exception('Unknown period type ' . $this->updatePeriodType);
                    break;
            }

            if (!$emailPeriod->save()) {
                throw new Exception('Can\'t save email period: ' . print_r($emailPeriod->getErrors(), true));
            }

            /**
             * Email Reporter
             */

            $emailReporter = new EmailReporter();
            $emailReporter->email_period_id = $emailPeriod->id;
            $emailReporter->selection_period = $this->selectionPeriod;
            $emailReporter->is_updated_only = $this->isUpdatedOnly;

            if (!$emailReporter->save()) {
                throw new Exception('Can\'t save email reporter: ' . print_r($emailReporter->getErrors(), true));
            }

            /**
             * Email
             */
            
            foreach ($this->email as $e) {
                $email = null;
                
                if (!($email = Email::model()->findByAttributes(array(
                    'address' => $e,
                )))) {
                    $email = new Email();
                    $email->address = $e;

                    if (!$email->save()) {
                        throw new Exception('Can\'t save email: ' . print_r($email->getErrors(), true));
                    }
                }

                $email->bindToEmailReporter($emailReporter->id);
            }

            /**
             * Email Report Type
             */

            foreach ($this->reportTypes as $t) {
                if (($emailReportType = EmailReportType::model()->findByAttributes(array(
                    'name' => $t,
                )))) {
                    $emailReportType->bindToEmailReporter($emailReporter->id);
                } else {
                    throw new Exception('No email report type: ' . $t);
                }
            }

            /**
             * Tag
             */

            if (is_array($this->selectionTags)) {
                foreach ($this->selectionTags as $t) {
                    if (($tag = Tag::model()->findByAttributes(array(
                        'name' => $t,
                    )))) {
                        $tag->bindToEmailReporter($emailReporter->id);
                    }
                }
            }
            
            $transaction->commit();
            
            return true;
        } catch (Exception $ex) {
            $transaction->rollback();

            throw new Exception($ex->getMessage());
        }
    }
    
    public function edit($emailReporterId) {
        if (!$this->validate()) {
            return false;
        }
        
        $transaction = Yii::app()->db->beginTransaction();
        $emailReporter = EmailReporter::model()->findByPk($emailReporterId);
        
        try {
            /**
             * Email Period
             */
            
            $emailPeriodValue = 0;
            
            switch ($this->updatePeriodType) {
                case EmailPeriodType::TYPE_DAYS_OF_THE_WEEK:
                    $emailPeriodValue = $this->updatePeriodValueDays;
                    break;
                case EmailPeriodType::TYPE_DATES_OF_THE_MONTH:
                    $emailPeriodValue = $this->updatePeriodValueDates;
                    break;
                case EmailPeriodType::TYPE_MONTHS_OF_THE_YEAR:
                    $emailPeriodValue = $this->updatePeriodValueMonths;
                    break;
                default:
                    throw new Exception('Unknown period type ' . $this->updatePeriodType);
                    break;
            }
            
            EmailPeriod::model()->updateByPk($emailReporter->email_period_id, array(
                'email_period_type_id' => $this->updatePeriodType,
                'value' => CJSON::encode($emailPeriodValue),
            ));

            /**
             * Email Reporter
             */
            
            EmailReporter::model()->updateByPk($emailReporter->id, array(
                'selection_period' => $this->selectionPeriod,
                'is_updated_only' => $this->isUpdatedOnly,
            ));

            /**
             * Email
             */
            
            Yii::app()->db->createCommand()->delete('lds_email_reporter_email', 'email_reporter_id = :email_reporter_id', array(
                ':email_reporter_id' => $emailReporter->id,
            ));
            
            foreach ($this->email as $e) {
                $email = null;
                
                if (!($email = Email::model()->findByAttributes(array(
                    'address' => $e,
                )))) {
                    $email = new Email();
                    $email->address = $e;

                    if (!$email->save()) {
                        throw new Exception('Can\'t save email: ' . print_r($email->getErrors(), true));
                    }
                }

                $email->bindToEmailReporter($emailReporter->id);
            }

            /**
             * Email Report Type
             */
            
            Yii::app()->db->createCommand()->delete('lds_email_reporter_report_type', 'email_reporter_id = :email_reporter_id', array(
                ':email_reporter_id' => $emailReporter->id,
            ));

            foreach ($this->reportTypes as $t) {
                if (($emailReportType = EmailReportType::model()->findByAttributes(array(
                    'name' => $t,
                )))) {
                    $emailReportType->bindToEmailReporter($emailReporter->id);
                } else {
                    throw new Exception('No email report type: ' . $t);
                }
            }

            /**
             * Tag
             */
            
            Yii::app()->db->createCommand()->delete('lds_email_reporter_tag', 'email_reporter_id = :email_reporter_id', array(
                ':email_reporter_id' => $emailReporter->id,
            ));

            if (is_array($this->selectionTags)) {
                foreach ($this->selectionTags as $t) {
                    if (($tag = Tag::model()->findByAttributes(array(
                        'name' => $t,
                    )))) {
                        $tag->bindToEmailReporter($emailReporter->id);
                    }
                }
            }
            
            $transaction->commit();
            
            return true;
        } catch (Exception $ex) {
            $transaction->rollback();

            throw new Exception($ex->getMessage());
        }
    }
    
}
