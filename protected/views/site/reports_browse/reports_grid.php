<?php $this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider' => $dataProvider,
    'htmlOptions' => array(
        'class' => 'table',
    ),
    'columns' => array(
        array(
            'name' => 'name',
            'header' => false,
            'type' => 'raw',
            'value' => function ($e) {
                return CHtml::link($e['name'], Yii::app()->createUrl('site/download', array(
                    'filename' => $e['name'],
                )));
            },
        ),
    ),
)) ?>
