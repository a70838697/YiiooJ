<?php

class ExperimentReportController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/course';
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
				'actions'=>array('index','view','report'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','write'),
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
		
		if(isset($_POST['ExperimentReport']))
		{
			$model->attributes=$_POST['ExperimentReport'];
			$model->save();
		}
		
		$this->render('view',array(
			'model'=>$model,
		));
	}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionReport($id)
	{
		$this->renderPartial('report',array(
			'model'=>$this->loadModel($id),
		));
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ExperimentReport;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$model->user_id=Yii::app()->user->id;
		
		if(isset($_POST['ExperimentReport']))
		{
			$model->attributes=$_POST['ExperimentReport'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionWrite($id)
	{
		if(!UUserIdentity::isStudent())
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}		
		$experiment=Experiment::model()->findByPk((int)$id);
		if($experiment===null)
			throw new CHttpException(404,'The requested page does not exist.');		
		$model=ExperimentReport::model()->find(array(
		    'condition'=>'experiment_id=:experimentID and user_id='.Yii::app()->user->id,
		    'params'=>array(':experimentID'=>(int)$id),
		));
		
		if($model==null){
			if($experiment->isTimeOut())
			{
				throw new CHttpException(403,'Your submition is beyond the deadline ' .$experiment->begin."~".$experiment->end.'.');
			}
			$model=new ExperimentReport;
			$model->user_id=Yii::app()->user->id;
			$model->experiment_id=(int)$id;
		}
		
		if($model->user_id!=Yii::app()->user->id)
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExperimentReport']))
		{
			$model->attributes=$_POST['ExperimentReport'];
			if(Yii::app()->request->getQuery('preview',null)!==null)
			{
				$this->renderPartial('report',array(
					'model'=>$model));
				die;
			}else{
				if($model->save())
					$this->redirect(array('view','id'=>$model->id));
			}
		}

		if($experiment->isTimeOut())
		{
			$this->render('view',array(
				'model'=>$model,
				'experiment'=>$experiment
			));			
		}
		else
		{
			$this->render('update',array(
				'model'=>$model,
				'experiment'=>$experiment
			));
		}
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

		if($model->experiment->isTimeOut() && UUserIdentity::isStudent())
		{
			throw new CHttpException(403,'Your operation is beyond the deadline ' .$model->experiment->begin."~".$model->experiment->end.'.');
		}
		if(isset($_POST['ExperimentReport']))
		{
			$model->attributes=$_POST['ExperimentReport'];
			if(Yii::app()->request->getQuery('preview',null)!==null)
			{
				$this->renderPartial('report',array(
					'model'=>$model));
				die;
			}else{
				if($model->save())
					$this->redirect(array('view','id'=>$model->id));
			}			
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
		$dataProvider=new CActiveDataProvider('ExperimentReport');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ExperimentReport('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExperimentReport']))
			$model->attributes=$_GET['ExperimentReport'];

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
		$model=ExperimentReport::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='experiment-report-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
