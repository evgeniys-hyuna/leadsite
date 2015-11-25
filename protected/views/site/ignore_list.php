<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="form">
    <?= CHtml::beginForm() ?>
    
    <div class="row">
        <?= CHtml::label('Domains (each in new line)', 'txaDomain') ?>
        
        <?= CHtml::activeTextArea($ignoreList, 'domain', array(
            'id' => 'txaDomain',
            'cols' => 40,
            'rows' => 5,
        )) ?>
    </div>
    
    <div class="row">
        <?= Chtml::submitButton('Add') ?>
    </div>
    
    <?= CHtml::endForm() ?>
</div>

<div>
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
                'header' => '',
                'type' => 'raw',
                'value' => function ($e) {
                    return CHtml::link('Delete', Yii::app()->createUrl('site/ignoreListDelete', array(
                        'ignoreListId' => $e->id,
                    )));
                },
            ),
        ),
    )) ?>
</div>
