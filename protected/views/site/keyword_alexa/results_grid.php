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
