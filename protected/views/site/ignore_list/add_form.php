<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Domains (each in new line)', 'txaDomain') ?>

        <?= CHtml::activeTextArea($ignoreList, 'domain', array(
            'id' => 'txaDomain',
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
