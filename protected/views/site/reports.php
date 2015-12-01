<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Add</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'reports.add_form', array(
                'report' => $report,
            )) ?>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Reports</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'reports.reports_grid', array(
                'report' => $report,
            )) ?>
        </div>
    </div>
</div>

