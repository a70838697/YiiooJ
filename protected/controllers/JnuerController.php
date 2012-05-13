<?php

class JnuerController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Jnuer;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Jnuer']))
		{
			$model->attributes=$_POST['Jnuer'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->user_id));
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
		if(!UUserIdentity::isAdmin())$id=Yii::app()->user->id;
		if(UUserIdentity::isAdmin()||UUserIdentity::isCommonUser())
		{
			$model=Jnuer::model()->findByPk((int)$id);
			if($model==null)
			{
				$model= new Jnuer;
				$model->user_id=(int)$id;
			}
			if($model==null||$model->user==null)
			{
				throw new CHttpException(404,'The requested page does not exist.');				
			}
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
	
			$bsaved=true;
			if(isset($_POST['Jnuer']))
			{
				if($bsaved&&isset($_POST['UProfile'])){
					if(!UUserIdentity::isAdmin())unset($_POST['UProfile']['group']);
					$model->profile->attributes=$_POST['UProfile'];
					//if($model->profile->birthday==null)$model->profile->birthday= new DateTime('0000-00-00');
					$bsaved=$model->profile->save();
				}
				$model->attributes=$_POST['Jnuer'];
				if($bsaved)
				{
					if($model->save())
						$this->redirect(array('view','id'=>$model->user_id));
				}
			}
	
			$nodeRoot=Organization::model()->roots()->findByPk(1);
			$treeArray=array();
			if($nodeRoot!=null)
			{
				$treeArray[$nodeRoot->id]=str_repeat('&nbsp;',2*($nodeRoot->level-1)).CHtml::encode($nodeRoot->title);
				$tree=$nodeRoot->descendants()->findAll();
				if(!empty($tree))
				{
					foreach ($tree as $node)
					{
						//var_dump($node);
						$treeArray[$node->id]=str_repeat('&nbsp;',2*($node->level-1)).CHtml::encode($node->title);
					}
				}
			}
			$this->render('update',array(
				'model'=>$model,
				'units'=>$treeArray,
			));
			return;
		}
		else {
			$model=$this->loadModel($id);
		}
		
		$this->redirect(array('view','id'=>$model->user_id));
		
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
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Jnuer('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Jnuer']))
			$model->attributes=$_GET['Jnuer'];

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
		$model=Jnuer::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='jnuer-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
