<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Email (each in new line)', 'txaEmail') ?>

        <?= CHtml::activeTextArea($report, 'email', array(
            'id' => 'txaEmail',
            'cols' => 35,
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
        <?= Chtml::submitButton('Add', array(
            'class' => 'btn btn-success',
        )) ?>

        <?= CHtml::link('Browse', Yii::app()->createUrl('site/reportsBrowse'), array(
            'class' => 'btn btn-info',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>

<script type="text/javascript">

$("#ddlPeriod").change(function () {
    $("#txfPeriod").val($(this).val());
});

</script>