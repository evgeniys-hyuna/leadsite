<?php

/**
 * This is the model class for table "lds_tag".
 *
 * The followings are the available columns in table 'lds_tag':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * The followings are the available model relations:
 * @property Keyword[] $ldsKeywords
 */
class Tag extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_tag';
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
            array('description', 'length', 'max' => 512),
            array('name, description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, description, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'keywords' => array(self::MANY_MANY, 'Keyword', 'lds_keyword_tag(tag_id, keyword_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
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
     * @return Tag the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->created_at = date(Time::FORMAT_STANDART);
        }
        
        $this->updated_at = date(Time::FORMAT_STANDART);
        
        return parent::beforeSave();
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
        
        if (Yii::app()->db->createCommand()->select('*')->from('lds_email_reporter_tag')->queryScalar(array(
            'email_reporter_id' => $emailReporterId,
            'tag_id' => $this->id,
        ))) {
            return;
        }
        
        Yii::app()->db->createCommand()->insert('lds_email_reporter_tag', array(
            'email_reporter_id' => $emailReporterId,
            'tag_id' => $this->id,
        ));
    }
    
    public function unbindFromEmailReporter($emailReporterId) {
        if (Yii::app()->db->createCommand()->select('*')->from('lds_email_reporter_tag')->queryScalar(array(
            'email_reporter_id' => $emailReporterId,
            'tag_id' => $this->id,
        ))) {
            Yii::app()->db->createCommand()->delete('lds_email_reporter_tag', 'email_reporter_id = :email_reporter_id AND tag_id = :tag_id', array(
                ':email_reporter_id' => $emailReporterId,
                ':tag_id' => $this->id,
            ));
        }
    }
    
}
