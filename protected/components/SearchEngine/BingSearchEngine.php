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
        $this->fetch();
        
        $results = String::getTagsBySelector('li', '.b_algo', $this->pageHtml);
        $sites = array();
        
        for ($i = 0; $i < count($results); $i++) {
            $site = new Site();
            $site->name = String::getTagContent($results[$i], 'h2');
            $site->domain = String::rebuildUrl(String::getTagContent($results[$i], 'cite'), false, false, true, false);
            $site->link = String::getTagAttribute($results[$i], 'a', 'href');
            
            $sites[] = $site;
        }
        
        return count($sites) > 0 ? $sites : false;
    }

    public function getPosition($from = 1, $count = 1) {
        $console = Console::getInstance();
        $console->operationStart('Collecting search results');
        $this->pageNumber = ceil($from / $this->positionsPerPage);
        $sites = array();
        
        if ($count < 1 ||
                $count > 10) {
            $console->operationEnd();
            $console-error('Count must be in 1-10. ' . $count . ' is setted');
            
            return false;
        }
        
        $sitesCount = count($sites);

        do {
            $console->operationStep();
            $pageResults = $this->getPageResults();
            
            if (!$pageResults) {
                throw new Exception('No page results fetched');
            }
            
            foreach ($pageResults as $pr) {
                $sitesCount = count($sites);
                
                if (IgnoreList::isInList($pr->domain) ||
                        ($sitesCount &&
                        $sites[$sitesCount - 1]->domain == $pr->domain)) {
                    continue;
                }
                
                $sites[] = $pr;
            }
            
            $this->pageNumber++;
        } while ($sitesCount < $count);
        
        $console->operationEnd();
        
        return array_slice($sites, 0, $count);
    }

}
