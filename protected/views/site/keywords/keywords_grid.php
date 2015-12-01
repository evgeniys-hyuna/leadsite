<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grvKeywords',
    'dataProvider' => $keyword->search(),
    'filter' => $keyword,
    'htmlOptions' => array(),
    'columns' => array(
        array(
            'name' => 'name',
            'header' => 'Keyword',
            'type' => 'raw',
            'value' => function ($e) {
                return String::build('<a href="{link}" title="Added on {created_at}">{keyword}</a>', array(
                    'link' => Yii::app()->createUrl('site/keywordDetails', array(
                        'keywordId' => $e->id,
                    )),
                    'created_at' => Time::toPretty($e->created_at),
                    'keyword' => $e->name,
                ));
            },
        ),
        array(
            'name' => 'search_engine',
            'header' => 'Search Engine',
            'value' => function ($e) {
                return ucwords($e->search_engine);
            }
        ),
        array(
            'name' => 'status',
            'header' => 'Status',
            'type' => 'raw',
            'value' => function ($e) {
                return CHtml::tag('span', array(
                    'title' => String::build('Last Check: {last_check} Next Check: {next_check}', array(
                        'last_check' => Time::toPretty($e->checked_at),
                        'next_check' => $e->period ? date(Time::FORMAT_PRETTY, strtotime($e->checked_at) + $e->period) : 'No autocheck',
                    )),
                ), ucwords($e->status));
            },
        ),
    ),
)) ?>