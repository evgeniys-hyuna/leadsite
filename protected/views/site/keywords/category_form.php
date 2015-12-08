<div class="form">
    <?= CHtml::beginForm() ?>
    
    <div class="row">
        <?= CHtml::label('Category', 'acpCategory') ?>
        
        <?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'model' => $categorySuggestForm,
            'attribute' => 'name',
            'id' => 'country-single',
            'name' => 'country_single',
            'source' => $this->createUrl('site/suggestCategory'),
            'htmlOptions' => array(
                'id' => 'acpCategory',
                'size' => '40',
            ),
        ));
        ?>
        
        <?= Chtml::submitButton('Go', array(
            'class' => 'btn btn-success',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
