<?php

class ExperimentController extends Controller
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
				'actions'=>array('index','view','createProblem'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','reports'),
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
		$experiment=$this->loadModel($id);
		$exercise_problem=Yii::app()->user->isGuest?null:$this->newExerciseProblem($experiment);

		$this->render('view',array(
			'model'=>$experiment,
			'exercise_problem'=>$exercise_problem,
		));
	}
	
	/**
	 * Creates a new exercise_problem.
	 * This method attempts to create a new exercise_problem based on the user input.
	 * If the exercise_problem is successfully created, the browser will be redirected
	 * to show the created exercise_problem.
	 * @param Experiment the experiment that the new exercise_problem belongs to
	 * @return ExerciseProblem the exercise_problem instance
	 */
	protected function newExerciseProblem($experiment)
	{
		$exercise_problem=new ExerciseProblem;
		if(isset($_POST['ajax']) && $_POST['ajax']==='exercise-problem-form')
		{
			echo CActiveForm::validate($exercise_problem);
			Yii::app()->end();
		}
		if(isset($_POST['ExerciseProblem']))
		{
			if($experiment->exercise_id==0)
			{
				$exercise=new Exercise;
				$exercise->type_id = Exercise::EXERCISE_TYPE_COURSE;
				$exercise->belong_to_id=$experiment->id;
				if($exercise->save())
				{
					$experiment->exercise_id=$exercise->id;
					$experiment->save();
				}
			}
			$exercise_problem->attributes=$_POST['ExerciseProblem'];
			$problem = Problem::model()->findByPk((int)$exercise_problem->problem_id);
			if($problem==null || !$this->canAccess(array('model'=>$problem),'view','problem'))
			{
				$exercise_problem->addError('problem_id','Not a validate problem id.');
				return $exercise_problem;
			}
			if(ExerciseProblem::model()->find('exercise_id='.$experiment->exercise_id.' and problem_id ='.(int)$exercise_problem->problem_id)!=null)
			{
				$exercise_problem->addError('problem_id','This problem already exists.');
				return $exercise_problem;
			}			
			if($exercise_problem->title==null||strlen(trim($exercise_problem->title))==0)
			{
				$exercise_problem->title=$problem->title;				
			}
			$exercise_problem->exercise_id=$experiment->exercise_id;
			if($exercise_problem->save())
			{
				//if($comment->status==Comment::STATUS_PENDING)
				Yii::app()->user->setFlash('exercise_problemSubmitted','Your problem has been added.');
				$this->refresh();
			}
		}
		return $exercise_problem;
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreateProblem($id)
	{
		$experiment=$this->loadModel($id);		
		$model=new Problem;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Problem']))
		{
			$model->attributes=$_POST['Problem'];
			$model->user_id=Yii::app()->user->id;
			if($model->save())
				$this->redirect(array('view','id'=>$id));
			if(is_int($model->compiler_set))
				$model->compiler_set=UCompilerLookup::values($model->compiler_set);
		}
		
		$this->render('createProblem',array(
			'model'=>$model,
			'experiment'=>$experiment
		));
	}	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Experiment;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Experiment']))
		{
			$model->attributes=$_POST['Experiment'];
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
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Experiment']))
		{
			$model->attributes=$_POST['Experiment'];
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
		$dataProvider=new CActiveDataProvider('Experiment');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}
	/**
	 * Lists all models.
	 */
	public function actionReports($id)
	{
		$model=$this->loadModel($id);		
		$dataProvider=new EActiveDataProvider('GroupUser',
			array(
				'scopes'=>array('common'),
				'criteria' => array(
					'select'=>'t.user_id,{{experiment_reports}}.id as data,{{experiment_reports}}.score as score',
					'join' => 'LEFT JOIN {{experiment_reports}} ON t.user_id = {{experiment_reports}}.user_id and {{experiment_reports}}.experiment_id='.(int)$id,
					'condition'=>'t.group_id='.$model->course->student_group_id.' and t.status='.GroupUser::USER_STATUS_ACCEPTED,
				),
				'pagination'=>array(
					'pageSize'=>30,
				)
			)
		);
		$this->render('reports',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}	

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Experiment('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Experiment']))
			$model->attributes=$_GET['Experiment'];

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
		$model=Experiment::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='experiment-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
