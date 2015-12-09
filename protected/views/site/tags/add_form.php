<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Name', 'txfName') ?>
        
        <?= CHtml::activeTextField($tag, 'name') ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Add', array(
            'class' => 'btn btn-success',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
