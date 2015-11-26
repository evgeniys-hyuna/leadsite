<?php

class File {
    
    public static function download($url, $file) {
        if (!($file = fopen($file, 'w'))) {
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_FILE, $file);
        $result = curl_exec($ch);
        curl_close($ch);
        
        return empty($result) ? false : $file;
    }
    
    public static function zip($file, $zip) {}
    
    public static function unzip($zipFile, $directory) {
        $zip = new ZipArchive();
        
        if (!$zip->open($zipFile)) {
            return false;
        }
        
        $zip->extractTo($directory);
        $zip->close();
        
        return true;
    }
    
}
