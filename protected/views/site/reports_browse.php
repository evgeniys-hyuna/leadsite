<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Browse Reports: <?= $currentDirectory ?></h3>
        </div>
        <div class="panel-body">
            <?= CHtml::link('<span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span> Go Up', Yii::app()->createUrl('site/reportsBrowse', array(
                'directory' => substr($currentDirectory, 0, strpos($currentDirectory, pathinfo($currentDirectory, PATHINFO_BASENAME)) -1),
            )), array(
                'class' => 'btn btn-sm btn-default',
            )) ?>
            
            <br /><br />
            
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'reports_browse.reports_grid', array(
                'dataProvider' => $dataProvider,
                'currentDirectory' => $currentDirectory,
            )) ?>
        </div>
    </div>
</div>