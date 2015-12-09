<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Name', 'txfName') ?>
        
        <?= CHtml::activeTextField($tag, 'name', array(
            'id' => 'txfName',
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Description (optional)', 'txaDescription') ?>
        
        <?= CHtml::activeTextArea($tag, 'description', array(
            'id' => 'txaDescription',
        )) ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Save', array(
            'class' => 'btn btn-success',
        )) ?>

        <?= CHtml::button('Delete', array(
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'data-target' => '#mdlDeleteTag',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
