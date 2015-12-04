<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Report Details</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'report_details.edit_form', array(
                'report' => $report,
            )) ?>
        </div>
    </div>
</div>

<?= $this->renderPartial(Yii::app()->params['siteView'] . 'report_details.delete_modal', array(
    'report' => $report,
)) ?>
