<?php

class MultipleChoiceController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/onlinejudge';
	public $contentMenu=null;
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
		$model=$this->loadModel($id);
		$choiceOptionManager=new ChoiceOptionManager();
		$choiceOptionManager->load($model);
		
		$answer_faker=preg_split('/,/',$model->answer);
		foreach($choiceOptionManager->items as $id=>$choiceOption){
			$choiceOption->isAnswer=(in_array($id,$answer_faker))?1:0;
		}		
		
		$this->render('view',array(
			'model'=>$model,
			'choiceOptionManager'=>$choiceOptionManager,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new MultipleChoice;
		$choiceOptionManager=new ChoiceOptionManager();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['MultipleChoice']))
		{
			$model->attributes=$_POST['MultipleChoice'];
			$choiceOptionManager->manage(isset($_POST['ChoiceOption'])?$_POST['ChoiceOption']:array());
			if (isset($_POST['OldValue']))
			{
				if($_POST['OldValue']==0)
				{
					foreach($choiceOptionManager->items as $id=>$choiceOption){
						$choiceOption->isAnswer=($id==$model->answer)?1:0;
					}
				}
				else
				{
					$model->answer="";
					foreach($choiceOptionManager->items as $id=>$choiceOption){
						if($choiceOption->isAnswer)$model->answer=$id;
					}
				}
			}
			if (!isset($_POST['noValidate']))
			{
				if($model->more_than_one_answer)
				{
					$answer=array();
					foreach($choiceOptionManager->items as $id=>$choiceOption){
						if($choiceOption->isAnswer)$answer[]=$id;
					}
					sort($answer);
					$model->answer=join($answer,",");
				}
				$valid=$model->validate();
				$valid=$choiceOptionManager->validate($model) && $valid;
		
				if($valid)
				{
					if($model->save())
					{
						$choiceOptionManager->save($model);
						$answer_faker= preg_split('/,/',$model->answer);
						$answer=array();
						foreach($choiceOptionManager->items as $id=>$choiceOption){
							if(in_array($id,$answer_faker))$answer[]=$choiceOption->id;
						}
						sort($answer);
						
						$model->answer = join($answer,",");
						$model->save();
						$this->redirect(array('view','id'=>$model->id));
					}
				}
			}
		}
		
		$this->render('create',array(
				'model'=>$model,
				'choiceOptionManager'=>$choiceOptionManager,
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
		$choiceOptionManager=new ChoiceOptionManager();
		$choiceOptionManager->load($model);
		
		$answer_faker=preg_split('/,/',$model->answer);
		foreach($choiceOptionManager->items as $id=>$choiceOption){
			$choiceOption->isAnswer=(in_array($id,$answer_faker))?1:0;
		}		
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

			if(isset($_POST['MultipleChoice']))
		{
			$model->attributes=$_POST['MultipleChoice'];
			$choiceOptionManager->manage(isset($_POST['ChoiceOption'])?$_POST['ChoiceOption']:array());
			if (isset($_POST['OldValue']))
			{
				if($_POST['OldValue']==0)
				{
					foreach($choiceOptionManager->items as $id=>$choiceOption){
						$choiceOption->isAnswer=($id==$model->answer)?1:0;
					}
				}
				else
				{
					$model->answer="";
					foreach($choiceOptionManager->items as $id=>$choiceOption){
						if($choiceOption->isAnswer)$model->answer=$id;
					}
				}
			}
			if (!isset($_POST['noValidate']))
			{
				if($model->more_than_one_answer)
				{
					$answer=array();
					foreach($choiceOptionManager->items as $id=>$choiceOption){
						if($choiceOption->isAnswer)$answer[]=$id;
					}
					sort($answer);
					$model->answer=join($answer,",");
				}
				$valid=$model->validate();
				$valid=$choiceOptionManager->validate($model) && $valid;
		
				if($valid)
				{
					if($model->save())
					{
						$choiceOptionManager->save($model);
						$answer_faker=preg_split('/,/',$model->answer);
						$answer=array();
						foreach($choiceOptionManager->items as $id=>$choiceOption){
							if(in_array($id,$answer_faker))$answer[]=$choiceOption->id;
						}
						
						sort($answer);
						$model->answer = join($answer,",");
						$model->save();
						$this->redirect(array('view','id'=>$model->id));
					}
				}
			}
		}
		

		$this->render('update',array(
			'model'=>$model,
			'choiceOptionManager'=>$choiceOptionManager,
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
		$dataProvider=new CActiveDataProvider('MultipleChoice');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new MultipleChoice('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MultipleChoice']))
			$model->attributes=$_GET['MultipleChoice'];

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
		$model=MultipleChoice::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='multiple-choice-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
