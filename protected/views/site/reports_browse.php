<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Browse Reports</h3>
        </div>
        <div class="panel-body">
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
        </div>
    </div>
</div>