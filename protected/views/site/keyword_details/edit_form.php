<div class="form">
    <?= CHtml::beginForm() ?>

    <div class="row">
        <?= CHtml::label('Keyword', 'txfName') ?>

        <?= CHtml::activeTextField($keyword, 'name', array(
            'id' => 'txfName',
        )) ?>
    </div>
    
    <div class="row">
        <?= CHtml::label('Tags', 'slzCategory') ?>
        
        <?php
        $this->widget('ext.yii-selectize.YiiSelectize', array(
            'name' => 'KeywordTags',
            'data' => CHtml::listData(Tag::model()->findAll(), 'name', 'name'),
            'selectedValues' => CHtml::listData($keyword->tags, 'name', 'name'),
            'fullWidth' => true,
            'htmlOptions' => array(
                'id' => 'slzCategory',
            ),
            'multiple' => true,
        ));
        ?>
    </div>

    <div class="row">
        <?= CHtml::label('Status', 'ddlStatus') ?>

        <?= CHtml::activeDropDownList($keyword, 'status', array(
            Keyword::STATUS_PENDING => Keyword::STATUS_PENDING,
            Keyword::STATUS_CHECKED => Keyword::STATUS_CHECKED,
//            Keyword::STATUS_FULFILLED => Keyword::STATUS_FULFILLED,
        ), array(
            'id' => 'txfName',
            'selected' => $keyword->status,
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Autocheck (in seconds)', 'txfPeriod') ?>

<!--        </?= CHtml::dropDownList('ddlPeriod', '', array(
//            Time::SECONDS_IN_MINUTE => 'Every minute',
            Time::SECONDS_IN_HOUR => 'Every hour',
            Time::SECONDS_IN_DAY => 'Every day',
            Time::SECONDS_IN_WEEK => 'Every week',
            Time::SECONDS_IN_MONTH => 'Every month',
            Time::SECONDS_IN_YEAR => 'Every year',
        ), array(
            'id' => 'ddlPeriod',
            'prompt' => 'Select',
        )) ?>-->

        <?= CHtml::activeTextField($keyword, 'period', array(
            'id' => 'txfPeriod',
            'value' => $keyword->period,
        )) ?>
    </div>

    <div class="row">
        <?= CHtml::label('Search Engine', 'ddlSearchEngine') ?>

        <?= CHtml::activeDropDownList($keyword, 'search_engine', array(
            Keyword::SEARCH_ENGINE_BING => Keyword::SEARCH_ENGINE_BING,
            Keyword::SEARCH_ENGINE_YAHOO => Keyword::SEARCH_ENGINE_YAHOO,
            Keyword::SEARCH_ENGINE_GOOGLE => Keyword::SEARCH_ENGINE_GOOGLE,
            Keyword::SEARCH_ENGINE_GOOGLE_ES => Keyword::SEARCH_ENGINE_GOOGLE_ES,
            Keyword::SEARCH_ENGINE_GOOGLE_IT => Keyword::SEARCH_ENGINE_GOOGLE_IT,
            Keyword::SEARCH_ENGINE_GOOGLE_FR => Keyword::SEARCH_ENGINE_GOOGLE_FR,
        ), array(
            'id' => 'ddlSearchEngine',
            'selected' => Keyword::SEARCH_ENGINE_GOOGLE_IT,
        )) ?>
    </div>

    <div class="row">
        <?= Chtml::submitButton('Save', array(
            'class' => 'btn btn-success',
        )) ?>

        <?= CHtml::button('Delete', array(
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'data-target' => '#mdlDeleteKeyword',
        )) ?>

        <?= CHtml::link('ALEXA Top 1m', Yii::app()->createUrl('site/keywordAlexa', array(
            'keywordId' => $keyword->id,
        )), array(
            'class' => 'btn btn-info',
        )) ?>
    </div>

    <?= CHtml::endForm() ?>
</div>
