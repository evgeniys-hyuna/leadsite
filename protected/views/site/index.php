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
                'type' => 'raw',
                'value' => function ($e) {
                    return CHtml::tag('span', array(
                        'title' => String::build('by {search_engine} on {position} position', array(
                            'search_engine' => $e->keyword->search_engine,
                            'position' => $e->position,
                        )),
                    ), Time::toPretty($e->created_at));
                }
            ),
        ),
    )) ?>
</div>