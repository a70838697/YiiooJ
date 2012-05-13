<?php

class UuserController extends Controller
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
	public function actionView($id)
	{
		$model=UUser::model()->with('submitedCount','acceptedCount','acceptedProbCount','submitedProbCount')->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$command=Yii::app()->db->createCommand("
		select count(*) from ( select t.`id`, (select count(DISTINCT(ac.problem_id) ) from {{submitions}} as ac where (ac.user_id=t.id ) and  ac.status=1 ) as acceptedProblemCount, (select count(1) from {{submitions}} as sb where(sb.user_id=t.id) ) as submisionCount".
		" from {{users}} t ) as t2 where acceptedProblemCount>".$model->acceptedProbCount." or (acceptedProblemCount=".$model->acceptedProbCount." and submisionCount<".$model->submitedCount.") or (acceptedProblemCount=".$model->acceptedProbCount." and submisionCount=".$model->submitedCount."  and t2.id<".(int)$id.")
		");
		$rank=(int)$command->queryScalar()+1;
		$criteria=new CDbCriteria();
		$criteria->select='t.`id`,t.`username`, (select count(DISTINCT(ac.problem_id) ) from {{submitions}} as ac where (ac.user_id=t.id ) and  ac.status='.ULookup::JUDGE_RESULT_ACCEPTED .' ) as acceptedProblemCount, (select count(1) from {{submitions}} as sb where(sb.user_id=t.id) ) as submissionCount';
		$criteria->order='acceptedProblemCount desc,submissionCount,t.`id`';
		$criteria->offset=$rank-4;
		$criteria->limit=7;
		$rankDataProvider=new CActiveDataProvider('UUser',array (
                        'criteria' => $criteria,'pagination'=>false, ) );

		$criteria=new CDbCriteria();
		$criteria->select='t.`id`, (select count(1) from {{submitions}} as ac where (ac.user_id='.(int)$id.' and ac.problem_id=t.id and  ac.status='.ULookup::JUDGE_RESULT_ACCEPTED .'  )) as hisAcceptedCount, (select count(1) from {{submitions}} as sb where(sb.user_id='.(int)$id.' and sb.problem_id=t.id) ) as hisSubmitedCount';
		$criteria->condition='exists(select 1 from {{submitions}} as sd where sd.problem_id=t.id and sd.user_id='.(int)$id.')';
		$criteria->order='t.`id`';
		$problemDataProvider=new CActiveDataProvider('Problem',array (
                        'criteria' => $criteria ,'pagination'=>false) );
				
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'rank'=>$rank,
			'rankDataProvider'=>$rankDataProvider,
			'problemDataProvider'=>$problemDataProvider,
		));
	}


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		
		$criteria=new CDbCriteria();
		$criteria->select='t.`id`,t.`username`, (select count(DISTINCT(ac.problem_id) ) from {{submitions}} as ac where (ac.user_id=t.id ) and  ac.status='.ULookup::JUDGE_RESULT_ACCEPTED .' ) as acceptedProblemCount, (select count(1) from {{submitions}} as sb where(sb.user_id=t.id) ) as submissionCount';
		$criteria->order='acceptedProblemCount desc,submissionCount,t.`id`';
		$dataProvider=new CActiveDataProvider('UUser',array (
                        'criteria' => $criteria,'pagination'=>array('pageSize'=>15,) ) );
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=UUser::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='uuser-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
