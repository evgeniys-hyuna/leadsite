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

<!--<h3>Keywords and history</h3>

<div>
    </?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvLeads',
        'dataProvider' => $keyword->buildLeads(),
        'columns' => array(
            array(
                'name' => 'keyword',
                'header' => 'Keyword',
                'type' => 'raw',
                'value' => function ($e) {
                    return String::build('<a href="{link}" title="Added on {created_at}">{keyword}</a>', array(
                        'link' => Yii::app()->createUrl('site/keywordDetails', array(
                            'keywordId' => $e['id'],
                        )),
                        'created_at' => Time::toPretty($e->created_at),
                        'keyword' => $e['keyword'],
                    ));
                }
            ),
            array(
                'name' => 'domain',
                'header' => 'Top Result',
                'type' => 'raw',
                'value' => function ($e) {
                    return $e['domain'] . ' ' . CHtml::link('View all', Yii::app()->createUrl('site/allResults', array(
                        'keywordId' => $e['id'],
                    )));
                }
            ),
            array(
                'name' => 'search_engine',
                'header' => 'Search Engine',
                'value' => function ($e) {
                    return $e['search_engine'];
                }
            ),
            array(
                'name' => 'updated',
                'header' => 'Updated',
                'value' => function ($e) {
                    return Time::toPretty($e['updated']);
                }
            ),
        ),
    )) ?>
</div>-->
