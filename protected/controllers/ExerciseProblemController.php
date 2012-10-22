<?php

class ExerciseProblemController extends Controller
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
				'actions'=>array('index','view','addProblemToExperiment'),
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
	 * Creates a new submition.
	 * This method attempts to create a new submition based on the user input.
	 * If the submition is successfully created, the browser will be redirected
	 * to show the created submition.
	 * @param Problem the problem that the new submition belongs to
	 * @return Submition the submition instance
	 */
	protected function newSubmition($exerciseProblem)
	{
		$submition=new Submition;
		if(isset($_GET['ajax']) && $_POST['ajax']==='submition-form')
		{
			echo CActiveForm::validate($submition);
			Yii::app()->end();
		}
		if(isset($_POST['Submition']))
		{
			$submition->attributes=$_POST['Submition'];
			$submition->user_id=Yii::app()->user->id;
			$submition->exercise_id=$exerciseProblem->exercise_id;
			$submition->problem_id=$exerciseProblem->problem->id;
			if($submition->save())
			{
				//if($comment->status==Comment::STATUS_PENDING)
				//Yii::app()->user->setFlash('submitionSubmitted','Thank you for your submition. Your submition will be judged.');
				//$this->refresh();
				$this->redirect(array('/exerciseSubmition/view','id'=>$submition->id));				
			}
		}
		return $submition;
	}
	
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAddProblemToExperiment($id)
	{
		$experiment=Experiment::model()->findByPk((int)$id);
		if($experiment===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$this->classRoom=$experiment->classRoom;
		if($this->classRoom->denyStudent())$this->denyAccess();
	
		$model=new ExerciseProblem;
		$model->exercise_id=$experiment->exercise_id;
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
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
					$model->exercise_id=$exercise->id;
					$experiment->save();
				}
			}
			$model->attributes=$_POST['ExerciseProblem'];
			$problem = Problem::model()->findByPk((int)$model->problem_id);
			if($problem==null || !$this->canAccess(array('model'=>$problem),'view','problem'))
			{
				$model->addError('problem_id','Not a validate problem id.');
			}
			if(ExerciseProblem::model()->find('exercise_id='.$experiment->exercise_id.' and problem_id ='.(int)$model->problem_id)!=null)
			{
				$model->addError('problem_id','This problem already exists.');
			}
			if($model->title==null||strlen(trim($model->title))==0)
			{
				$model->title=$problem->title;
			}
			if( (!$model->hasErrors()) && $model->save())
			{
				if (Yii::app()->request->isAjaxRequest)
				{
					echo CJSON::encode(array(
							'status'=>'success',
							'message'=>Yii::t('t',"Success!")
					));
					exit;
				}
				else
					$this->redirect(array('view','id'=>$model->id));
			}
		}
	
		if (Yii::app()->request->isAjaxRequest)
		{
			echo CJSON::encode(array(
					'status'=>'failure',
					'form'=>$this->renderPartial('_form', array('model'=>$model), true)));
			exit;
		}
		else
			$this->render('create',array('model'=>$model,));
	}
	
	
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		if($id!=0)
			$model=$this->loadModel($id);
		else
		{
			$model=$this->loadModelByAttr(Yii::app()->request->getQuery('exercise',0),Yii::app()->request->getQuery('problem',0));
		}
		$this->classRoom=$model->exercise->experiment->classRoom;
		if(Yii::app()->user->isGuest)
		{
			throw new CHttpException(404,'Please relogin.');
			return false;
		}
		//$this->checkAccess(array('model'=>$model));
		
		//$submition=$this->canAccess(array('model'=>$model),'Create','Submition')?$this->newSubmition($model->problem):null;
		$submition=$this->newSubmition($model);

		$buttons=array(
			'submit'=>!is_null($submition),
			//'update'=>$this->canAccess(array('model'=>$model),'Update'),
			//'delete'=>$this->canAccess(array('model'=>$model),'Delete'),
		);
		$this->render('view',array(
			'model'=>$model,
			'problem'=>$model->problem,
			'buttons'=>$buttons,
			'submition'=>$submition,
		));		
		//$this->render('view',array(
		//	'model'=>$this->loadModel($id),
		//));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ExerciseProblem;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExerciseProblem']))
		{
			$model->attributes=$_POST['ExerciseProblem'];
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

		if(isset($_POST['ExerciseProblem']))
		{
			$model->attributes=$_POST['ExerciseProblem'];
			if($model->save()){
				if (Yii::app()->request->isAjaxRequest)
				{
					echo CJSON::encode(array(
							'status'=>'success',
							'message'=>Yii::t('t',"Success!")
						)
					);
					exit;
				}
				else
					$this->redirect(array('view','id'=>$model->id));
			}
		}
		if (Yii::app()->request->isAjaxRequest)
		{
			echo CJSON::encode(array(
					'status'=>'failure',
					'form'=>$this->renderPartial('_form', array('model'=>$model), true)));
			exit;
		}
		else
			$this->render('update',array('model'=>$model,));
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
		$dataProvider=new CActiveDataProvider('ExerciseProblem');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ExerciseProblem('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExerciseProblem']))
			$model->attributes=$_GET['ExerciseProblem'];

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
		$model=ExerciseProblem::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModelByAttr($exercise_id,$problem_id)
	{
		$model=ExerciseProblem::model()->find('exercise_id='.(int)$exercise_id.' and problem_id='.(int)$problem_id);
		if($model===null||(int)$exercise_id<=0)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='exercise-problem-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
