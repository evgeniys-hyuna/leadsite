<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grvKeywords',
    'dataProvider' => $tag->search(),
//    'filter' => $keyword,
    'htmlOptions' => array(),
    'columns' => array(
        array(
            'name' => 'name',
            'header' => 'Name',
            'type' => 'raw',
            'value' => function ($e) {
                return String::build('<a href="{link}" title="Added on {created_at}">{name}</a>', array(
                    'link' => Yii::app()->createUrl('site/tagDetails', array(
                        'tagId' => $e->id,
                    )),
                    'created_at' => Time::toPretty($e->created_at),
                    'name' => $e->name,
                ));
            },
        ),
        array(
            'name' => 'description',
            'header' => 'Description',
            'filter' => false,
            'value' => function ($e) {
                return $e->description;
            },
        ),
        array(
            'header' => 'Keywords',
            'value' => function ($e) {
                return count($e->keywords);
            },
        ),
        array(
            'name' => 'created_at',
            'header' => 'Added on',
            'filter' => false,
            'value' => function ($e) {
                return Time::toPretty($e->created_at);
            },
        ),
    ),
)) ?>
