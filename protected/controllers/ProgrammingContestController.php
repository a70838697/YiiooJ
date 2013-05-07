<?php

class ProgrammingContestController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/onlinejudge';
	public $contentMenu=null;
	public $prefix="";	

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
				'actions'=>array('index','students','view','rank'),
				'roles'=>array('Teacher','Student','Admin'),
			),
			array('allow', // allow Student
				'actions'=>array('apply','experiments','quizzes'),
				'roles'=>array('Student'),			
			),
					
			array('allow', // allow Teacher
				'actions'=>array('create','change','update','experiments','quizzes','reports','deleteExperiment','resubmitReport'),
				'roles'=>array('Teacher'),			
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','change','delete','create','update','experiments','quizzes','students','reports','deleteExperiment','resubmitReport'),
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
		$programming_contest=$this->loadModel($id);
		$exercise_problem=Yii::app()->user->isGuest?null:$this->newExerciseProblem($programming_contest);
		
		$this->render('view',array(
				'model'=>$programming_contest,
				'exercise_problem'=>$exercise_problem,
		));
	}

	public function actionRank($id)
	{
		$model=$this->loadModel($id);
		
		$criteria=new CDbCriteria(array(
		));
		$criteria->select='username,id';
		$criteria->with=array('info','schoolInfo','group');
		$criteria->params=array(':group_id'=>$model->user_group_id);
		
		$dataProvider=new EActiveDataProvider('ClassRoomUser',
				array(
						'criteria'=>$criteria,
				)
		);
		
		$dataProvider->setPagination(false);
		$rawData = array();
		foreach($dataProvider->getData() as $record) {
			
			$items=array();
			$items['id']=$record->id;
			$items['username']=	$record->schoolInfo==null?$record->username:CHtml::link(CHtml::encode($record->info->lastname.$record->info->firstname),array("schoolInfo/view","id"=>$record->schoolInfo->user_id));
			$items['studentid']=$record->schoolInfo==null?"":$record->schoolInfo->identitynumber;
			$items['score']=0;
			$items['solvecount']=0;
			$items['solveproblem']=0;
			$items['totalcount']=0;
			foreach($model->exercise->exercise_problems as $exercise_problem){
				$items['solved'.$exercise_problem->problem_id]=0;
				$items['total'.$exercise_problem->problem_id]=0;
				$items['wrong'.$exercise_problem->problem_id]=0;
			}
			$rawData[$record->id] = $items;
		}
		$total=array();
		$total['id']=99999999;
		$total['username']=	"Total";
		$total['studentid']="";
		$total['score']=0;
		$total['solvecount']=0;
		$total['totalcount']=0;
		$total['solveproblem']=0;
		foreach($model->exercise->exercise_problems as $exercise_problem){
			$total['solved'.$exercise_problem->problem_id]=0;
			$total['total'.$exercise_problem->problem_id]=0;
			$total['wrong'.$exercise_problem->problem_id]=0;
		}
		$criteria=new CDbCriteria(array(
		));
		$criteria->select="t.user_id,t.created,t.status,t.problem_id";
		//$criteria->compare('t.status',ULookup::JUDGE_RESULT_PENDING,true);
		$criteria->compare('t.exercise_id',$model->exercise_id);
		$criteria->order="t.created";
		$dataProvider=new EActiveDataProvider('Submition',
				array(
						'criteria'=>$criteria,
				)
		);
		$dataProvider->setPagination(false);
		$begin_date=CDateTimeParser::parse($model->begin,"yyyy-MM-dd hh:mm:ss") ;
		foreach($dataProvider->getData() as $record) {
			if(isset($rawData[$record->user_id]))
			{
				$rawData[$record->user_id]['total'.$record->problem_id]++;
				$total['total'.$record->problem_id]++;
				if($record->status==ULookup::JUDGE_RESULT_ACCEPTED)
				{
					if($rawData[$record->user_id]['solved'.$record->problem_id]==0)
					{
						$rawData[$record->user_id]['solveproblem']++;
						$rawData[$record->user_id]['score']+=20*$rawData[$record->user_id]['wrong'.$record->problem_id]+CDateTimeParser::parse($record->created,"yyyy-MM-dd hh:mm:ss") -$begin_date;
					}
					if($total['solved'.$record->problem_id]==0)$total['solveproblem']++;
					$rawData[$record->user_id]['solved'.$record->problem_id]++;
					$total['solved'.$record->problem_id]++;
					$rawData[$record->user_id]['solvecount']++;
				}
				else if($record->status!=ULookup::JUDGE_RESULT_PENDING)
				{
					$total['wrong'.$record->problem_id]++;
					$rawData[$record->user_id]['wrong'.$record->problem_id]++;
				}
			}
			else echo $record->user_id.'xxxxx';
		}
		
		function cmp($a, $b)
		{
			if($a['solveproblem']==$b['solveproblem'])return $a['score']-$b['score'];
			return $b['solveproblem']-$a['solveproblem'];
		}
		usort($rawData,'cmp');
		
		
		
		$rank=1;
		$oldrank=1;
		$oldscore=-1;
		$oldsolveproblem=-1;
		foreach ($rawData as &$item) {
			if( $oldsolveproblem!=$item['solveproblem'] || $oldscore!=$item['score'] )
			{
				$oldsolveproblem=$item['solveproblem'];
				$oldscore=$item['score'];
				$oldrank=$rank;
			}else $oldrank="";
			$item['rank']=$oldrank;
			$rank++;
		}
		unset($item);
		$total['rank']='';
		
		$rawData[]=$total;
		$dataProvider=new CArrayDataProvider($rawData, array(
				'id'=>'id',
				'pagination'=>array(
						'pageSize'=>200,
				),
		));
		
		$this->render('rank',array(
				'model'=>$model,
				'dataProvider'=>$dataProvider,
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
	protected function newExerciseProblem($programming_contest)
	{
		$exercise_problem=new ExerciseProblem;
		if(isset($_POST['ajax']) && $_POST['ajax']==='exercise-problem-form')
		{
			echo CActiveForm::validate($exercise_problem);
			Yii::app()->end();
		}
		if(isset($_POST['ExerciseProblem']))
		{
			if($programming_contest->exercise_id==0)
			{
				$exercise=new Exercise;
				$exercise->type_id = Exercise::EXERCISE_TYPE_PROGRAMMING_CONTEST;
				$exercise->belong_to_id=$programming_contest->id;
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
			if(ExerciseProblem::model()->find('exercise_id='.$programming_contest->exercise_id.' and problem_id ='.(int)$exercise_problem->problem_id)!=null)
			{
				$exercise_problem->addError('problem_id','This problem already exists.');
				return $exercise_problem;
			}
			if($exercise_problem->title==null||strlen(trim($exercise_problem->title))==0)
			{
				$exercise_problem->title=$problem->title;
			}
			$exercise_problem->exercise_id=$programming_contest->exercise_id;
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
	public function actionCreate()
	{
		$model=new ProgrammingContest;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ProgrammingContest']))
		{
			$model->attributes=$_POST['ProgrammingContest'];
			$model->user_id=Yii::app()->user->id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	public function actionChange($id)
	{
		$model=$this->loadModel($id,'myMemberShip');
	
		GroupUser::model()->updateAll(array('status'=>GroupUser::USER_STATUS_ACCEPTED),'group_id='.$model->user_group_id);
		die;
	}
	
	private function addStudentMember($model,$student_id,$status)
	{
		if($model->user_group_id==0)
		{
			$studentGroup= new Group;
			$studentGroup->type_id= Group::GROUP_TYPE_CLASS_ROOM;
			$studentGroup->belong_to_id=$model->id;
			if(!$studentGroup->save())
				return false;
			$model->user_group_id=$studentGroup->id;
			if(!$model->save())return false;
		}
		$groupUser=new GroupUser();
		$groupUser->group_id = $model->user_group_id;
		$groupUser->user_id=$student_id;
		$groupUser->status = $status;
		return $groupUser->save()?$groupUser:false;
	}
	/**
	 * Apply for the class room.
	 * @param integer $id the ID of the model to be applyed
	 */
	public function actionApply($id)
	{
		$model=$this->loadModel($id,'myMemberShip');
		if($model->denyStudent())$this->denyAccess();
		//$this->checkAccess(array('model'=>$model));
		$groupUser=$model->myMemberShip;
		if($groupUser===null)
		{
			if($model->application_option!=ClassRoom::STUDENT_APPLICATION_OPTION_DENY){
				if(Yii::app()->request->getQuery('op',null)=='apply'){
					if(UUserIdentity::isStudent())
						$groupUser=$this->addStudentMember($model,Yii::app()->user->id,$model->application_option==ClassRoom::STUDENT_APPLICATION_OPTION_APPROVE?GroupUser::USER_STATUS_APPLIED:GroupUser::USER_STATUS_ACCEPTED);
					if(!$groupUser)
						throw new CHttpException(404,'The requested operation can not be done.');
				}
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
					'status'=>$groupUser?false:($groupUser->status) ,
			);
			echo json_encode($result);
			die;
		}
	
		$this->redirect(array('programmingContest/index/mine'));
	}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionStudents($id)
	{
		$model=$this->loadModel($id);
		$canAccess=UUserIdentity::isAdmin()||UUserIdentity::isTeacher();
		if(!$canAccess)
			$this->checkAccess(array('model'=>$model));
		
		if(isset($_POST['students_ids']))
		{
			foreach(preg_split("/,/",$_POST['students_ids']) as $student_id)
			{
				if((int)$student_id>0)$this->addStudentMember($model,(int)$student_id,GroupUser::USER_STATUS_ACCEPTED);
			}
		}
	
		$command = Yii::app()->db->createCommand()
		->select('count(1)')
		->from('{{group_users}} a')
		->join('{{users}} b', 'a.user_id=b.id')
		->join('{{profiles}} c', 'a.user_id=c.user_id')
		->join('{{school_infos}} d', 'a.user_id=d.user_id')
		->where('a.group_id= :group_id', array(':group_id'=>$model->user_group_id));
		$count=$command->queryScalar();
	
		$command->reset();
	
		$command->select('a.id,a.status, b.id as user_id,b.username,b.email,c.lastname,c.firstname,d.identitynumber')
		->from('{{group_users}} a')
		->join('{{users}} b', 'a.user_id=b.id')
		->join('{{profiles}} c', 'a.user_id=c.user_id')
		->join('{{school_infos}} d', 'a.user_id=d.user_id')
		->where('a.group_id= :group_id');
	
		$sql=$command->getText();
		$sort = new CSort();
		$sort->attributes = array(
				'username'=>array(
						'asc'=>'username',
						'desc'=>'username desc',
				),
				'name'=>array(
						'asc'=>'lastname,firstname',
						'desc'=>'lastname desc,firstname desc',
				),
				'identitynumber',
				'status',
		);
	
	
		$dataProvider=new CSqlDataProvider($sql, array(
				'params'=> array(':group_id'=>$model->user_group_id),
				'totalItemCount'=>$count,
				'sort'=>$sort,
				'pagination'=>array(
						'pageSize'=>30,
				),
		));
	
		$this->render('students',array(
				'model'=>$model,
				'dataProvider'=>$dataProvider,
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
		if(UUserIdentity::isAdmin()||UUserIdentity::isTeacher())
		{
			
		}else
		$this->checkAccess(array('model'=>$model));
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ProgrammingContest']))
		{
			$model->attributes=$_POST['ProgrammingContest'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$scopes=array('recentlist');
		
		if(Yii::app()->request->getQuery('term',null)!==null)
			$scopes[]='term';
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
	    
		$dataProvider=new EActiveDataProvider('ProgrammingContest',
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
		$model=new ProgrammingContest('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ProgrammingContest']))
			$model->attributes=$_GET['ProgrammingContest'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id,$with=null)
	{
		if($with!==null)
			$model=ProgrammingContest::model()->with($with)->findByPk((int)$id);
		else
			$model=ProgrammingContest::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='programming-contest-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
