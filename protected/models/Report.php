<?php

/**
 * This is the model class for table "lds_report".
 *
 * The followings are the available columns in table 'lds_report':
 * @property integer $id
 * @property string $email
 * @property string $path
 * @property string $last_send_at
 * @property integer $period
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Report extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_report';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, period', 'required'),
            array('email', 'length', 'max' => 1024),
            array('path', 'length', 'max' => 256),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, email, path, last_send_at, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
            array('email, updated_at', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'email' => 'Email',
            'path' => 'Path',
            'last_send_at' => 'Last Send At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('last_send_at', $this->last_send_at, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('deleted_at', $this->deleted_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Report the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->created_at = date(Time::FORMAT_STANDART);
            $this->last_send_at = date(Time::FORMAT_STANDART, time() - $this->period);
        }

        $this->updated_at = date(Time::FORMAT_STANDART);

        return parent::beforeSave();
    }
    
    public function generate() {
        $reportHtml = '';
        $criteria = new CDbCriteria();
        $criteria->alias = 'site';
        $criteria->addCondition('site.deleted_at IS NULL');
        $criteria->group = 'site.domain';
        $criteria->distinct = true;
        $site = Site::model()->findAll($criteria);
        
        $reportHtml .= '<table>';
        $row = '<tr>';
        $row .= '<th>Domain</th>';
        $row .= '<th>Keyword</th>';
        $row .= '<th>Added On</th>';
        $row .= '</tr>';
        $reportHtml .= $row;
        
        foreach ($site as $s) {
            if (IgnoreList::isInList($s->domain)) {
                continue;
            }
            
            $row = '<tr>';
            $row .= '<td>' . $s->domain . '</td>';
            $row .= '<td>' . $s->keyword->name . '</td>';
            $row .= '<td>' . Time::toPretty($s->created_at) . '</td>';
            $row .= '</tr>';
            $reportHtml .= $row;
        }
        
        $reportHtml .= '</table><p><i>Report generated on ' . date(Time::FORMAT_PRETTY) . '</i></p>';
        
        return $reportHtml;
    }
    
    public function send() {
        $reportHtml;
        
        if (!($reportHtml = $this->generate())) {
            return false;
        }
        
        $title = Yii::app()->name . ' Report';
        $body = String::build('<h1>{title}</h1><br /><br />{report}', array(
            'title' => $title,
            'report' => $reportHtml,
        ));

        $headers = 'From: noreply@ad-center.com' . PHP_EOL;
        $headers .= 'Content-type: text/html' . PHP_EOL;

        if (mail($this->email, $title, $body, $headers)) {
            $this->last_send_at = date(Time::FORMAT_STANDART);
            $this->update();

            file_put_contents(Yii::app()->basePath . '/reports/' . date(Time::FORMAT_STANDART) . '.html', $body);
        } else {
            throw new Exception('Can\'t send report to ' . $this->email);
        }
    }
    
    public function isTimeToUpdate() {
        return (strtotime($this->last_send_at) + $this->period) <= time();
    }
    
    public static function browse() {
        $files = scandir(Yii::app()->basePath . '/reports/');
        $reports = array();
        
        foreach ($files as $f) {
            if (in_array($f, array('.', '..'))) {
                continue;
            }
            
            array_push($reports, array(
                'id' => null, // Avoid "unknown index 'id'" error
                'name' => $f,
            ));
        }
        
        return $reports;
    }

}
