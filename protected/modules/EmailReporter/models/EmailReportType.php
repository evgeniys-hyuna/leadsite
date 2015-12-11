<?php

/**
 * This is the model class for table "lds_email_report_type".
 *
 * The followings are the available columns in table 'lds_email_report_type':
 * @property integer $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property EmailReporter[] $ldsEmailReporters
 */
class EmailReportType extends CActiveRecord {
    const TYPE_LEADS = 1;
    const TYPE_ALEXA = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_email_report_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 64),
            array('name', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'emailReporters' => array(self::MANY_MANY, 'EmailReporter', 'lds_email_reporter_report_type(email_report_type_id, email_reporter_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EmailReportType the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function bindToEmailReporter($emailReporterId) {
        if ($this->isNewRecord) {
            throw new Exception('Can\'t bind unexisting model');
        }
        
        if (!EmailReporter::model()->exists('id = :id', array(
            ':id' => $emailReporterId,
        ))) {
            throw new Exception('EmailReporter does not exists: ' . $emailReporterId);
        }
        
        if (Yii::app()->db->createCommand()->select('*')->from('lds_email_reporter_report_type')->queryScalar(array(
            'email_reporter_id' => $emailReporterId,
            'email_report_type_id' => $this->id,
        ))) {
            return;
        }
        
        Yii::app()->db->createCommand()->insert('lds_email_reporter_report_type', array(
            'email_reporter_id' => $emailReporterId,
            'email_report_type_id' => $this->id,
        ));
    }
    
    public function unbindFromEmailReporter($emailReporterId) {
        if (Yii::app()->db->createCommand()->select('*')->from('lds_email_reporter_report_type')->queryScalar(array(
            'email_reporter_id' => $emailReporterId,
            'email_report_type_id' => $this->id,
        ))) {
            Yii::app()->db->createCommand()->delete('lds_email_reporter_report_type', 'email_reporter_id = :email_reporter_id AND email_report_type_id = :email_report_type_id', array(
                ':email_reporter_id' => $emailReporterId,
                ':email_report_type_id' => $this->id,
            ));
        }
    }

}
