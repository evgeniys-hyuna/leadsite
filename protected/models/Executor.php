<?php

/**
 * This is the model class for table "lds_executor".
 *
 * The followings are the available columns in table 'lds_executor':
 * @property integer $id
 * @property integer $proxy_id
 * @property integer $keyword_id
 * @property string $status
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * 
 * @property Keyword $keyword Keyword relation
 */
class Executor extends CActiveRecord {
    const STATUS_PENDING = 'pending'; // Awaiting for command
    const STATUS_SEARCHING = 'searching'; // Searching for task (keyword)
    const STATUS_DONE = 'done'; // Ready to begin checking
    const STATUS_CHECKING = 'checking'; // Checking keyword
    const STATUS_COOLDOWN = 'cooldown'; // Awaiting
    const STATUS_ERROR = 'error'; // Error
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_executor';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('proxy_id, keyword_id', 'numerical', 'integerOnly' => true),
            array('status', 'length', 'max' => 9),
            array('message', 'length', 'max' => 512),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, proxy_id, keyword_id, status, message, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'keyword' => array(self::BELONGS_TO, 'Keyword', 'keyword_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'proxy_id' => 'Proxy',
            'keyword_id' => 'Keyword',
            'status' => 'Status',
            'message' => 'Message',
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
        $criteria->compare('proxy_id', $this->proxy_id);
        $criteria->compare('keyword_id', $this->keyword_id);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('message', $this->message, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('deleted_at', $this->deleted_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    public function searchActive() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('proxy_id', $this->proxy_id);
        $criteria->compare('keyword_id', $this->keyword_id);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('message', $this->message, true);
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
     * @return Executor the static model class
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
    
    public function findTask() {
        $criteria = new CDbCriteria();
        $criteria->alias = 'keyword';
        $criteria->addCondition('keyword.status = :status');
        $criteria->addCondition('keyword.deleted_at IS NULL');
        $criteria->params = array(
            ':status' => Keyword::STATUS_PENDING,
        );
        
        $activeSearchEngines = self::getActiveSearchEngines();
        
        for ($i = 0; $i < count($activeSearchEngines); $i++) {
            $parameterName = ':search_engine_' . $i;
            $criteria->addCondition('keyword.search_engine NOT LIKE ' . $parameterName);
            $criteria->params[$parameterName] = '%' . $activeSearchEngines[$i] . '%';
        }
        
        $criteria->order = 'keyword.updated_at ASC';
        $criteria->limit = 1;
        
        if (($keyword = Keyword::model()->find($criteria))) {
            $this->keyword_id = $keyword->id;
            $keyword->setStatus(Keyword::STATUS_TAKEN);
            
            return true;
        }
        
        return false;
    }
    
    public function check() {
        if (!$this->keyword_id) {
            return false;
        }
    }
    
    public function setStatus($status) {
        $this->status = $status;
        
        if (!$this->update()) {
            throw new Exception(print_r($this->getErrors(), true));
        }
    }
    
    public function stop() {
        if ($this->keyword->status == Keyword::STATUS_IN_PROGRESS) {
            $this->keyword->setStatus(Keyword::STATUS_PENDING);
        }
        
        $this->status = Executor::STATUS_DONE;
        $this->deleted_at = date(Time::FORMAT_STANDART);
        $this->update();
    }
    
    public static function getActiveSearchEngines() {
        $currentExecutors = Executor::model()->findAll('deleted_at IS NULL');
        $activeSearchEngines = array();
        
        foreach ($currentExecutors as $e) {
            if (!$e->keyword_id) {
                continue;
            }
            
            $searchEngnie = '';
            
            if ($dotPos = strpos($e->keyword->search_engine, '.')) {
                $searchEngnie = substr($e->keyword->search_engine, 0, $dotPos);
            } else {
                $searchEngnie = $e->keyword->search_engine;
            }
            
            if (!in_array($searchEngnie, $activeSearchEngines)) {
                $activeSearchEngines[] = $searchEngnie;
            }
        }
        
        return $activeSearchEngines;
    }
    
    public function deleteResults() {
        Site::model()->deleteAll('executor_id = :executor_id', array(
            'executor_id' => $this->id,
        ));
        
        $this->delete();
    }

}
