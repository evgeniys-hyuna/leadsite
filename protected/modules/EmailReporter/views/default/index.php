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
            <?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'index.add') ?>
        </div>
    </div>
</div>