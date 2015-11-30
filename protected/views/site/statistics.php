<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="col-md-6">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Status</h3>
        </div>
        <div class="panel-body">
            <p>Tasks done: <?= Executor::model()->count('status = \'' . Executor::STATUS_DONE . '\'') ?> / <?= Executor::model()->count() ?></p>
            <p>Pending keywords: <?= Keyword::model()->count('status = \'' . Keyword::STATUS_PENDING . '\'') ?></p>
            <p>Alexa Rankings file updated on: <?= date(Time::FORMAT_PRETTY, filemtime(Yii::app()->basePath . '/../uploads/alexa/top-1m.csv')) ?></p>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Settings</h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'grvKeywords',
                'dataProvider' => $settings->search(),
                'filter' => $settings,
                'htmlOptions' => array(),
                'columns' => array(
                    array(
                        'name' => 'name',
                        'header' => 'Parameter',
                        'value' => function ($e) {
                            return $e->name;
                        },
                    ),
                    array(
                        'name' => 'value',
                        'header' => 'Value',
                        'value' => function ($e) {
                            return $e->value;
                        },
                    ),
                ),
            )) ?>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Active Executors</h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'grvKeywords',
                'dataProvider' => $executor->searchActive(),
                'htmlOptions' => array(),
                'columns' => array(
                    array(
                        'name' => 'id',
                        'header' => 'ID',
                        'value' => function ($e) {
                            return $e->id;
                        }
                    ),
                    array(
                        'name' => 'keyword_id',
                        'header' => 'Keyword',
                        'value' => function ($e) {
                            return String::build('{keyword_id}: {keyword}', array(
                                'keyword' => $e->keyword->name,
                                'keyword_id' => $e->keyword_id,
                            ));
                        }
                    ),
                    array(
                        'header' => 'Search Engine',
                        'value' => function ($e) {
                            return ucwords($e->keyword->search_engine);
                        }
                    ),
                    array(
                        'name' => 'status',
                        'header' => 'Status',
                        'value' => function ($e) {
                            return ucwords($e->status);
                        }
                    ),
                    array(
                        'name' => 'message',
                        'header' => 'Message',
                        'value' => function ($e) {
                            return $e->message;
                        }
                    ),
                    array(
                        'name' => 'created_at',
                        'header' => 'Created',
                        'value' => function ($e) {
                            return Time::toPretty($e->created_at);
                        }
                    ),
                    array(
                        'name' => 'updated_at',
                        'header' => 'Updated',
                        'value' => function ($e) {
                            return Time::toPretty($e->updated_at);
                        }
                    ),
                    array(
                        'header' => '',
                        'type' => 'raw',
                        'value' => function ($e) {
                            if ($e->status == Executor::STATUS_ERROR) {
                                return CHtml::link('Terminate', Yii::app()->createUrl('site/terminateExecutor', array(
                                    'executorId' => $e->id,
                                )));
                            }
                        }
                    ),
                ),
            )) ?>
        </div>
    </div>
</div>