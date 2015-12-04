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
            'value' => function ($e) use ($currentDirectory) {
                return CHtml::link($e['name'], Yii::app()->createUrl('site/reportsBrowse', array(
                    'directory' => $currentDirectory . DIRECTORY_SEPARATOR . $e['name'],
                )));
            },
        ),
    ),
)) ?>
