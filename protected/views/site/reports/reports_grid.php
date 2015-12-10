<div id="alertSendNow" class="alert alert-info" style="display: none;">
    Sending report. Please wait...
</div>

<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'grvKeywords',
        'dataProvider' => $report->search(),
        'htmlOptions' => array(),
        'columns' => array(
            array(
                'name' => 'email',
                'header' => 'Email',
                'type' => 'raw',
                'value' => function ($e) {
                    return Chtml::link($e->email, Yii::app()->createUrl('site/reportsDetails', array(
                        'reportId' => $e->id,
                    )));
                },
            ),
            array(
                'name' => 'created_at',
                'header' => 'Added On',
                'filter' => false,
                'value' => function ($e) {
                    return Time::toFormat(Time::FORMAT_DATE_PRETTY, $e->created_at);
                },
            ),
            array(
                'name' => 'last_send_at',
                'header' => 'Last Update',
                'filter' => false,
                'value' => function ($e) {
                    return Time::toFormat(Time::FORMAT_DATE_PRETTY, $e->last_send_at);
                },
            ),
            array(
                'header' => 'Next Update',
                'value' => function ($e) {
                    return date(Time::FORMAT_DATE_PRETTY, (strtotime($e->last_send_at) + $e->period));
                },
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function ($e) {
                    $links = '';

//                    if ((time() - strtotime($e->last_send_at)) > 600) {
                        $links .= CHtml::link('Send Now', Yii::app()->createUrl('site/reportsSend', array(
                            'reportId' => $e->id,
                        )), array(
                            'class' => 'btn-sm btn-info',
                            'onclick' => 'btnSendNowClick(this)',
                        )) . '&nbsp;';
//                    }

                    return $links;
                },
            ),
        ),
    )) ?>
</div>

<script type="text/javascript">

//function btnSendNowClick (sender) {
//    $("#alertSendNow").show(100);
//}

</script>