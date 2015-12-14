<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Add new</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'index.add_form', array(
                'emailReporterForm' => $emailReporterForm,
            )) ?>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Email reporters</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'index.email_reporter_grid', array(
                'emailReporter' => $emailReporter,
            )) ?>
        </div>
    </div>
</div>