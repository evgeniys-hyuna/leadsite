<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>Browse Reports</h1>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'columns' => array(
            array(
                'name' => 'name',
                'header' => 'Reports Directory',
                'type' => 'raw',
                'value' => function ($e) {
                    return CHtml::link($e['name'], Yii::app()->createUrl('site/download', array(
                        'filename' => $e['name'],
                    )));
                },
            ),
        ),
    )) ?>
</div>
