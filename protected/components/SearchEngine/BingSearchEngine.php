<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

class BingSearchEngine extends ASearchEngine {
    
    public function __construct() {
        $this->positionsPerPage = 10;
        $this->cooldown = 10;
    }
    
    public function getSearchEngine() {
        return Keyword::SEARCH_ENGINE_BING;
    }

    protected function getUrl() {
        return String::build('http://www.bing.com/search?q={query}&first={startPosition}', array(
            'query' => urlencode(str_replace(' ', '+', $this->query)),
            'startPosition' => ($this->pageNumber > 0 ? $this->pageNumber * $this->positionsPerPage : '0'),
        ));
    }

    public function getPageResults() {
        Console::writeLine('fetching');
        $this->fetch();
        
        file_put_contents(Yii::app()->basePath . '/reports/pagehtml.html', $this->pageHtml);
        
        CVarDumper::dump($this->response, 10, false);
        die('Debug Point');
    }

    public function getPosition($from = 1, $count = 1) {
        $this->pageNumber = ceil($from / $this->positionsPerPage);
        
        if ($count < 1) {
            return false;
        } elseif ($count == 1) {
            $pageResults = $this->getPageResults();
            
            $site = new Site();
            $site->name = 'test';
            $site->position = 0;
            $site->link = 'http://domain.com';

            return $site;
        }
        
        return false;
        
//        $sites = array();
//
//        for ($i = 0; $i < $count; $i++) {
//            $site = new Site();
//            $site->name = 'test';
//            $site->position = 0;
//            $site->link = 'http://domain.com';
//
//            $sites[] = $site;
//        }
//
//        return $sites;
    }

}
