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
            
            <div>
                <p>
                    ALEXA search method:
                    
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
                </p>
                <p><i>
                    <p>Full: searches all words from keyword in domain. Like: "watch movie" will be founded in "watchonlinemovie", but not in "watchonline".</p>
                    <p>Combo: searches all combinations of all words from keyword. Like: "watch movie" will be founded in "moviewatch", but not in "watchonlinemovie"</p>
                    <p>Partial: searches at least for 1 of words from keyword. Like: "watch movie" will be founded in "watchonline".</p>
                </i></p>
            </div>

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
        </div>
    </div>
</div>