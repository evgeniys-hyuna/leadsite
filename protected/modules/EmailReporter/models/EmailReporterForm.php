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
            array('email', 'safe'),
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
        CVarDumper::dump($attributes, 10, true);
        die('Debug Point' . PHP_EOL);
        
        return parent::validate($attributes, false);
    }
    
}
