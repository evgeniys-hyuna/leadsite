<?php

/**
 * This is the model class for table "lds_ignore_list".
 *
 * The followings are the available columns in table 'lds_ignore_list':
 * @property integer $id
 * @property string $domain
 * @property string $created_at
 */
class IgnoreList extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_ignore_list';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('domain', 'required'),
            array('id', 'numerical', 'integerOnly' => true),
            array('domain', 'length', 'max' => 256),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, domain, created_at', 'safe', 'on' => 'search'),
            array('domain', 'safe'),
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
            'domain' => 'Domain',
            'created_at' => 'Created At',
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
        $criteria->compare('domain', $this->domain, true);
        $criteria->compare('created_at', $this->created_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return IgnoreList the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->created_at = date(Time::FORMAT_STANDART);
        }
        
        return parent::beforeSave();
    }
    
    public static function isInList($domain) {
        return IgnoreList::model()->find('domain = :domain', array(
            ':domain' => $domain,
        )) ? true : false;
    }

}
