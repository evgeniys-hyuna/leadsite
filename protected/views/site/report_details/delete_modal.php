<div id="mdlDeleteReport" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Remove Email Reporter</h4>
            </div>
            <div class="modal-body">
                <p>Remove email reporter "<?= $report->email ?>"?</p>
            </div>
            <div class="modal-footer">
                <?= CHtml::link('Remove', Yii::app()->createUrl('site/reportsDelete', array(
                    'reportId' => $report->id,
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
