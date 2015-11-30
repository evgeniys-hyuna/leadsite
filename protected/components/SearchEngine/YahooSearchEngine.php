<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

class YahooSearchEngine extends ASearchEngine {
    
    public function __construct() {
        $this->positionsPerPage = 10;
        $this->cooldown = 10;
    }
    
    public function getSearchEngine() {
        return Keyword::SEARCH_ENGINE_YAHOO;
    }

    protected function getUrl() {
        return String::build('https://search.yahoo.com/search?p={query}&ei=UTF-8&fr=yfp-t-901&fp=1&b=11&pz=10&bct=0&pstart={startPosition}', array(
            'query' => urlencode(str_replace(' ', '+', $this->query)),
            'startPosition' => ($this->pageNumber > 0 ? $this->pageNumber * $this->positionsPerPage : '0'),
        ));
    }

    public function getPageResults() {
        $this->fetch();
        
        $results = String::getTagsBySelector('div', '.dd algo', $this->pageHtml);
        $sites = array();
        
        for ($i = 0; $i < count($results); $i++) {
            $site = new Site();
            $site->name = String::build(String::getTagContent($results[$i], 'h3'));
            $site->position = $i + 1;
            $site->domain = String::rebuildUrl(String::getTagContent($results[$i], 'span'), false, false, true, false);
            $site->link = String::getTagAttribute(String::getTagContent($results[$i], 'h3', false), 'a', 'href');
            
            $sites[] = $site;
        }
        
        return $sites;
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

        do {
            $console->operationStep();
            $pageResults = $this->getPageResults();
            
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