<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Edit Category</h3>
        </div>
        <div class="panel-body">
            <?= $this->renderPartial(Yii::app()->params['siteView'] . 'category.edit_form', array(
                'category' => $category,
            )); ?>
        </div>
    </div>
</div>

<?= $this->renderPartial(Yii::app()->params['siteView'] . 'category.delete_modal', array(
    'category' => $category,
)) ?>
