<?php

class UploadController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','download'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionDownload($id)
	{
		$model=$this->loadModel($id);
		// Import library ( assuming in protected.extensions.helpers)
		Yii::import('ext.helpers.EDownloadHelper');
		 
		// assumming I have a folder named docs under my webroot folder
		// and a file to be downloaded 'myhugefile.zip'
		EDownloadHelper::download($model->location,$model->filename);	
		Yii::app()->end();
	}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
        Yii::import("ext.EAjaxUpload.qqFileUploader");
 
        $folder='upload/';// folder for uploaded files
        $type=Yii::app()->request->getQuery('type',null);
        $allowedExtensions = array("jpg","jpeg","png","gif","txt","rar","zip","chm","ppt","pdf","doc","7z");//array("jpg","jpeg","gif","exe","mov" and etc...
        $filefieldname="qqfile";
        if($type!==null)
        {
        	if($type=="wiki")
        	{
		       	$folder.="wiki/";
        		$filefieldname="qqfile";
        	}
        	if($type=="report"){
				$folder.="report/".Yii::app()->request->getQuery('course','')."/";
        		$filefieldname="filedata";
        	}
            if($type=="problem"){
				$folder.="problem/";
        		$filefieldname="filedata";
        	}        	
			if (!is_dir($folder) ){
			   @mkdir($folder);
			}
        }
        $sizeLimit = 20 * 1024 * 1024;// maximum file size in bytes
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit,$filefieldname);
        $result = $uploader->handleUpload($folder);
        if(isset($result['success'])&& $result['success'])
        {
	        $model=new Upload;
	        $model->filename=$result['oldfilename']. '.' . $result['ext'];
	      	$model->location= $folder . $result['filename'];
	      	$model->access=0;
	      	$model->revision=1;
	      	$model->filesize=$result['size'];
	      	$model->user_id=Yii::app()->user->id;
			$model->ip=ip2long(Yii::app()->request->userHostAddress);
        
			if($model->save())
			{
				$result['fileid']=$model->id;
			}
			else 
			{
				$result['success']=false;
				$result['error']='Can not save to database!';
			}
        }
        if($type=="report"||$type=="problem"){
        	$result['msg']="";
	        if(isset($result['error'])){
	        	$result['err']=$result['error'];
	        }
	        else if($result['success'])
	        {
	        	$result['err']="";
	        	$result['msg']="!".UCHtml::url("upload/download/").$result['fileid'];
	        }
	        else $result['err']="error in uploading";
        }
        $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;// it's array
        Yii::app()->end();
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Upload']))
		{
			$model->attributes=$_POST['Upload'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Upload');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Upload('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Upload']))
			$model->attributes=$_GET['Upload'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Upload::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='upload-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
