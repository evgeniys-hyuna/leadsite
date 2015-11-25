<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvKeywords',
        'dataProvider' => $settings->search(),
        'filter' => $settings,
        'htmlOptions' => array(),
        'columns' => array(
            array(
                'name' => 'name',
                'header' => 'Parameter',
                'value' => function ($e) {
                    return $e->name;
                },
            ),
            array(
                'name' => 'value',
                'header' => 'Value',
                'value' => function ($e) {
                    return $e->value;
                },
            ),
        ),
    )) ?>
</div>