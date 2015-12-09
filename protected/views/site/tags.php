<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Add Tag</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'tags.add_form', array(
                'tag' => $tag,
            )); ?>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Tags</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'tags.tags_grid', array(
                'tag' => $tag,
            )); ?>
        </div>
    </div>
</div>
