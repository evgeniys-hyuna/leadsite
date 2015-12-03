<?php

/**
 * This is the model class for table "lds_site".
 *
 * The followings are the available columns in table 'lds_site':
 * @property integer $id
 * @property integer $keyword_id
 * @property string $name
 * @property integer $position
 * @property string $domain
 * @property string $link
 * @property integer $executor_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * 
 * @property Keyword $keyword Keyword relation
 */
class Site extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_site';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('keyword_id, name, position, link', 'required'),
            array('keyword_id, position', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 512),
            array('link', 'length', 'max' => 1024),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, keyword_id, name, position, link, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
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
            'executor' => array(self::BELONGS_TO, 'Executor', 'executor_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'keyword_id' => 'Keyword',
            'name' => 'Name',
            'position' => 'Position',
            'link' => 'Link',
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
        $criteria->compare('keyword_id', $this->keyword_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('position', $this->position);
        $criteria->compare('link', $this->link, true);
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
     * @return Site the static model class
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
    
    public static function getNewExecutors($from) {
        $criteria = new CDbCriteria();
        $criteria->alias = 'site';
        $criteria->addCondition('site.created_at >= :from_date');
        $criteria->params = array(
            ':from_date' => $from,
        );
        $criteria->select = 'site.executor_id';
        $criteria->group = 'site.executor_id';
        $criteria->distinct = true;
        
        $site = Site::model()->findAll($criteria);
        $executors = array();
        
        foreach ($site as $s) {
            $executors[] = $s->executor_id;
        }
        
        return $executors;
    }
    
    public function searchLeads() {
        $criteria = new CDbCriteria();
        $criteria->alias = 'site';
        $criteria->with = array(
            'keyword',
        );
        $criteria->addCondition('site.deleted_at IS NULL');
        $criteria->addCondition('keyword.deleted_at IS NULL');
        $criteria->group = 'site.domain';
        $criteria->distinct = true;
        $sites = Site::model()->findAll($criteria);
        $sitesCount = count($sites);
        
        for ($i = 0; $i < $sitesCount; $i++) {
            if (IgnoreList::isInList($sites[$i]->domain)) {
                unset($sites[$i]);
            }
        }
        
        return new CArrayDataProvider($sites, array(
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
    
    public static function deleteLastResults($keywordId) {
        $previousSiteCriteria = new CDbCriteria();
        $previousSiteCriteria->alias = 'site';
        $previousSiteCriteria->addCondition('site.keyword_id = :keyword_id');
        $previousSiteCriteria->params = array(
            ':keyword_id' => $keywordId,
        );
        $previousSiteCriteria->order = 'site.executor_id DESC';
        $previousSiteCriteria->limit = 1;
        
        if (($previousSite = Site::model()->find($previousSiteCriteria))) {
            Site::model()->updateAll(array(
                'deleted_at' => date(Time::FORMAT_STANDART),
            ), 'executor_id = :executor_id', array(
                ':executor_id' => $previousSite->executor_id,
            ));
        }
    }
    
    public static function getLastResults($keywordId) {
        $previousSiteCriteria = new CDbCriteria();
        $previousSiteCriteria->alias = 'site';
        $previousSiteCriteria->addCondition('site.keyword_id = :keyword_id');
        $previousSiteCriteria->params = array(
            ':keyword_id' => $keywordId,
        );
        $previousSiteCriteria->order = 'site.executor_id DESC';
        $previousSiteCriteria->limit = 1;
        
        if (($previousSite = Site::model()->find($previousSiteCriteria))) {
            
            return Site::model()->findAll('executor_id = :executor_id', array(
                ':executor_id' => $previousSite->executor_id,
            ));
        }
        
        return false;
    }
    
    public static function isDifferent($sitesLeft, $sitesRight) {
        $sitesLeftCount = count($sitesLeft);
        
        if ($sitesLeftCount != count($sitesRight)) {
            return true;
        }
        
        for ($i = 0; $i < $sitesLeftCount; $i++) {
            if ($sitesLeft[$i]->domain != $sitesRight[$i]->domain) {
                return true;
            }
        }
        
        return false;
    }

}
