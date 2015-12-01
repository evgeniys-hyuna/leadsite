<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grvLeads',
    'dataProvider' => $keyword->allReports(),
    'columns' => array(
        array(
            'name' => 'updated_at',
            'header' => 'Checked On',
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
