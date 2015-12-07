<div class="form">
    <?= CHtml::beginForm() ?>
    
    <div class="row">
        <?= CHtml::label('Name', 'txfName') ?>
        
        <?= CHtml::activeTelField($categoryForm, 'name', array(
            'id' => 'txfName',
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Description (optional)', 'txaDescription') ?>

        <?= CHtml::activeTextArea($categoryForm, 'description', array(
            'id' => 'txaDescription',
            'cols' => 35,
            'rows' => 5,
        )) ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Add', array(
            'class' => 'btn btn-success',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
