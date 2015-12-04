<?php

/**
 * This is the model class for table "lds_settings".
 *
 * The followings are the available columns in table 'lds_settings':
 * @property integer $id
 * @property string $name
 * @property string $value
 */
class Settings extends CActiveRecord {
    const SIMULTANEOUS_EXECUTORS_LIMIT = 'Simultaneous executors limit';
    const EXECUTOR_TASK_SEARCH_COOLDOWN = 'Executor task search cooldown';
    const GOOGLE_SEARCH_COOLDOWN = 'Google search cooldown';
    const EXECUTOR_TASK_SEARCH_LIMIT = 'Executor task search limit';
    const ABUSE_COOLDOWN = 'Abuse cooldown';
    const ALEXA_SEARCH_COOLDOWN = 5;
    const LAST_REPORT_LEADS = 'Last report Leads';
    const LAST_REPORT_ALEXA = 'Last report Alexa';
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_settings';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, value', 'required'),
            array('name, value', 'length', 'max' => 128),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('value', $this->value, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Settings the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public static function getValue($name) {
        if (($settings = Settings::model()->findByAttributes(array(
            'name' => $name,
        )))) {
            return $settings->value;
        }
        
        throw new Exception('Can\'t get settings by name ' . $name);
    }
    
    public static function setValue($name, $value) {
        try {
            Settings::model()->update(array(
                'value' => $value,
            ), 'name = :name', array(
                ':name' => $name,
            ));
        } catch (Exception $ex) {
            $settings = new Settings();
            $settings->name = $name;
            $settings->value = $value;
            
            if (!$settings->save()) {
                throw new Exception('Can\'t save settings. ' . print_r($settings->getErrors(), true));
            }
        }
    }

}
