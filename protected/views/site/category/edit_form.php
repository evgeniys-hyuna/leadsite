<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Name', 'txfName') ?>
        
        <?= CHtml::activeTelField($category, 'name', array(
            'id' => 'txfName',
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Description (optional)', 'txaDescription') ?>

        <?= CHtml::activeTextArea($category, 'description', array(
            'id' => 'txaDescription',
            'cols' => 35,
            'rows' => 5,
        )) ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Save', array(
            'class' => 'btn btn-success',
        )) ?>

        <?= CHtml::button('Delete', array(
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'data-target' => '#mdlDeleteCategory',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
