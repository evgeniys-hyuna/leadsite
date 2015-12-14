<?php

/**
 * This is the model class for table "lds_email_reporter".
 *
 * The followings are the available columns in table 'lds_email_reporter':
 * @property integer $id
 * @property string $last_sent_at
 * @property integer $email_period_id
 * @property string $selection_period
 * @property boolean $is_updated_only
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * The followings are the available model relations:
 * @property EmailPeriod $emailPeriod
 * @property Email[] $ldsEmails
 * @property EmailReportType[] $ldsEmailReportTypes
 * @property Tag[] $ldsTags
 */
class EmailReporter extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_email_reporter';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email_period_id', 'numerical', 'integerOnly' => true),
            array('last_sent_at, email_period_id, selection_period', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, last_sent_at, email_period_id, selection_period, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'emailPeriod' => array(self::BELONGS_TO, 'EmailPeriod', 'email_period_id'),
            'emails' => array(self::MANY_MANY, 'Email', 'lds_email_reporter_email(email_reporter_id, email_id)'),
            'emailReportTypes' => array(self::MANY_MANY, 'EmailReportType', 'lds_email_reporter_report_type(email_reporter_id, email_report_type_id)'),
            'tags' => array(self::MANY_MANY, 'Tag', 'lds_email_reporter_tag(email_reporter_id, tag_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'last_sent_at' => 'Last Sent At',
            'email_period_id' => 'Email Period',
            'selection_period' => 'Selection Period',
            'is_updated_only' => 'Updated Only',
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
        $criteria->compare('last_sent_at', $this->last_sent_at, true);
        $criteria->compare('email_period_id', $this->email_period_id);
        $criteria->compare('selection_period', $this->selection_period, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->addCondition('deleted_at IS NULL');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EmailReporter the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function beforeSave() {
        if ($this->isNewRecord) {
            $date = date(Time::FORMAT_STANDART);
            $this->last_sent_at = $date;
            $this->created_at = $date;
            $this->updated_at = $date;
        }
        
        return parent::beforeSave();
    }
    
    public function getEmails() {
        $result = array();
        
        foreach ($this->emails as $e) {
            $result[] = $e->address;
        }
        
        return $result;
    }
    
    public function getEmailReportTypes() {
        $result = array();
        
        foreach ($this->emailReportTypes as $t) {
            $result[] = $t->name;
        }
        
        return $result;
    }
    
    public function getTags() {
        $result = array();
        
        foreach ($this->tags as $t) {
            $result[] = $t->name;
        }
        
        return $result;
    }

}
