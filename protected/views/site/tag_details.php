<?php
$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Tag Details</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'tag_details.edit_form', array(
                'tag' => $tag,
            )) ?>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title" title="Shows all tasks for current keyword">Keywords</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'tag_details.keywords_grid', array(
                'tag' => $tag,
            )) ?>
        </div>
    </div>
</div>

<?= $this->renderPartial(Yii::app()->params['siteView'] . 'tag_details.delete_modal', array(
    'tag' => $tag,
)) ?>