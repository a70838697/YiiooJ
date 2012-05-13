<?php

class ProblemController extends ZController
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
				'actions'=>array('index','view','select'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','delete','update','submited','manage','accepted','notAccepted'),
			//	'roles'=>array('Teacher',"Admin"),
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
		$this->checkAccess(array('model'=>$model));

		$submition=$this->canAccess(array('model'=>$model),'Create','Submition')?$this->newSubmition($model):null;

		$buttons=array(
			'submit'=>!is_null($submition),
			'update'=>$this->canAccess(array('model'=>$model),'Update'),
			'delete'=>$this->canAccess(array('model'=>$model),'Delete'),
		);
		$this->render('/problem/view',array(
			'model'=>$model,
			'buttons'=>$buttons,
			'submition'=>$submition,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Problem;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Problem']))
		{
			$model->attributes=$_POST['Problem'];
			$model->user_id=Yii::app()->user->id;
			if($model->setTags(isset($_POST['Tags'])?implode(',', $_POST['Tags']):'')->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
			if(is_int($model->compiler_set))
				$model->compiler_set=UCompilerLookup::values($model->compiler_set);
		}
		
		$this->render('/problem/create',array(
			'model'=>$model,
		));
	}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionManage($id)
	{
		$model=$this->loadModel($id);
		$this->checkAccess(array('model'=>$model),'update');
		
		$problemJudger=$this->newProblemJudger($model);
		if(Yii::app()->request->getQuery('deleteProblemJudger',null)!==null){
			if($model->judger!=null)$model->judger->delete();
			$this->redirect(array('manage','id'=>$model->id));
		}
		
	
		$this->render('/problem/manage',array(
			'model'=>$model,
			'problemJudger'=>$problemJudger,
			'testDataProvider'=>$testDataProvider,
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

		if(isset($_POST['Problem']))
		{
			
			$old_time_limit=$model->time_limit;
			$old_memory_limit=$model->memory_limit;
			
			$model->attributes=$_POST['Problem'];
			if($model->setTags(isset($_POST['Tags'])?implode(',', $_POST['Tags']):'')->save())
			{
				
				if($old_time_limit!=$model->time_limit||$old_memory_limit!=$model->memory_limit)
				{
					$connection=Yii::app()->db;
					$command=$connection->createCommand("update {{submitions}} set status=0 where problem_id=".$model->id);
					$command->execute();
				}
				
				$this->redirect(array('view','id'=>$model->id));
			}
			if(is_int($model->compiler_set))
				$model->compiler_set=UCompilerLookup::values($model->compiler_set);
		}

		$this->render('/problem/update',array(
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
		$criteria=new CDbCriteria(array());
		$tag=Yii::app()->request->getQuery('tag',null);
		if($tag!==null){
			$tag=Tag::model()->findByAttributes(array('name'=>$tag));
			if($tag!=null)
				$criteria=new CDbCriteria(array(
					'condition'=>'Exists( select 1 from {{problem_tags}} as tg where tg.problem_id=t.id and tg.tag_id='.$tag->id.")",
				));
		}

					
		$scopes=array('titled','allCount');
		if((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)
			$scopes[]='mine';
		else $scopes[]='public';
		
		$dataProvider=new EActiveDataProvider('Problem',
			array(
				'scopes'=>$scopes,
				'pagination'=>array(
			        	'pageSize'=>50,
			    ),
				'criteria'=>$criteria,
			)
		);		
		$this->render('/problem/index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionSelect()
	{
		$scopes=array('titled');
		if((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)
			$scopes[]='mine';
		else $scopes[]='public';
		$criteria=new CDbCriteria(array(
	    ));
	    $id=Yii::app()->request->getQuery('id',null);
		if($id!==null && preg_match("/^\d$/",$id))
		{
	    	$criteria->compare('t.id',(int)($id));
		}		
	    $title=Yii::app()->request->getQuery('title',null);
		if($title!==null)
		{
	    	$criteria->compare('t.title',$title,true);
		}		
		$dataProvider=new EActiveDataProvider('Problem',
			array(
				'criteria'=>$criteria,
				'scopes'=>$scopes,
				'pagination'=>array(
			        	'pageSize'=>10,
			    ),
			)
		);
		$render=Yii::app()->request->isAjaxRequest ? 'renderPartial' : 'render';
		$this->$render('/problem/select', array(
		    'dataProvider'=>$dataProvider,
		));
	}	
	/**
	 * Lists all models.
	 */
	public function actionSubmited()
	{
		$dataProvider=new EActiveDataProvider('Problem',
			array(
				'scopes'=>array('titled','public','mySubmited'),
				
				'pagination'=>array(
			        	'pageSize'=>50,
			    ),
			)
		);		
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}
	/**
	 * Lists all models.
	 */
	public function actionAccepted()
	{
		$dataProvider=new EActiveDataProvider('Problem',
			array(
				'scopes'=>array('titled','public','myAccepted'),
				
				'pagination'=>array(
			        	'pageSize'=>50,
			    ),
			)
		);		
		$this->render('/problem/index',array(
			'dataProvider'=>$dataProvider,
		));
	}	
	/**
	 * Lists all models.
	 */
	public function actionNotAccepted()
	{
		$dataProvider=new EActiveDataProvider('Problem',
			array(
				'scopes'=>array('titled','public','myNotAccepted'),
				
				'pagination'=>array(
			        	'pageSize'=>50,
			    ),
			)
		);		
		$this->render('/problem/index',array(
			'dataProvider'=>$dataProvider,
		));
	}	
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Problem('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Problem']))
			$model->attributes=$_GET['Problem'];

		$this->render('/problem/admin',array(
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
		$model=Problem::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='problem-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	/**
	 * Creates a new submition.
	 * This method attempts to create a new submition based on the user input.
	 * If the submition is successfully created, the browser will be redirected
	 * to show the created submition.
	 * @param Problem the problem that the new submition belongs to
	 * @return Submition the submition instance
	 */
	protected function newSubmition($problem)
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
			$submition->exercise_id=0;
			$submition->problem_id=$problem->id;
			if($submition->save())
			{
				//if($comment->status==Comment::STATUS_PENDING)
				//Yii::app()->user->setFlash('submitionSubmitted','Thank you for your submition. Your submition will be judged.');
				//$this->refresh();
				$this->redirect(array('/'.$this->prefix.'submition/view','id'=>$submition->id));				
			}
		}
		return $submition;
	}
}
