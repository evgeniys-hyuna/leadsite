<div>
    <p>ALEXA search method:</p>

    <div class="btn-group">
        <?= CHtml::link('Full', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_FULL,
        )), array(
            'class' => ($alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_FULL ? 'btn btn-info' : 'btn btn-default'),
        )) ?>
        <?= CHtml::link('Combo', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_COMBO,
        )), array(
            'class' => ($alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_COMBO ? 'btn btn-info' : 'btn btn-default'),
        )) ?>
        <?= CHtml::link('Partial', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_PARTIAL,
        )), array(
            'class' => ($alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_PARTIAL ? 'btn btn-info' : 'btn btn-default'),
        )) ?>
    </div>

    <p><i>
        <p>Full: searches all words from keyword in domain. Like: "watch movie" will be founded in "watchonlinemovie", but not in "watchonline".</p>
        <p>Combo: searches all combinations of all words from keyword. Like: "watch movie" will be founded in "moviewatch", but not in "watchonlinemovie"</p>
        <p>Partial: searches at least for 1 of words from keyword. Like: "watch movie" will be founded in "watchonline".</p>
    </i></p>
</div>
