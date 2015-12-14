<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grvLeads',
    'dataProvider' => $emailReporter->search(),
    'columns' => array(
        array(
            'name' => 'email',
            'header' => 'Email',
            'type' => 'raw',
            'value' => function ($e) {
                return implode(', ', $e->getEmails());
            }
        ),
        array(
            'header' => 'Report Type',
            'type' => 'raw',
            'value' => function ($e) {
                return implode(', ', $e->getEmailReportTypes());
            },
        ),
        array(
            'header' => 'Update By',
            'type' => 'raw',
            'value' => function ($e) {
                $period = array();
                
                switch ($e->emailPeriod->email_period_type_id) {
                    case EmailPeriodType::TYPE_DAYS_OF_THE_WEEK:
                        $period = Time::getDaysOfTheWeek(false, true);
                        break;
                    case EmailPeriodType::TYPE_DATES_OF_THE_MONTH:
                        $period = Time::getDatesOfTheMonth();
                        break;
                    case EmailPeriodType::TYPE_MONTHS_OF_THE_YEAR:
                        $period = Time::getMonthsOfTheYear(false, true);
                        break;
                }
                
                $period = array_intersect_key($period, array_flip($e->emailPeriod->value));
                
                return implode(', ', $period);
            },
        ),
        array(
            'header' => 'Selection Tags',
            'value' => function ($e) {
                return implode(', ', $e->getTags());
            },
        ),
        array(
            'name' => 'created_at',
            'header' => 'Added On',
            'type' => 'raw',
            'value' => function ($e) {
                return Time::toFormat(Time::FORMAT_DATE_PRETTY, $e->created_at);
            }
        ),
        array(
            'header' => 'Action',
            'type' => 'raw',
            'value' => function ($e) {
                $actions = '';
                
                $actions .= CHtml::link('Edit', Yii::app()->createUrl('/EmailReporter/default/edit', array(
                    'emailReporterId' => $e->id,
                )), array(
                    'class' => 'btn btn-sm btn-primary',
                ));
            
                return $actions;
            },
        ),
    ),
)) ?>
