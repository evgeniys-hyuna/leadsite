<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

class GoogleSearchEngineIt extends GoogleSearchEngine {
    
    public function getSearchEngine() {
        return Keyword::SEARCH_ENGINE_GOOGLE_IT;
    }
    
    public function getUrl() {
        return String::build('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&hl=it&rsz=8&q={query}{startPosition}', array(
            'query' => urlencode($this->query),
            'startPosition' => ($this->pageNumber > 1 ? (('&start=' . ($this->pageNumber - 1) * $this->positionsPerPage) - 1) : ''),
        ));
    }
}
