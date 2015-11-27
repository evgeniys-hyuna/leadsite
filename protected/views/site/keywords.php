<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="form">
    <?= CHtml::beginForm() ?>
    
    <div class="row">
        <?= CHtml::label('Keywords (each in new line)', 'txaKeywords') ?>
        
        <?= CHtml::activeTextArea($keywordForm, 'keywords', array(
            'id' => 'txaKeywords',
            'cols' => 40,
            'rows' => 5,
        )) ?>
    </div>
    
    <div class="row">
        <?= CHtml::label('Autocheck (in seconds)', 'txfPeriod') ?>
        
        <?= CHtml::dropDownList('ddlPeriod', Time::SECONDS_IN_WEEK, array(
//            Time::SECONDS_IN_MINUTE => 'Every minute',
            Time::SECONDS_IN_HOUR => 'Every hour',
            Time::SECONDS_IN_DAY => 'Every day',
            Time::SECONDS_IN_WEEK => 'Every week',
            Time::SECONDS_IN_MONTH => 'Every month',
            Time::SECONDS_IN_YEAR => 'Every year',
        ), array(
            'id' => 'ddlPeriod',
        )) ?>
        
        <?= CHtml::activeTextField($keywordForm, 'period', array(
            'id' => 'txfPeriod',
            'value' => Time::SECONDS_IN_WEEK,
        )) ?>
    </div>
    
    <div class="row">
        <?= CHtml::label('Search Engine', 'ddlSearchEngine') ?>
        
        <?= CHtml::activeDropDownList($keywordForm, 'searchEngine', array(
            Keyword::SEARCH_ENGINE_BING => Keyword::SEARCH_ENGINE_BING,
//            Keyword::SEARCH_ENGINE_YAHOO => Keyword::SEARCH_ENGINE_YAHOO,
            Keyword::SEARCH_ENGINE_GOOGLE => Keyword::SEARCH_ENGINE_GOOGLE,
            Keyword::SEARCH_ENGINE_GOOGLE_ES => Keyword::SEARCH_ENGINE_GOOGLE_ES,
            Keyword::SEARCH_ENGINE_GOOGLE_IT => Keyword::SEARCH_ENGINE_GOOGLE_IT,
            Keyword::SEARCH_ENGINE_GOOGLE_FR => Keyword::SEARCH_ENGINE_GOOGLE_FR,
        ), array(
            'id' => 'ddlSearchEngine',
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
        'dataProvider' => $keyword->search(),
        'filter' => $keyword,
        'htmlOptions' => array(),
        'columns' => array(
            array(
                'name' => 'name',
                'header' => 'Keyword',
                'type' => 'raw',
                'value' => function ($e) {
                    return String::build('<a href="{link}" title="Added on {created_at}">{keyword}</a>', array(
                        'link' => Yii::app()->createUrl('site/keywordDetails', array(
                            'keywordId' => $e->id,
                        )),
                        'created_at' => Time::toPretty($e->created_at),
                        'keyword' => $e->name,
                    ));
                },
            ),
            array(
                'name' => 'search_engine',
                'header' => 'Search Engine',
                'value' => function ($e) {
                    return ucwords($e->search_engine);
                }
            ),
            array(
                'name' => 'status',
                'header' => 'Status',
                'type' => 'raw',
                'value' => function ($e) {
                    return CHtml::tag('span', array(
                        'title' => String::build('Last Check: {last_check} Next Check: {next_check}', array(
                            'last_check' => Time::toPretty($e->checked_at),
                            'next_check' => $e->period ? date(Time::FORMAT_PRETTY, strtotime($e->checked_at) + $e->period) : 'No autocheck',
                        )),
                    ), ucwords($e->status));
                },
            ),
        ),
    )) ?>
</div>

<script type="text/javascript">

$("#ddlPeriod").change(function () {
    $("#txfPeriod").val($(this).val());
});

</script>