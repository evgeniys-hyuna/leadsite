<?php

/**
 * This is the model class for table "lds_email".
 *
 * The followings are the available columns in table 'lds_email':
 * @property integer $id
 * @property string $address
 * @property string $deleted_at
 *
 * The followings are the available model relations:
 * @property EmailReporter[] $ldsEmailReporters
 */
class Email extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_email';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('address', 'required'),
            array('address', 'length', 'max' => 128),
            array('deleted_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, address, deleted_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'emailReporters' => array(self::MANY_MANY, 'EmailReporter', 'lds_email_reporter_email(email_id, email_reporter_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'address' => 'Address',
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
        $criteria->compare('address', $this->address, true);
        $criteria->compare('deleted_at', $this->deleted_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Email the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
