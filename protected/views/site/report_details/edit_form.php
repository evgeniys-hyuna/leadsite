<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Email', 'txfEmail') ?>

        <?= CHtml::activeTextField($report, 'email', array(
            'id' => 'txfEmail',
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Update Period (in seconds)', 'txfPeriod') ?>

        <?= CHtml::dropDownList('ddlPeriod', '', array(
//            Time::SECONDS_IN_MINUTE => 'Every minute',
//            Time::SECONDS_IN_HOUR => 'Every hour',
            Time::SECONDS_IN_DAY => 'Every day',
            Time::SECONDS_IN_WEEK => 'Every week',
            Time::SECONDS_IN_MONTH => 'Every month',
            Time::SECONDS_IN_YEAR => 'Every year',
        ), array(
            'id' => 'ddlPeriod',
            'prompt' => 'Select',
        )) ?>

        <?= CHtml::activeTextField($report, 'period', array(
            'id' => 'txfPeriod',
            'value' => $report->period,
        )) ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Save', array(
            'class' => 'btn btn-success',
        )) ?>

        <?= CHtml::button('Remove', array(
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'data-target' => '#mdlDeleteReport',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>

<script type="text/javascript">

$("#ddlPeriod").change(function () {
    $("#txfPeriod").val($(this).val());
});

</script>