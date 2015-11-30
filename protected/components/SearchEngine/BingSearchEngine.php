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
//        $this->fetch();
        $this->pageHtml = file_get_contents(Yii::app()->basePath . '/reports/pagehtml.html');
//        file_put_contents(Yii::app()->basePath . '/reports/pagehtml.html', $this->pageHtml);
        
        $results = String::getTagsBySelector('li', '.b_algo', $this->pageHtml);
        $sites = array();
        
        for ($i = 0; $i < count($results); $i++) {
            $site = new Site();
            $site->name = String::getTagContent($results[$i], 'h2');
            $site->position = $i + 1;
            $site->domain = String::rebuildUrl(String::getTagContent($results[$i], 'cite'), false, false, true, false);
            $site->link = String::getTagAttribute($results[$i], 'a', 'href');
            
            $sites[] = $site;
        }
        
        return $sites;
    }

    public function getPosition($from = 1, $count = 1) {
        $this->pageNumber = ceil($from / $this->positionsPerPage);
        $pageResults = $this->getPageResults();
        
        if ($count < 1) {
            return false;
        } elseif ($count == 1) {
            return !empty($pageResults[$from - 1]) ? $pageResults[$from - 1] : false;
        }
        
        return $pageResults;
    }

}
