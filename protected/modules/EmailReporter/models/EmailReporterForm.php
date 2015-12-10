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
    public $selectionTags;
    
    public function rules() {
        return array(
            array('email', 'email'),
            array('email', 'safe'),
        );
    }
    
//    public function validate($attributes = null, $clearErrors = true) {
//        
//        
//        return parent::validate($attributes, $clearErrors);
//    }
}
