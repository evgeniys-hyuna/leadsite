<?php
$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Keyword ALEXA</h3>
        </div>
        <div class="panel-body">
            <h3><?= $keyword->name ?></h3>
            
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keyword_alexa.search_method', array(
                'keyword' => $keyword,
                'alexaSearchMethod' => $alexaSearchMethod,
            )) ?>

            <p><?= CHtml::link('<< Back', Yii::app()->createUrl('site/keywordDetails', array(
                'keywordId' => $keyword->id,
            )), array(
                'class' => 'btn btn-info',
            )) ?></p>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Search Results</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keyword_alexa.results_grid', array(
                'keyword' => $keyword,
                'alexaSearchMethod' => $alexaSearchMethod,
            )) ?>
        </div>
    </div>
</div>