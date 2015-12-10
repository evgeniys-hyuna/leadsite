<?= CHtml::beginForm() ?>

<div class="form">
    <div class="row">
        <?= CHtml::label('Email', 'acpEmail') ?>

        <?php
        $this->widget('ext.yii-selectize.YiiSelectize', array(
            'model' => $emailReporterForm,
            'attribute' => 'email',
            'data' => CHtml::listData(Email::model()->findAll(), 'id', 'name'),
            'fullWidth' => false,
            'multiple' => false,
        ));
        ?>
        
        <?= CHtml::error($emailReporterForm, 'email') ?>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'reportTypes') ?>

        <?php
        $this->widget('ext.yii-selectize.YiiSelectize', array(
            'model' => $emailReporterForm,
            'attribute' => 'reportTypes',
            'data' => CHtml::listData(EmailReportType::model()->findAll(), 'id', 'name'),
            'fullWidth' => false,
            'multiple' => true,
        ));
        ?>
        
        <?= CHtml::error($emailReporterForm, 'reportTypes') ?>
    </div>
    
    <div class="row">
        <?= CHtml::activeLabel($emailReporterForm, 'updatePeriodType') ?>

        <?= CHtml::activeDropDownList($emailReporterForm, 'updatePeriodType', CHtml::listData(EmailPeriodType::model()->findAll(), 'id', 'name')) ?>
        
        <?= CHtml::error($emailReporterForm, 'updatePeriodType') ?>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <?= CHtml::activeCheckBoxList($emailReporterForm, 'updatePeriodValueDays', array(
                'Sun', 
                'Mon', 
                'Tue', 
                'Wed', 
                'Thu', 
                'Fri', 
                'Sat',
            ), array(
                'template'=>'<div style="width: 50px; float: left; text-align:center;">{input} {label}</div>',
                'separator' => '',
            )) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <?= CHtml::activeCheckBoxList($emailReporterForm, 'updatePeriodValueDates', range(1, 31), array(
                'template'=>'<div style="width: 50px; float: left; text-align:center;">{input} {label}</div>',
                'separator' => '',
            )) ?>
        </div>
    </div>

    <div class="row">
        <?= CHtml::submitButton('Add', array(
            'class' => 'btn btn-success',
        )) ?>
    </div>
</div>

<?= CHtml::endForm() ?>