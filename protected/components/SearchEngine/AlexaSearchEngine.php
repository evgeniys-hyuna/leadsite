<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

class GoogleSearchEngine extends ASearchEngine {
    
    public function __construct() {
        $this->positionsPerPage = 8;
    }
    
    public function getSearchEngine() {
        return Keyword::SEARCH_ENGINE_ALEXA;
    }

    protected function getUrl() {
        return String::build('http://www.alexa.com/topsites/global;{startPage}', array(
            'startPage' => $this->pageNumber - 1,
        ));
    }

    public function getPageResults() {
        $this->fetch();
        
        CVarDumper::dump('111', 10, false);
        die('Debug Point' . PHP_EOL);
    }

    // rewrite
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
        $position = 1;
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
                $site->position = $position++;
                $site->link = $pr->url;
                $site->domain = $domain;

                $sites[] = $site;
            }
            
            $this->pageNumber++;
        } while ($sitesCount < $count); //$this->pageNumber++ * $this->positionsPerPage < $count // old
        
        $console->operationEnd();
        
        return array_slice($sites, 0, $count);
    }

}
