<?php

/**
 * This is the model class for table "lds_report".
 *
 * The followings are the available columns in table 'lds_report':
 * @property integer $id
 * @property string $email
 * @property string $path
 * @property string $last_send_at
 * @property integer $period
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Report extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'lds_report';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, period', 'required'),
            array('email', 'length', 'max' => 1024),
            array('path', 'length', 'max' => 256),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, email, path, last_send_at, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
            array('email, updated_at', 'safe'),
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
            'email' => 'Email',
            'path' => 'Path',
            'last_send_at' => 'Last Send At',
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
        $criteria->compare('email', $this->email, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('last_send_at', $this->last_send_at, true);
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
     * @return Report the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->created_at = date(Time::FORMAT_STANDART);
            $this->last_send_at = date(Time::FORMAT_STANDART, time() - $this->period);
        }

        $this->updated_at = date(Time::FORMAT_STANDART);

        return parent::beforeSave();
    }
    
    public function generate($leads = true, $alexa = true) {
        $reportHtml = '';
        
        // Leads
        
        $reportHtml .= '<h3>Leads</h3>';
        
        $criteria = new CDbCriteria();
        $criteria->alias = 'site';
        $criteria->with = array(
            'keyword',
        );
        $criteria->addCondition('site.deleted_at IS NULL');
        $criteria->addCondition('keyword.deleted_at IS NULL');
        $criteria->group = 'site.domain';
        $criteria->distinct = true;
        $site = Site::model()->findAll($criteria);
        
        $reportHtml .= '<table>';
        $row = '<tr>';
        $row .= '<th>Domain</th>';
        $row .= '<th>Keyword</th>';
        $row .= '<th>Added On</th>';
        $row .= '</tr>';
        $reportHtml .= $row;
        
        foreach ($site as $s) {
            if (IgnoreList::isInList($s->domain)) {
                continue;
            }
            
            $row = '<tr>';
            $row .= '<td>' . $s->domain . '</td>';
            $row .= '<td>' . $s->keyword->name . '</td>';
            $row .= '<td>' . Time::toPretty($s->created_at) . '</td>';
            $row .= '</tr>';
            $reportHtml .= $row;
        }
        
        $reportHtml .= '</table>';
        
        // Alexa
        
        $reportHtml .= '<h3>Alexa</h3>';
        
        $keyword = Keyword::model()->findAll();
        
        foreach ($keyword as $k) {
            $reportHtml .= '<p>Keyword: ' . $k->name . '</p><br />';
            $reportHtml .= $k->alexaToHtml(Keyword::ALEXA_SEARCH_METHOD_PARTIAL);
        }
        
        // Stamp
        
        $reportHtml .= '<p><i>Report generated on ' . date(Time::FORMAT_PRETTY) . '</i></p>';
        
        return $reportHtml;
    }
    
    public function send($generateNew = false) {
        $reportHtml;
        
        if ($generateNew) {
            $reportHtml = $this->generate();
        } else {
            $reportHtml = file_get_contents(Settings::getValue(Settings::LAST_REPORT_LEADS));
        }
        
        if (!$reportHtml) {
            return false;
        }
        
        $title = Yii::app()->name . ' Report';
        $body = String::build('<h1>{title}</h1><br /><br />{report}', array(
            'title' => $title,
            'report' => $reportHtml,
        ));

        $headers = 'From: noreply@ad-center.com' . PHP_EOL;
        $headers .= 'Content-type: text/html' . PHP_EOL;

        if ($this->sendEmail($reportHtml, array(
            Settings::getValue(Settings::LAST_REPORT_LEADS),
            Settings::getValue(Settings::LAST_REPORT_ALEXA),
        ))) {
            $this->last_send_at = date(Time::FORMAT_STANDART);
            $this->update();

            if ($generateNew) {
                file_put_contents(Yii::app()->basePath . '/reports/' . date(Time::FORMAT_STANDART) . '.html', $body);
            }
        } else {
            throw new Exception('Can\'t send report to ' . $this->email);
        }
    }
    
    public function isTimeToUpdate() {
        return (strtotime($this->last_send_at) + $this->period) <= time();
    }
    
    public static function browse($directory = false) {
        if (!$directory) {
            $directory = Yii::app()->basePath . '/reports/';
        }
        
        $files = scandir($directory);
        $reports = array();
        
        foreach ($files as $f) {
            if (in_array($f, array('.', '..'))) {
                continue;
            }
            
            array_push($reports, array(
                'id' => null, // Avoid "unknown index 'id'" error
                'name' => $f,
            ));
        }
        
        return $reports;
    }
    
    /**
     * 
     * @param type $body
     * @param type $attachments
     * @return type
     * @deprecated since version 1.0
     */
    private function email($body, $attachments) {
        $uid = md5(uniqid(time()));
        $header = "From: " . Yii::app()->name . " Report <noreply@ad-center.com>\r\n";
//        $header .= "Reply-To: " . $replyto . "\r\n";
//        $header .= "cc : < $cc > \r\n";  // comma saparated emails
//        $header .= "Bcc :  < $bcc >\r\n"; // comma saparated emails
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n";
        $header .= "This is a multi-part message in MIME format.\r\n";
        $header .= "--" . $uid . "\r\n";
        $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
        $header .= "Content-Transfer-Encoding: 7bit\r\n";
        $header .= $body . "\r\n";

        foreach ($attachments as $a) {
            $file = $a;
            $name = basename($file);
            $file_size = filesize($file);
            $handle = fopen($file, "r");
            $content = fread($handle, $file_size);
            fclose($handle);
            $content = chunk_split(base64_encode($content));

            $header .= "--" . $uid . "\r\n";
            $header .= "Content-Type: application/octet-stream; name=\"" . pathinfo($a, PATHINFO_BASENAME) . "\"\r\n"; // use different content types here
            $header .= "Content-Transfer-Encoding: base64\r\n";
            $header .= "Content-Disposition: attachment; filename=\"" . pathinfo($a, PATHINFO_BASENAME) . "\"\r\n";
            $header .= $content . "";
        }
        $header .= "--" . $uid . "--";
        
        return mail($this->email, Yii::app()->name . ' Report', "", $header);
    }
    
    private function sendEmail($body, $attachments = array()) {
        Yii::import('application.extensions.phpmailer.JPhpMailer');
        
        $mail = new JPhpMailer();
        $mail->SetFrom('noreply@ad-center.com');
        $mail->AddAddress($this->email);
        $mail->Subject = Yii::app()->name . ' Report';
        $mail->MsgHTML($body);
        
        foreach ($attachments as $a) {
            $mail->AddAttachment($a);
        }
        
        return $mail->Send();
    }
    
}
