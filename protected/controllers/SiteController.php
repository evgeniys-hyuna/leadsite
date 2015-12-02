<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        $keyword = new Keyword();
        $site = new Site();
        
        $this->render('index', array(
            'site' => $site,
            'keyword' => $keyword,
        ));
    }
    
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
//    public function actionContact() {
//        $model = new ContactForm;
//        if (isset($_POST['ContactForm'])) {
//            $model->attributes = $_POST['ContactForm'];
//            if ($model->validate()) {
//                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
//                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
//                $headers = "From: $name <{$model->email}>\r\n" .
//                        "Reply-To: {$model->email}\r\n" .
//                        "MIME-Version: 1.0\r\n" .
//                        "Content-Type: text/plain; charset=UTF-8";
//
//                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
//                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
//                $this->refresh();
//            }
//        }
//        $this->render('contact', array('model' => $model));
//    }

    /**
     * Displays the login page
     */
//    public function actionLogin() {
//        $model = new LoginForm;
//
//        // if it is ajax validation request
//        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
//            echo CActiveForm::validate($model);
//            Yii::app()->end();
//        }
//
//        // collect user input data
//        if (isset($_POST['LoginForm'])) {
//            $model->attributes = $_POST['LoginForm'];
//            // validate user input and redirect to the previous page if valid
//            if ($model->validate() && $model->login())
//                $this->redirect(Yii::app()->user->returnUrl);
//        }
//        // display the login form
//        $this->render('login', array('model' => $model));
//    }

    /**
     * Logs out the current user and redirect to homepage.
     */
//    public function actionLogout() {
//        Yii::app()->user->logout();
//        $this->redirect(Yii::app()->homeUrl);
//    }
    
    public function actionKeywords() {
        $keyword = new Keyword();
        $keywordForm = new KeywordForm();

        if (($postKeywordForm = Yii::app()->request->getParam('KeywordForm'))) {
            $keywords = array();
            
            if (count($keywords = explode(PHP_EOL, $postKeywordForm['keywords'])) <= 1) {
                $keywords = array($postKeywordForm['keywords']);
            }
            
            foreach ($keywords as $k) {
                $k = trim($k);
                
                if (strlen($k) <= 0) {
                    continue;
                }

                if (($existingKeyword = Keyword::model()->findByAttributes(array(
                    'name' => $k,
                    'search_engine' => $postKeywordForm['searchEngine'],
                )))) {
                    $existingKeyword->deleted_at = null;
                    $existingKeyword->period = $postKeywordForm['period'];
                    $existingKeyword->update();

                    continue;
                }

                $newKeyword = new Keyword();
                $newKeyword->name = $k;
                $newKeyword->search_engine = $postKeywordForm['searchEngine'];
                $newKeyword->period = $postKeywordForm['period'];

                if (!$newKeyword->save()) {
                    throw new Exception(print_r($newKeyword->getErrors(), true));
                }
            }
            
            $this->refresh();
        }
        
        $this->render('keywords', array(
            'keyword' => $keyword,
            'keywordForm' => $keywordForm,
        ));
    }
    
    public function actionKeywordAlexa($keywordId, $alexaSearchMethod = Keyword::ALEXA_SEARCH_METHOD_COMBO) {
        $keyword = Keyword::model()->findByPk($keywordId);
        
        $this->render('keyword_alexa', array(
            'keyword' => $keyword,
            'alexaSearchMethod' => $alexaSearchMethod,
        ));
    }
    
    public function actionDev() {
        $executor = new Executor();
        $settings = new Settings();
        
        $this->render('dev', array(
            'executor' => $executor,
            'settings' => $settings,
        ));
    }
    
    public function actionTerminateExecutor($executorId) {
        $executor = Executor::model()->findByPk($executorId);
        $executor->stop();
        
        $this->redirect(Yii::app()->createUrl('site/dev'));
    }
    
    public function actionReports() {
        $report = new Report();
        
        if (($postReport = Yii::app()->request->getParam('Report'))) {
            $report->setAttributes($postReport);
            $emails = array();
            
            if (count($emails = explode(PHP_EOL, $postReport['email'])) <= 1) {
                $emails = array($postReport['email']);
            }
            
            foreach ($emails as $e) {
                if (strlen($e) <= 0) {
                    continue;
                }
                
                $newReport = new Report();
                $newReport->email = $e;
                $newReport->period = $postReport['period'];
                
                if (!$newReport->save()) {
                    throw new Exception(print_r($newReport->getErrors(), true));
                }
            }
            
            $this->refresh();
        }
        
        $this->render('reports', array(
            'report' => $report,
        ));
    }
    
    public function actionReportsSend($reportId) {
        $report = Report::model()->findByPk($reportId);
        $report->send();
        
        $this->redirect(Yii::app()->createUrl('site/reports'));
    }
    
    public function actionReportsDelete($reportId) {
        Report::model()->deleteByPk($reportId);
        
        $this->redirect(Yii::app()->createUrl('site/reports'));
    }
    
    public function actionReportsBrowse() {
        $reports = Report::browse();
        
        $this->render('reports_browse', array(
            'dataProvider' => new CArrayDataProvider($reports, array(
                'sort' => array(
//                    'defaultOrder' => 'name DESC',
                    'attributes' => array(
                        '*',
                    )
                ),
                'pagination' => array(
                    'pageSize' => 50,
                ),
            )),
        ));
    }
    
    public function actionKeywordDetails($keywordId) {
        $keyword = Keyword::model()->findByPk($keywordId);
        
        if (($postKeyword = Yii::app()->request->getParam('Keyword'))) {
            $keyword->setAttributes($postKeyword);
            
            if (!$keyword->save()) {
                throw new Exception(print_r($keyword->getErrors(), true));
            }
            
            $this->redirect(Yii::app()->createUrl('site/keywords'));
        }
        
        $this->render('keyword_details', array(
            'keyword' => $keyword,
        ));
    }
    
    public function actionKeywordDelete($keywordId) {
        $keyword = Keyword::model()->findByPk($keywordId);
        $keyword->deleted_at = date(Time::FORMAT_STANDART);
        $keyword->update();
        
        $this->redirect(Yii::app()->createUrl('site/keywords'));
    }
    
    public function actionIgnoreList() {
        $ignoreList = new IgnoreList();
        
        if (($postIgnoreList = Yii::app()->request->getParam('IgnoreList'))) {
            $ignoreList->setAttributes($postIgnoreList);
            $domains = array();
            
            if (count($domains = explode(PHP_EOL, $postIgnoreList['domain'])) <= 1) {
                $domains = array($postIgnoreList['domain']);
            }
            
            foreach ($domains as $d) {
                $domain = String::rebuildUrl($d, false, false, true, false);
                
                if (strlen($domain) <= 0) {
                    continue;
                }
                
                $newIgnoreList = new IgnoreList();
                $newIgnoreList->domain = $domain;
                
                if (!$newIgnoreList->save()) {
                    throw new Exception(print_r($newIgnoreList->getErrors(), true));
                }
            }
            
            $this->refresh();
        }
        
        $this->render('ignore_list', array(
            'ignoreList' => $ignoreList,
        ));
    }
    
    public function actionIgnoreListDelete($ignoreListId) {
        IgnoreList::model()->deleteByPk($ignoreListId);
        
        $this->redirect(Yii::app()->createUrl('site/ignoreList'));
    }
    
    public function actionDownload($filename) {
        $filepath = Yii::app()->basePath . '/reports/' . $filename;
        
        if (file_exists($filepath)) {
            Yii::app()->request->sendFile($filename, file_get_contents($filepath));
        } else {
            $this->render('site/reports');
        }
    }

}
