<?php

class GroupController extends Controller
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
				'actions'=>array('view','apply','selectStudent'),
				'users'=>array('*'),
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
	public function actionSelectStudent($id)
	{
		$model=$this->loadModel($id);
		$criteria=new CDbCriteria(array(
	    ));
	    $criteria->with=array('profile','unit');
	    $criteria->condition='profile.group='.UUserIdentity::GROUP_STUDENT ." and not exists(select 1 from {{group_users}} as gu where gu.group_id=".(int)$id." and gu.user_id=t.user_id )";
	    $identitynumber=Yii::app()->request->getQuery('identitynumber',null);
	    if($identitynumber!=null)
	    {
		    $criteria->compare('t.identitynumber',$identitynumber,true);
	    }
	    
		$dataProvider=new EActiveDataProvider('Jnuer',
			array(
				'criteria'=>$criteria,
				'pagination'=>array(
			        	'pageSize'=>10,
			    ),
			)
		);
		$render=Yii::app()->request->isAjaxRequest ? 'renderPartial' : 'render';
	
		$this->$render('selectStudent',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}	
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		$criteria=new CDbCriteria(array(
	    ));
	    $criteria->compare('t.group_id',$id);
	    
		$dataProvider=new EActiveDataProvider('GroupUser',
			array(
				'criteria'=>$criteria,
				'scopes'=>array('common'),
				'pagination'=>array(
			        	'pageSize'=>30,
			    ),
			)
		);
		$render=Yii::app()->request->isAjaxRequest ? 'renderPartial' : 'render';
	
		$this->$render('view',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionApply($id)
	{
		$model=GroupUser::model("GroupUser")->findByPk((int)$id);
		if($model===null)die;
		if(Yii::app()->request->getQuery('op',null)=='agree'){
			$model->status= GroupUser::USER_STATUS_ACCEPTED;
			$model->save();
		}
		if(Yii::app()->request->getQuery('op',null)=='deny'){
			$model->delete();
		}
		die;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Group::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='group-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
