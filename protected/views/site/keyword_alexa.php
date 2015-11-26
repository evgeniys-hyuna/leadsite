<?php
$this->pageTitle=Yii::app()->name;
?>

<h1>Keyword ALEXA</h1>
<h3><?= $keyword->name ?></h3>

<div>
    <p>
        Alexa search method:

        <?= $alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_COMBO ? '<b>Combo</b>' : CHtml::link('Combo', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_COMBO,
        ))) ?>
    
        <?= $alexaSearchMethod == Keyword::ALEXA_SEARCH_METHOD_PARTIAL ? '<b>Partial</b>' : CHtml::link('Partial', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
            'alexaSearchMethod' => Keyword::ALEXA_SEARCH_METHOD_PARTIAL,
        ))) ?>
    </p>
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
