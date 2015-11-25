<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="form">
    <?= CHtml::beginForm() ?>
    
    <div class="row">
        <?= CHtml::label('Email (each in new line)', 'txaEmail') ?>
        
        <?= CHtml::activeTextArea($report, 'email', array(
            'id' => 'txaEmail',
            'cols' => 40,
            'rows' => 5,
        )) ?>
    </div>
    
    <div class="row">
        <?= CHtml::label('Period (in seconds)', 'txfPeriod') ?>
        
        <?= CHtml::dropDownList('ddlPeriod', Time::SECONDS_IN_WEEK, array(
//            Time::SECONDS_IN_MINUTE => 'Every minute',
//            Time::SECONDS_IN_HOUR => 'Every hour',
            Time::SECONDS_IN_DAY => 'Every day',
            Time::SECONDS_IN_WEEK => 'Every week',
            Time::SECONDS_IN_MONTH => 'Every month',
            Time::SECONDS_IN_YEAR => 'Every year',
        ), array(
            'id' => 'ddlPeriod',
        )) ?>
        
        <?= CHtml::activeTextField($report, 'period', array(
            'id' => 'txfPeriod',
            'value' => Time::SECONDS_IN_WEEK,
        )) ?>
    </div>
    
    <div class="row">
        <?= Chtml::submitButton('Add') ?>
    </div>
    
    <?= CHtml::endForm() ?>
</div>

<p><a href="<?= Yii::app()->createUrl('site/reportsBrowse') ?>">Browse Reports</a></p>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvKeywords',
        'dataProvider' => $report->search(),
        'filter' => $report,
        'htmlOptions' => array(),
        'columns' => array(
            array(
                'name' => 'email',
                'header' => 'Email',
                'type' => 'raw',
                'value' => function ($e) {
                    return String::build('<span title="Added on {created_at}">{email}</span>', array(
                        'email' => $e->email,
                        'created_at' => Time::toPretty($e->created_at),
                    ));
                },
            ),
            array(
                'name' => 'last_send_at',
                'header' => 'Last Update',
                'filter' => false,
                'value' => function ($e) {
                    return Time::toPretty($e->last_send_at);
                },
            ),
            array(
                'header' => 'Next Update',
                'value' => function ($e) {
                    return date(Time::FORMAT_PRETTY, (strtotime($e->last_send_at) + $e->period));
                },
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function ($e) {
                    $links = '';
                    
                    if ((time() - strtotime($e->last_send_at)) > 600) {
                        $links .= CHtml::link('Send Now', Yii::app()->createUrl('site/reportsSend', array(
                            'reportId' => $e->id,
                        ))) . '&nbsp;';
                    }
                    
                    $links .= Chtml::link('Delete', Yii::app()->createUrl('site/reportsDelete', array(
                        'reportId' => $e->id,
                    )));
                    
                    return $links;
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