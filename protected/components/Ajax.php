<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ajax
 *
 * @author jomedia_64
 */
class Ajax {
    
    public static function reply($status, $data = false) {
        echo CJSON::encode(array(
            'status' => $status,
            'data' => $data,
        ));
        
        Yii::app()->end();
    }
    
}
