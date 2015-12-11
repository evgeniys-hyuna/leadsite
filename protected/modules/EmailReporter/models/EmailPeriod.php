<?php

/**
 * This is the model class for table "lds_email_period".
 *
 * The followings are the available columns in table 'lds_email_period':
 * @property integer $id
 * @property string $name
 * @property int $email_period_type_id
 * @property string $value
 *
 * The followings are the available model relations:
 * @property EmailReporter[] $emailReporters
 * @property EmailPeriodType $emailPeriodType
 */
class EmailPeriod extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_email_period';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'length', 'max' => 45),
            array('value', 'length', 'max' => 512),
            array('name, value', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, value', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'emailReporters' => array(self::HAS_MANY, 'EmailReporter', 'email_period_id'),
            'emailPeriodType' => array(self::HAS_ONE, 'EmailPeriodType', 'email_period_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'value' => 'Value',
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
        $criteria->compare('value', $this->value, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EmailPeriod the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    private function encodeValue() {
        if (is_array($this->value)) {
            $this->value = CJSON::encode($this->value);
        }
    }
    
    private function decodeValue() {
        if (is_string($this->value)) {
            $this->value = CJSON::decode($this->value);
        }
    }
    
    public function beforeValidate() {
        $this->encodeValue();
        
        return parent::beforeValidate();
    }
    
    public function beforeSave() {
        $this->encodeValue();
        
        return parent::beforeSave();
    }
    
    public function afterFind() {
        $this->decodeValue();
        
        return parent::afterFind();
    }

}
