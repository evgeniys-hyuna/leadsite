<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Add Keywords</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keywords.add_keyword_form', array(
                'keywordForm' => $keywordForm,
            )); ?>
        </div>
    </div>
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Add Category</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keywords.add_category_form', array(
                'categoryForm' => $categoryForm,
            )); ?>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Keywords</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keywords.keywords_grid', array(
                'keyword' => $keyword,
            )); ?>
        </div>
    </div>
</div>
