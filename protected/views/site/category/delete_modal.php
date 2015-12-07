<div id="mdlDeleteCategory" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Category</h4>
            </div>
            <div class="modal-body">
                <p>Delete category "<?= $category->name ?>"?</p>
            </div>
            <div class="modal-footer">
                <?= CHtml::link('Delete', Yii::app()->createUrl('site/categoryDelete', array(
                    'categoryId' => $category->id,
                )), array(
                    'class' => 'btn btn-danger',
                )) ?>
                
                <?= CHtml::button('Cancel', array(
                    'class' => 'btn btn-default',
                    'data-dismiss' => 'modal',
                )) ?>
            </div>
        </div>
    </div>
</div>
