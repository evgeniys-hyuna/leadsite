<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grvKeywords',
    'dataProvider' => $ignoreList->search(),
    'filter' => $ignoreList,
    'htmlOptions' => array(),
    'columns' => array(
        array(
            'name' => 'domain',
            'header' => 'Domain',
            'value' => function ($e) {
                return $e->domain;
            },
        ),
        array(
            'name' => 'created_at',
            'header' => 'Added On',
            'value' => function ($e) {
                return Time::toPretty($e->created_at);
            },
        ),
        array(
            'header' => 'Action',
            'type' => 'raw',
            'value' => function ($e) {
                return CHtml::link('Delete', Yii::app()->createUrl('site/ignoreListDelete', array(
                    'ignoreListId' => $e->id,
                )), array(
                    'class' => 'btn-sm btn-danger',
                ));
            },
        ),
    ),
)) ?>
