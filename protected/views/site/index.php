<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvLeads',
        'dataProvider' => $site->searchLeads(),
        'columns' => array(
            array(
                'name' => 'domain',
                'header' => 'Domain',
                'value' => function ($e) {
                    return $e->domain;
                }
            ),
            array(
                'name' => 'keyword',
                'header' => 'Keyword',
                'value' => function ($e) {
                    return $e->keyword->name;
                }
            ),
            array(
                'name' => 'created_at',
                'header' => 'Added On',
                'value' => function ($e) {
                    return Time::toPretty($e->created_at);
                }
            ),
        ),
    )) ?>
</div>