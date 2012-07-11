<?php

class PracticeController extends Controller
{
	public $contentMenu=1;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public function   init() {
		$this->registerAssets();
		parent::init();
	}

	private function registerAssets(){

		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->registerJs('webroot.js_plugins.jstree','/jquery.jstree.js');
		$this->registerCssAndJs('webroot.js_plugins.fancybox',
				'/jquery.fancybox-1.3.4.js',
				'/jquery.fancybox-1.3.4.css');
		$this->registerCssAndJs('webroot.js_plugins.jqui1812',
				'/js/jquery-ui-1.8.12.custom.min.js',
				'/css/dark-hive/jquery-ui-1.8.12.custom.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_plugins/json2/json2.js');
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/client_val_form.css','screen');
	}
	public static  function registerCssAndJs($folder, $jsfile, $cssfile) {
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerScriptFile($publishedFolder . $jsfile, CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerCssFile($publishedFolder . $cssfile);
	}
	
	public static function registerCss($folder, $cssfile) {
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerCssFile($publishedFolder .'/'. $cssfile);
		return $publishedFolder .'/'. $cssfile;
	}
	
	public static function registerJs($folder, $jsfile) {
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerScriptFile($publishedFolder .'/'.  $jsfile);
		return $publishedFolder .'/'. $jsfile;
	}
	
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
				'actions'=>array('index','view'),
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

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$quiz=Yii::app()->request->getQuery('quiz',null);
		if($quiz!==null)$quiz=(int)$quiz;
		else {
			$quiz_model= Quiz::model()->findByPk((int)$quiz);
	
			if($quiz_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
	
				
		$model=$this->loadModel($id);
		if($model->chapter)
			$this->course=$model->chapter->course;		
		$this->render('view',array(
			'model'=>$model,
			'quiz'=>$quiz,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id=null)
	{
		$model=new Practice;
		
		

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if( ((int)$id)==0 && $this->getCourse())$id=$this->getCourse()->chapter_id;

		if(isset($_POST['Practice']))
		{
			$model->attributes=$_POST['Practice'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$treeArray=array();
		if($id!=null)
		{
			$nodeRoot=Chapter::model()->findByPk($id);
			$nodeRoot->book;
			if($nodeRoot===null)
				throw new CHttpException(404,'The requested page does not exist.');
			$treeArray[$nodeRoot->id]=str_repeat('&nbsp;',2*($nodeRoot->level-1)).CHtml::encode($nodeRoot->name);
			$tree=$nodeRoot->descendants()->findAll();
			if(!empty($tree))
			{
				foreach ($tree as $node)
				{
					//var_dump($node);
					$treeArray[$node->id]=str_repeat('&nbsp;',2*($node->level-1)).CHtml::encode($node->name);
				}
			}
		}		
		$this->render('create',array(
			'model'=>$model,
			'chapters'=>$treeArray
		));
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

		if(isset($_POST['Practice']))
		{
			$model->attributes=$_POST['Practice'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$treeArray=array();
		$id=$model->chapter_id;
		if($id!=null)
		{
			$nodeRoot=Chapter::model()->findByPk($id);
			$nodeRoot->book;
			if($nodeRoot===null)
				throw new CHttpException(404,'The requested page does not exist.');
			$treeArray[$nodeRoot->id]=str_repeat('&nbsp;',2*($nodeRoot->level-1)).CHtml::encode($nodeRoot->name);
			$tree=$nodeRoot->descendants()->findAll();
			if(!empty($tree))
			{
				foreach ($tree as $node)
				{
					//var_dump($node);
					$treeArray[$node->id]=str_repeat('&nbsp;',2*($node->level-1)).CHtml::encode($node->name);
				}
			}
		}		
		$this->render('create',array(
			'model'=>$model,
			'chapters'=>$treeArray
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
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
		$dataProvider=new CActiveDataProvider('Practice');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Practice('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Practice']))
			$model->attributes=$_GET['Practice'];

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
		$model=Practice::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='practice-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
