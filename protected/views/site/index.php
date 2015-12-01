<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Leads</h3>
    </div>
    <div class="panel-body">
        <?= $this->renderPartial(Yii::app()->params['siteView'] . 'index.leads_grid', array(
            'site' => $site,
        )) ?>
    </div>
</div>
