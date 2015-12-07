<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grvKeywords',
    'dataProvider' => $executor->searchActive(),
    'htmlOptions' => array(),
    'columns' => array(
        array(
            'name' => 'id',
            'header' => 'ID',
            'value' => function ($e) {
                return $e->id;
            }
        ),
        array(
            'name' => 'keyword_id',
            'header' => 'Keyword',
            'value' => function ($e) {
                return String::build('{keyword} ({keyword_id})', array(
                    'keyword' => $e->keyword->name,
                    'keyword_id' => $e->keyword_id,
                ));
            }
        ),
        array(
            'header' => 'Search Engine',
            'value' => function ($e) {
                return ucwords($e->keyword->search_engine);
            }
        ),
        array(
            'name' => 'status',
            'header' => 'Status',
            'value' => function ($e) {
                return ucwords($e->status);
            }
        ),
        array(
            'name' => 'message',
            'header' => 'Message',
            'value' => function ($e) {
                return $e->message;
            }
        ),
        array(
            'name' => 'created_at',
            'header' => 'Created',
            'value' => function ($e) {
                return Time::toPretty($e->created_at);
            }
        ),
        array(
            'name' => 'updated_at',
            'header' => 'Updated',
            'value' => function ($e) {
                return Time::toPretty($e->updated_at);
            }
        ),
        array(
            'header' => '',
            'type' => 'raw',
            'value' => function ($e) {
                return CHtml::link('Terminate', Yii::app()->createUrl('site/terminateExecutor', array(
                    'executorId' => $e->id,
                )), array(
                    'class' => 'btn btn-sm btn-danger',
                ));
            }
        ),
    ),
));
