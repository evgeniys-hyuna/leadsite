<?php

/**
 * Description of AjaxController
 *
 * @author jomedia_64
 */
class AjaxController extends Controller {
    
    public function actionSendEmail($emailReporterId) { 
        $emailReporter = EmailReporter::model()->findByPk($emailReporterId);
        
        try {
            $emailReporter->send();
        } catch (Exception $ex) {
            Ajax::reply(false, $ex->getMessage());
        }
        
        Ajax::reply(true);
    }
    
}
