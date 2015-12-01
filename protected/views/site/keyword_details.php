<?php
$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Keyword Details</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keyword_details.edit_form', array(
                'keyword' => $keyword,
            )) ?>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title" title="Shows all tasks for current keyword">All Results</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'keyword_details.results_grid', array(
                'keyword' => $keyword,
            )) ?>
        </div>
    </div>
</div>

<?= $this->renderPartial(Yii::app()->params['siteView'] . 'keyword_details.delete_modal', array(
    'keyword' => $keyword,
)) ?>

<script type="text/javascript">

$("#ddlPeriod").change(function () {
    $("#txfPeriod").val($(this).val());
});

</script>