<?php

class TestController extends Controller
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
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','problem','createByFile'),
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
		$this->checkAccess(array('model'=>($model->problem)),'update','problem');		
		$this->render('view',array(
			'model'=>$model,
			'problem'=>$model->problem,		
		
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreateByFile($id)
	{
		$problem=Problem::model()->findByPk((int)$id);
		if($problem===null)
			throw new CHttpException(404,'The requested page does not exist.');		
		$this->checkAccess(array('model'=>$problem),'update','problem');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $model=new TestFiles;
        if(isset($_POST['TestFiles']))
        {
            $model->attributes=$_POST['TestFiles'];
            $model->input_file=CUploadedFile::getInstance($model,'input_file');
            $model->output_file=CUploadedFile::getInstance($model,'output_file');
			$model->problem_id=(int)$id;
			$model->user_id=Yii::app()->user->id;
            if($model->save())
            {
				$this->redirect(array('view','id'=>$model->id));
            }
        }
  
		$this->render('createByFile',array(
			'model'=>$model,
			'problem'=>$problem,		
		));
	}	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id)
	{
		$problem=Problem::model()->findByPk((int)$id);
		if($problem===null)
			throw new CHttpException(404,'The requested page does not exist.');		
		$this->checkAccess(array('model'=>$problem),'update','problem');
		$model=new Test;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Test']))
		{
			$model->attributes=$_POST['Test'];
			$model->problem_id=(int)$id;
			$model->user_id=Yii::app()->user->id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
			'problem'=>$problem,		
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
		$this->checkAccess(array('model'=>$model->problem),'update','problem');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Test']))
		{
			$model->attributes=$_POST['Test'];
			if($model->save())
			{
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'problem'=>$model->problem,
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
	public function actionProblem($id)
	{
		$problem=Problem::model()->findByPk((int)$id);
		$this->checkAccess(array('model'=>$problem),'update','problem');
		
		$problemJudger=$this->newProblemJudger($problem);
		if(Yii::app()->request->getQuery('deleteProblemJudger',null)!==null){
			if($problem->judger!=null)$problem->judger->delete();
			$this->redirect(array('problem','id'=>$problem->id));
			Yii::app()->end;
		}
		
	
		$testDataProvider=new EActiveDataProvider('Test',
			array(
			    'criteria'=>array(
			        'condition'=>'problem_id='.(int)$id,
			        //'order'=>'created DESC',
			        'select'=>array('id,LEFT(input,32) as input,input_size,LEFT(output,32) as output,output_size'),
			    ),
				'pagination'=>false,
			)
		);
		if($problem===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$dataProvider=new CActiveDataProvider('Test');
		$this->render('/test/problem',array(
				'dataProvider'=>$testDataProvider,
				'problemJudger'=>$problemJudger,
				'problem'=>$problem,
			));
	}
	protected function newProblemJudger($problem)
	{
		$problemJudger=$problem->judger;
		if($problemJudger==null)
		{
			$problemJudger=new ProblemJudger;
			$problemJudger->user_id=Yii::app()->user->id;
			$problemJudger->problem_id=$problem->id;
		}
		if(isset($_POST['ajax']) && $_POST['ajax']==='problem-judger-form')
		{
			echo CActiveForm::validate($problemJudger);
			Yii::app()->end();
		}
		if(isset($_POST['ProblemJudger']))
		{
			$problemJudger->attributes=$_POST['ProblemJudger'];
			if($problemJudger->save())
			{
				Yii::app()->user->setFlash('problemJudgerSubmitted','Your special judger is saved.');
				$this->refresh();
			}
		}
		return $problemJudger;
	}		
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Test');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Test('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Test']))
			$model->attributes=$_GET['Test'];

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
		$model=Test::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='test-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
