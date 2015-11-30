<?php
$this->pageTitle=Yii::app()->name;
?>

<h1>Keyword ALEXA</h1>
<h3><?= $keyword->name ?></h3>

<p><?= CHtml::link('<< Back', Yii::app()->createUrl('site/keywordDetails', array(
    'keywordId' => $keyword->id,
))) ?></p>

<div>
    <p>
        Alexa search method:
    
        <?= $alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_FULL ? '<b>Full</b>' : CHtml::link('Full', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_FULL,
        ))) ?>

        <?= $alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_COMBO ? '<b>Combo</b>' : CHtml::link('Combo', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_COMBO,
        ))) ?>
    
        <?= $alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_PARTIAL ? '<b>Partial</b>' : CHtml::link('Partial', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_PARTIAL,
        ))) ?>
    </p>
    <p><i>
        Full: searches all words from keyword in domain. Like: "watch movie" will be founded in "watchonlinemovie", but not in "watchonline". <br />
        Combo: searches all combinations of all words from keyword. Like: "watch movie" will be founded in "moviewatch", but not in "watchonlinemovie" <br />
        Partial: searches at least for 1 of words from keyword. Like: "watch movie" will be founded in "watchonline". <br />
    </i></p>
</div>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvAlexa',
        'dataProvider' => $keyword->searchAlexaRankings($alexaSearchMethod),
        'columns' => array(
            array(
                'name' => 'position',
                'header' => 'Position',
                'value' => function ($e) {
                    return $e['position'];
                }
            ),
            array(
                'name' => 'domain',
                'header' => 'Domain',
                'value' => function ($e) {
                    return $e['domain'];
                }
            ),
        ),
    )) ?>
</div>
