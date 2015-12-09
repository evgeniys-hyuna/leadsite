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
        <?= Chtml::submitButton('Add', array(
            'class' => 'btn btn-success',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
