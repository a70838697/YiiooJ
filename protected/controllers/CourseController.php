<?php

class CourseController extends Controller
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
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow Student
				'actions'=>array('apply','experiments'),
				'roles'=>array('Student'),			
			),
					
			array('allow', // allow Teacher
				'actions'=>array('create','update','students','experiments'),
				'roles'=>array('Teacher'),			
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update','experiments','students'),
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
		$model=$this->loadModel($id,'myMemberShip');
		$this->checkAccess(array('model'=>$model));		
		

		$this->render('view',array(
			'model'=>$model,
		));
	}
	private function addStudentMember($model,$student_id,$status)
	{
		if($model->student_group_id==0)
		{
			$studentGroup= new Group;
			$studentGroup->type_id= Group::GROUP_TYPE_COURSE;
			$studentGroup->belong_to_id=$model->id;
			if(!$studentGroup->save())
				return false;
			$model->student_group_id=$studentGroup->id;
			if(!$model->save())return false;
		}
		$groupUser=new GroupUser();
		$groupUser->group_id = $model->student_group_id;
		$groupUser->user_id=$student_id;
		$groupUser->status = $status;
		if(!$groupUser->save())
			return false;
	}
	/**
	 * Apply for the course.
	 * @param integer $id the ID of the model to be applyed
	 */
	public function actionApply($id)
	{
		$model=$this->loadModel($id,'myMemberShip');
		$this->checkAccess(array('model'=>$model));				
		$groupUser=$model->myMemberShip;
		if($groupUser===null)
		{
			if(Yii::app()->request->getQuery('op',null)=='apply'){
				if(!$this->addStudentMember($model,Yii::app()->user->id,GroupUser::USER_STATUS_APPLIED))
					throw new CHttpException(404,'The requested operation can not be done.');
			}
		}else {
			if(Yii::app()->request->getQuery('op',null)=='cancel')
			{
				$groupUser->delete();
			}
		}
		if(Yii::app()->request->isAjaxRequest )
		{
			$result=array(
				'ok'=>true,//you can check time, if timeout, no result
				'status'=>$groupUser->status ,
			);
			echo json_encode($result);
			die;
		}		
		
		$this->redirect(array('course/index/mine'));
	}	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionExperiments($id)
	{
		$model=$this->loadModel($id,'myMemberShip');
		$this->checkAccess(array('model'=>$model));				
		
		$experiment=UUserIdentity::isTeacher()?$this->newExperiment($model):null;

		$this->render('experiments',array(
			'model'=>$model,
			'experiment'=>$experiment,
		));
	}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionStudents($id)
	{
		$model=$this->loadModel($id);
		if(isset($_POST['students_ids']))
		{
			foreach(preg_split("/,/",$_POST['students_ids']) as $student_id)
			{
				if((int)$student_id>0)$this->addStudentMember($model,(int)$student_id,GroupUser::USER_STATUS_ACCEPTED);
			}
		}
		$this->checkAccess(array('model'=>$model));		
		
		$this->render('students',array(
			'model'=>$model,
		));
	}	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Course;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Course']))
		{
			$model->attributes=$_POST['Course'];
			$model->user_id= Yii::app()->user->id;
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
		$this->checkAccess(array('model'=>$model));		
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Course']))
		{
			$model->attributes=$_POST['Course'];
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
		$scopes=array('recentlist');
		
		if(Yii::app()->request->getQuery('mine',null)!==null)
			$scopes[]='mine';
		else $scopes[]='public';
		$criteria=new CDbCriteria(array(
	    ));
	    $status=Yii::app()->request->getQuery('status',null);
		if($status!==null && preg_match("/^\d$/",$status))
		{
	    	$criteria->compare('t.status',(int)($status));
		}
	    
		$dataProvider=new EActiveDataProvider('Course',
			array(
				'criteria'=>$criteria,
				'scopes'=>$scopes,
				'pagination'=>array(
			        	'pageSize'=>30,
			    ),
			)
		);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Course('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Course']))
			$model->attributes=$_GET['Course'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new experiment.
	 * This method attempts to create a new experiment based on the user input.
	 * If the experiment is successfully created, the browser will be redirected
	 * to show the created experiment.
	 * @param Course the course that the new experiment belongs to
	 * @return Experiment the experiment instance
	 */
	protected function newExperiment($course)
	{
		$experiment=new Experiment;
		if(isset($_POST['ajax']) && $_POST['ajax']==='experiment-form')
		{
			echo CActiveForm::validate($experiment);
			Yii::app()->end();
		}
		if(isset($_POST['Experiment']))
		{
			$experiment->attributes=$_POST['Experiment'];
			$experiment->user_id=Yii::app()->user->id;
			$experiment->course_id=$course->id;
			$experiment->exercise_id=0;
			if($experiment->save())
			{
				//if($comment->status==Comment::STATUS_PENDING)
				Yii::app()->user->setFlash('experimentSubmitted','Your experiment has been saved.');
				$this->refresh();
			}
		}
		return $experiment;
	}	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id,$with=null)
	{
		if($with!==null)
			$model=Course::model()->with($with)->findByPk((int)$id);
		else
			$model=Course::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='course-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
