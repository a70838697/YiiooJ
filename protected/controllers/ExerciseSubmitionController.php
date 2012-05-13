<?php

class ExerciseSubmitionController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/course';
	public $contentMenu=null;
	public $prefix="";
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
				'actions'=>array('create','update','delete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin'),
				'roles'=>array('Admin'),
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
		if($model->exercise_id==0)$this->denyAccess();
		$exercise_problem=ExerciseProblem::model()->find('exercise_id='.$model->exercise_id.' and problem_id ='.$model->problem_id );
		if($exercise_problem==null)$this->denyAccess();		
		if($model->exercise->type_id== Exercise::EXERCISE_TYPE_COURSE){
			$this->layout='course';
		}
		
		//$this->checkAccess(array('model'=>$model));		
		if(Yii::app()->request->isAjaxRequest )
		{
			$result=array(
				'ok'=>$model->status!=ULookup::JUDGE_RESULT_PENDING,//you can check time, if timeout, no result
				'status'=>ULookup::$JUDGE_RESULT_MESSAGES[$model->status],
				'result'=>$model->result,
			);
			echo json_encode($result);
			die;
		}
		else{
			$this->render('/exerciseSubmition/view',array(
				'model'=>$model,
				'exercise_problem'=>$exercise_problem,
			));
		}
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
		
		$this->checkAccess(array('model'=>$problem));
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$model=new Submition;
		
		if(isset($_POST['Submition']))
		{
			$model->attributes=$_POST['Submition'];
			$model->problem_id=$problem->id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('/submition/create',array(
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

		
		$this->checkAccess(array('model'=>$model),'update','Submition');
		$exercise_problem=ExerciseProblem::model()->find('exercise_id='.$model->exercise_id.' and problem_id ='.$model->problem_id );
		if($exercise_problem==null)$this->denyAccess();		
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExerciseSubmition']))
		{
			unset($_POST['ExerciseSubmition']['status']);
			$status=null;
			if(isset($_POST['ExerciseSubmition']['source'])&& $_POST['ExerciseSubmition']['source']!=$model->source )
			{
				$status=0;				
			}
			if(isset($_POST['ExerciseSubmition']['compiler_id'])&& $_POST['ExerciseSubmition']['compiler_id']!=$model->compiler_id )
			{
				$status=0;				
			}			
			$model->attributes=$_POST['ExerciseSubmition'];
			//reset the status to pending
			if($status===0)$model->status=0;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('/exerciseSubmition/update',array(
			'model'=>$model,
			'exercise_problem'=>$exercise_problem,
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
			$model=$this->loadModel($id);
			$this->checkAccess(array('model'=>$model));
			$model->visibility=ULookup::RECORD_STATUS_DELETE;
			$model->save();
						
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
		$exercise_id=(int)Yii::app()->request->getQuery('exercise',null);
		$exercise= Exercise::model()->find('id='.$exercise_id);
		if($exercise==null)$this->denyAccess();	
		
		$scopes=array('list');
		if(Yii::app()->request->getQuery('refresh',null)!==null)$scopes[]='recent';
		
		if((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)
			$scopes[]='mine';
		else $scopes[]='public';
		$criteria=new CDbCriteria(array(
	    ));
	    $status=Yii::app()->request->getQuery('status',null);
		if($status!==null && preg_match("/^\d$/",$status))
		{
	    	$criteria->compare('t.status',(int)($status));
		}
	    if($exercise_id>0)$criteria->compare('t.exercise_id',(int)($exercise_id));
		
	    $problem=null;
		if(Yii::app()->request->getQuery('problem',null)!==null)
		{

	    	$criteria->compare('problem_id',(int)(Yii::app()->request->getQuery('problem')));

	    	if($exercise_id>0)$exercise_problem=ExerciseProblem::model()->find('exercise_id='.$exercise_id.' and problem_id ='.(int)(Yii::app()->request->getQuery('problem')) );
			if($exercise_problem==null)$this->denyAccess();		
	    	
		}
		$dataProvider=new EActiveDataProvider('Submition',
			array(
				'criteria'=>$criteria,
				'scopes'=>$scopes,
				'pagination'=>array(
			        	'pageSize'=>30,
			    ),
			)
		);
		$this->render('/exerciseSubmition/index',array(
			'dataProvider'=>$dataProvider,
			'exercise_problem'=>$exercise_problem,
			'exercise'=>$exercise,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Submition('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Submition']))
			$model->attributes=$_GET['Submition'];

		$this->render('/exerciseSubmition/admin',array(
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
		$model=ExerciseSubmition::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='submition-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
