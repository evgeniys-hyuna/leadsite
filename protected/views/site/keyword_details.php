<?php
$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Keyword Details</h3>
        </div>
        <div class="panel-body">
            <div class="form">
                <?= CHtml::beginForm() ?>

                <div class="row">
                    <?= CHtml::label('Keyword', 'txfName') ?>

                    <?= CHtml::activeTextField($keyword, 'name', array(
                        'id' => 'txfName',
                    )) ?>
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
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title" title="Shows all tasks for current keyword">All Results</h3>
        </div>
        <div class="panel-body">
            <div>
                <?php $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'grvLeads',
                    'dataProvider' => $keyword->allReports(),
                    'columns' => array(
                        array(
                            'name' => 'created_at',
                            'header' => 'Begin',
                            'value' => function ($e) {
                                return Time::toPretty($e->created_at);
                            },
                        ),
                        array(
                            'name' => 'updated_at',
                            'header' => 'End',
                            'value' => function ($e) {
                                return Time::toPretty($e->updated_at);
                            },
                        ),
                        array(
                            'name' => 'status',
                            'header' => 'Status',
                            'value' => function ($e) {
                                return ucwords($e->status);
                            },
                        ),
                        array(
                            'name' => 'message',
                            'header' => 'Message',
                            'value' => function ($e) {
                                return $e->message;
                            },
                        ),
                        array(
                            'header' => 'Results',
                            'type' => 'raw',
                            'value' => function ($e) {
                                $site = Site::model()->findAll('executor_id = :executor_id', array(
                                    ':executor_id' => $e->id,
                                ));
                                $result = '';

                                foreach ($site as $s) {
                                    $result .= "$s->position. $s->domain <br />";
                                }

                                return strlen($result) > 0 ? $result : 'No results';
                            },
                        ),
                    ),
                )) ?>
            </div>
        </div>
    </div>
</div>

<div id="mdlDeleteKeyword" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Keyword</h4>
            </div>
            <div class="modal-body">
                <p>Delete keyword "<?= $keyword->name ?>"?</p>
            </div>
            <div class="modal-footer">
                <?= CHtml::link('Delete', Yii::app()->createUrl('site/keywordDelete', array(
                    'keywordId' => $keyword->id,
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

<script type="text/javascript">

$("#ddlPeriod").change(function () {
    $("#txfPeriod").val($(this).val());
});

</script>