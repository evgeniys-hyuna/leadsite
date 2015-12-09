<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Keywords (each in new line)', 'txaKeywords') ?>

        <?= CHtml::activeTextArea($keywordForm, 'keywords', array(
            'id' => 'txaKeywords',
            'cols' => 35,
            'rows' => 5,
        )) ?>
    </div>
    
    <div class="row">
        <?= CHtml::label('Tags', 'slzCategory') ?>
        
        <?php
        $this->widget('ext.yii-selectize.YiiSelectize', array(
            'model' => $keywordForm,
            'attribute' => 'category',
            'data' => CHtml::listData(Tag::model()->findAll(), 'name', 'name'),
            'fullWidth' => true,
            'htmlOptions' => array(
                'id' => 'slzCategory',
            ),
            'multiple' => true,
        ));
        ?>
    </div>

    <div class="row">
        <?= CHtml::label('Autocheck (in seconds)', 'txfPeriod') ?>

        <?= CHtml::dropDownList('ddlPeriod', Time::SECONDS_IN_WEEK, array(
//            Time::SECONDS_IN_MINUTE => 'Every minute',
//            Time::SECONDS_IN_HOUR => 'Every hour',
            Time::SECONDS_IN_DAY => 'Every day',
            Time::SECONDS_IN_WEEK => 'Every week',
            Time::SECONDS_IN_MONTH => 'Every month',
            Time::SECONDS_IN_YEAR => 'Every year',
        ), array(
            'id' => 'ddlPeriod',
        )) ?>

        <?= CHtml::activeTextField($keywordForm, 'period', array(
            'id' => 'txfPeriod',
            'value' => Time::SECONDS_IN_WEEK,
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Search Engine', 'ddlSearchEngine') ?>

        <?= CHtml::activeDropDownList($keywordForm, 'searchEngine', array(
            Keyword::SEARCH_ENGINE_GOOGLE => Keyword::SEARCH_ENGINE_GOOGLE,
            Keyword::SEARCH_ENGINE_GOOGLE_ES => Keyword::SEARCH_ENGINE_GOOGLE_ES,
            Keyword::SEARCH_ENGINE_GOOGLE_IT => Keyword::SEARCH_ENGINE_GOOGLE_IT,
            Keyword::SEARCH_ENGINE_GOOGLE_FR => Keyword::SEARCH_ENGINE_GOOGLE_FR,
            Keyword::SEARCH_ENGINE_BING => Keyword::SEARCH_ENGINE_BING,
            Keyword::SEARCH_ENGINE_YAHOO => Keyword::SEARCH_ENGINE_YAHOO,
        ), array(
            'id' => 'ddlSearchEngine',
        )) ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Add', array(
            'class' => 'btn btn-success',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>

<script type="text/javascript">

$("#ddlPeriod").change(function () {
    $("#txfPeriod").val($(this).val());
});

</script>