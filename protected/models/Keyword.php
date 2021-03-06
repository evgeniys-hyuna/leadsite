<?php

/**
 * This is the model class for table "lds_keyword".
 *
 * The followings are the available columns in table 'lds_keyword':
 * @property integer $id
 * @property string $name
 * @property string $search_engine
 * @property string $status
 * @property integer $period
 * @property string $checked_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Keyword extends CActiveRecord {
    const STATUS_PENDING = 'pending'; // awaiting for executor
    const STATUS_TAKEN = 'taken'; // awaiting for check
    const STATUS_IN_PROGRESS = 'in_progress'; // checking
    const STATUS_CHECKED = 'checked'; // awaiting to be reported
    const STATUS_FULFILLED = 'fulfilled'; // all done
    const SEARCH_ENGINE_GOOGLE = 'google';
    const SEARCH_ENGINE_GOOGLE_IT = 'google.it';
    const SEARCH_ENGINE_GOOGLE_ES = 'google.es';
    const SEARCH_ENGINE_GOOGLE_FR = 'google.fr';
    const SEARCH_ENGINE_BING = 'bing';
    const SEARCH_ENGINE_YAHOO = 'yahoo';

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_keyword';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 512),
            array('status', 'length', 'max' => 11),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, status, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
            array('name, search_engine, status, period, updated_at', 'safe'),
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
            'status' => 'Status',
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
        $criteria->compare('status', $this->status, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->addCondition('deleted_at IS NULL');
        $criteria->order = 'created_at DESC';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Keyword the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_PENDING;
            $this->created_at = date(Time::FORMAT_STANDART);
        }
        
        $this->updated_at = date(Time::FORMAT_STANDART);
        
        return parent::beforeSave();
    }
    
    public function setStatus($status) {
        $this->status = $status;
        
        if ($status == self::STATUS_CHECKED) {
            $this->checked_at = date(Time::FORMAT_STANDART);
        }
        
        if (!$this->update()) {
            throw new Exception(print_r($this->getErrors(), true));
        }
    }
    
    public function buildLeads() {
        $leads = array();
        $keywords = Keyword::model()->findAll('deleted_at IS NULL AND status = \'' . Keyword::STATUS_CHECKED . '\'');
        
        foreach ($keywords as $k) {
            $domain = '';
            
            $executorCriteria = new CDbCriteria();
            $executorCriteria->addCondition('keyword_id = :keyword_id');
            $executorCriteria->addCondition('status = :status');
            $executorCriteria->params = array(
                ':keyword_id' => $k->id,
                ':status' => Executor::STATUS_DONE,
            );
            $executorCriteria->order = 'id DESC';
            
            $executor = Executor::model()->find($executorCriteria);
            
            if ($executor) {
                $site = Site::model()->findAll('executor_id = :executor_id', array(
                    ':executor_id' => $executor->id,
                ));
                
                $domain .= $site[0]->domain;
            } else {
                $domain = 'This task has never been checked';
            }
            
            array_push($leads, array(
                'id' => $k->id,
                'keyword' => $k->name,
                'domain' => strlen($domain) > 0 ? $domain : 'No results ' . $executor->id,
                'search_engine' => $k->search_engine,
                'updated' => $executor->deleted_at,
            ));
        }
        
        return new CArrayDataProvider($leads, array(
            'sort' => array(
//                'defaultOrder' => 'name DESC',
                'attributes' => array(
                    '*',
                )
            ),
            'pagination' => array(
                'pageSize' => 50,
            ),
        ));
    }
    
    public function allReports() {
        $criteria = new CDbCriteria();
        $criteria->compare('keyword_id', $this->id);
        $criteria->order = 'id DESC';

        return new CActiveDataProvider('Executor', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ));
    }
    
}
