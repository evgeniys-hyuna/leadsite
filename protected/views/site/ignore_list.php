<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Add</h3>
        </div>
        <div class="panel-body">
            <div class="form">
                <?= CHtml::beginForm() ?>

                <div class="row">
                    <?= CHtml::label('Domains (each in new line)', 'txaDomain') ?>

                    <?= CHtml::activeTextArea($ignoreList, 'domain', array(
                        'id' => 'txaDomain',
                        'cols' => 35,
                        'rows' => 5,
                    )) ?>
                </div>

                <div class="row">
                    <?= Chtml::submitButton('Add', array(
                        'class' => 'btn btn-success',
                    )) ?>
                </div>

                <?= CHtml::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>
        <div class="panel-body">
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
        </div>
    </div>
</div>
