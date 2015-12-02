<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

class GoogleSearchEngine extends ASearchEngine {
    
    public function __construct() {
        $this->positionsPerPage = 8;
    }
    
    public function getSearchEngine() {
        return Keyword::SEARCH_ENGINE_GOOGLE;
    }

    protected function getUrl() {
        return String::build('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=8&q={query}{startPosition}', array(
            'query' => urlencode($this->query),
            'startPosition' => ($this->pageNumber > 1 ? (('&start=' . ($this->pageNumber - 1) * $this->positionsPerPage) - 1) : ''),
        ));
    }

    public function getPageResults() {
        return $this->fetch();
    }

    public function getPosition($from = 1, $count = 1) {
        $console = Console::getInstance();
        $console->operationStart('Collecting search results');
        $this->pageNumber = ceil($from / $this->positionsPerPage);
        
        if ($count < 1 ||
                $count > 10) {
            $console->operationEnd();
            $console-error('Count must be in 1-10. ' . $count . ' is setted');
            
            return false;
        }
        
        $sites = array();
        $sitesCount = count($sites);
        
        do {
            $console->operationStep();
            $pageResults = $this->getPageResults();
            
            if (count($pageResults) <= 0) {
                throw new Exception('No page results fetched');
            }
            
            foreach ($pageResults as $pr) {
                $sitesCount = count($sites);
                $domain = ($domain = String::rebuildUrl($pr->url, false, false, true, false)) ? $domain : $pr->url;
                
                if (IgnoreList::isInList($domain) ||
                        ($sitesCount &&
                        $sites[$sitesCount - 1]->domain == $domain)) {
                    continue;
                }
                
                $site = new Site();
                $site->name = strip_tags($pr->title);
                $site->link = $pr->url;
                $site->domain = $domain;

                $sites[] = $site;
            }
            
            $this->pageNumber++;
        } while ($sitesCount < $count); //$this->pageNumber++ * $this->positionsPerPage < $count // old
        
        $console->operationEnd();
        
        return array_slice($sites, 0, $count);
    }
    
    protected function fetch($userAgent = null, $cookie = null, $referrer = null, \Proxy $proxy = null) {
        sleep(Settings::getValue(Settings::GOOGLE_SEARCH_COOLDOWN));
        
        $body = file_get_contents($this->getUrl());
        $json = json_decode($body);
        
        if ($json->responseStatus != 200) {
            throw new Exception(String::build('Can\'t fetch results from {search_engine}. Response status: {status} ({details})', array(
                'search_engine' => $this->getSearchEngine(),
                'status' => $json->responseStatus,
                'details' => $json->responseDetails,
            )));
        }
        
        return $json->responseData->results;
    }

}
