<?php
/* @var $this DefaultController */
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Edit Email Reporter</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'edit.edit_form', array(
                'emailReporterForm' => $emailReporterForm,
            )) ?>
        </div>
    </div>
</div>

<!--<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Additional info</h3>
        </div>
        <div class="panel-body">
            </?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'index.email_reporter_grid', array(
                'emailReporter' => $emailReporter,
            )) ?>
        </div>
    </div>
</div>-->

<?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'edit/delete_modal', array(
    'emailReporter' => $emailReporter,
)) ?>
