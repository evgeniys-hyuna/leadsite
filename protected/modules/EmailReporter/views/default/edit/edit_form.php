<?= CHtml::beginForm() ?>

<div class="form">
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'email') ?>

        <?php
        $this->widget('ext.yii-selectize.YiiSelectize', array(
            'model' => $emailReporterForm,
            'attribute' => 'email',
            'data' => CHtml::listData(Email::model()->findAll(), 'address', 'address'),
//            'selectedValues' => $emailReporterForm->email,
            'fullWidth' => true,
            'multiple' => true,
        ));
        ?>
        
        <?= CHtml::error($emailReporterForm, 'email') ?>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'reportTypes') ?>

        <div class="row">
            <?= CHtml::activeCheckBoxList($emailReporterForm, 'reportTypes', CHtml::listData(EmailReportType::model()->findAll(), 'name', 'name'), array(
                'template'=>'<div style="width: 50px; float: left; text-align:center;">{input} {label}</div>',
                'separator' => '',
            )) ?>
        </div>

        <?= CHtml::error($emailReporterForm, 'reportTypes') ?>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'updatePeriodType') ?>

        <?= CHtml::activeDropDownList($emailReporterForm, 'updatePeriodType', CHtml::listData(EmailPeriodType::model()->findAll(), 'id', 'name'), array(
            'id' => 'ddlUpdatePeriodType',
            'prompt' => 'select',
        )) ?>
        
        <?= CHtml::error($emailReporterForm, 'updatePeriodType') ?>
    </div>
    
    <div class="row period-pick" style="width: 320px;">
        <div class="row period-days-of-the-week" style="display: none;">
            <div class="col-md-12">
                <?= CHtml::activeCheckBoxList($emailReporterForm, 'updatePeriodValueDays', Time::getDaysOfTheWeek(false, true), array(
                    'template' => '<div style="width: 40px; float: left; text-align:center;">{input} {label}</div>',
                    'separator' => '',
                )) ?>
            </div>
        </div>

        <div class="row period-dates-of-the-months" style="display: none;">
            <div class="col-md-12">
                <?= CHtml::activeCheckBoxList($emailReporterForm, 'updatePeriodValueDates', Time::getDatesOfTheMonth(), array(
                    'template' => '<div style="width: 40px; float: left; text-align:center;">{input} {label}</div>',
                    'separator' => '',
                )) ?>
            </div>
        </div>

        <div class="row period-months-of-the-year" style="display: none;">
            <div class="col-md-12">
                <?= CHtml::activeCheckBoxList($emailReporterForm, 'updatePeriodValueMonths', Time::getMonthsOfTheYear(false, true), array(
                    'template' => '<div style="width: 40px; float: left; text-align:center;">{input} {label}</div>',
                    'separator' => '',
                )) ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'selectionPeriod') ?>
        
        <p><i>Here you can set keyword selection certain period in seconds. 
            Like, to select keywords for last day, use value <b><?= Time::SECONDS_IN_DAY ?></b>. 
            Value <b>0</b> means auto-period from last update.</i></p>

        <?= CHtml::activeTextField($emailReporterForm, 'selectionPeriod') ?>
        
        <?= CHtml::error($emailReporterForm, 'selectionPeriod') ?>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'isUpdatedOnly') ?>

        <?= CHtml::activeCheckBox($emailReporterForm, 'isUpdatedOnly') ?>
        
        <?= CHtml::error($emailReporterForm, 'isUpdatedOnly') ?>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'selectionTags') ?>
        
        <p><i>Blank value will select all keywords.</i></p>

        <?php
        $this->widget('ext.select2.ESelect2', array(
            'model' => $emailReporterForm,
            'attribute' => 'selectionTags',
            'data' => CHtml::listData(Tag::model()->findAll(), 'name', 'name'),
            'htmlOptions' => array(
                'multiple' => 'multiple',
                'style' => 'width: 300px;',
            ),
        ));
        ?>
        
        <?= CHtml::error($emailReporterForm, 'selectionTags') ?>
    </div>

    <div class="row">
        <?= CHtml::submitButton('Edit', array(
            'class' => 'btn btn-success',
        )) ?>
        
        <?= CHtml::button('Delete', array(
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'data-target' => '#mdlDeleteEmailReporter',
        )) ?>
        
        <?= CHtml::ajaxButton('Send Now', Yii::app()->createUrl('/EmailReporter/ajax/sendEmail', array(
            'emailReporterId' => $emailReporter->id,
        )), array(
            'success' => 'js:function (response) { sendEmailSuccess(response); }',
        ), array(
            'class' => 'btn btn-info',
        )) ?>
    </div>
</div>

<?= CHtml::endForm() ?>

<script type="text/javascript">

$("#ddlUpdatePeriodType").change(function () {
    var animationSpeed = 200;
    
    switch ($(this).val()) {
        case "1":
            $(".period-days-of-the-week").show(animationSpeed);
            $(".period-dates-of-the-months").hide(animationSpeed);
            $(".period-months-of-the-year").hide(animationSpeed);
            break;
        case "2":
            $(".period-days-of-the-week").hide(animationSpeed);
            $(".period-dates-of-the-months").show(animationSpeed);
            $(".period-months-of-the-year").hide(animationSpeed);
            break;
        case "3":
            $(".period-days-of-the-week").hide(animationSpeed);
            $(".period-dates-of-the-months").hide(animationSpeed);
            $(".period-months-of-the-year").show(animationSpeed);
            break;
        default:
            $(".period-days-of-the-week").hide(animationSpeed);
            $(".period-dates-of-the-months").hide(animationSpeed);
            $(".period-months-of-the-year").hide(animationSpeed);
            break;
    }
});

function sendEmailSuccess(response) {
    console.log(response);
}

</script>