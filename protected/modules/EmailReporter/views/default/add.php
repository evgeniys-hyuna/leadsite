<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Add Email Reporter</h3>
    </div>
    <div class="panel-body">
        <?= $this->renderPartial(Yii::app()->params['emailReporterView'] . 'add.add_form', array(
            'emailReporterForm' => $emailReporterForm,
        )) ?>
    </div>
</div>
