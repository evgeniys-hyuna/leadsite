<div id="alertSendNow" class="alert alert-info" style="display: none;">
    Report generating may take a while. Please, be patient...
</div>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvKeywords',
        'dataProvider' => $report->search(),
        'filter' => $report,
        'htmlOptions' => array(),
        'columns' => array(
            array(
                'name' => 'email',
                'header' => 'Email',
                'type' => 'raw',
                'value' => function ($e) {
                    return String::build('<span title="Added on {created_at}">{email}</span>', array(
                        'email' => $e->email,
                        'created_at' => Time::toPretty($e->created_at),
                    ));
                },
            ),
            array(
                'name' => 'last_send_at',
                'header' => 'Last Update',
                'filter' => false,
                'value' => function ($e) {
                    return Time::toPretty($e->last_send_at);
                },
            ),
            array(
                'header' => 'Next Update',
                'value' => function ($e) {
                    return date(Time::FORMAT_PRETTY, (strtotime($e->last_send_at) + $e->period));
                },
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function ($e) {
                    $links = '';

                    if ((time() - strtotime($e->last_send_at)) > 600) {
                        $links .= CHtml::link('Send Now', Yii::app()->createUrl('site/reportsSend', array(
                            'reportId' => $e->id,
                        )), array(
                            'class' => 'btn-sm btn-info',
                            'onclick' => 'btnSendNowClick(this)',
                        )) . '&nbsp;';
                    }

                    $links .= Chtml::link('Delete', Yii::app()->createUrl('site/reportsDelete', array(
                        'reportId' => $e->id,
                    )), array(
                        'class' => 'btn-sm btn-danger',
                    ));

                    return $links;
                },
            ),
        ),
    )) ?>
</div>

<script type="text/javascript">

function btnSendNowClick (sender) {
    $("#alertSendNow").show(300);
}

</script>