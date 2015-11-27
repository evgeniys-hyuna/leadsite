<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

abstract class ASearchEngine {
    const STATUS_FREE = 'free';
    const STATUS_BUSY = 'busy';
    const STATUS_COOLDOWN = 'cooldown';
    
    protected $query;
    protected $pageNumber;
    protected $pageHtml;
    protected $startPosition;
    protected $positionsPerPage;
    protected $response;
    protected $cooldown;
    protected $status;
    
    protected abstract function getUrl();
    
    public function getStatus() {
        return $this->_status;
    }
    
    public function search($query) {
        $this->query = $query;
    }
    
    public abstract function getSearchEngine();
    
    public abstract function getPageResults();
    
    public abstract function getPosition($from = 1, $to = 30);
    
    protected function fetch($userAgent = null, $cookie = null, $referrer = null, Proxy $proxy = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        if ($userAgent) {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
        }
        
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        }
        
        if ($referrer) {
            curl_setopt($ch, CURLOPT_REFERER, $referrer);
        }
        
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, "$proxy->ip");
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$proxy->password");
        }
        
        $this->pageHtml = curl_exec($ch);
        $this->response = curl_getinfo($ch);
        
        curl_close($ch);
        
//        $this->pageHtml = file_get_contents(Yii::app()->getBasePath() . '/reports/test.html');
//        $this->response = 200;
    }
}
