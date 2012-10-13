<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/course';
	public $prefix='';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	public $toolbar=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	public function denyAccess(){
		throw new CHttpException(404,'The requested operation can not be done.');		
	}
	public function canAccess($params=array(),$action=null,$controller=null)
	{
		if(is_null($controller))$controller=(isset($this->actual_controller))?($this->actual_controller):Yii::app()->controller->id;
		if(is_null($action))$action=$this->getAction()->getId();
		return Yii::app()->user->checkAccess($controller.":".$action,$params);
	}
	public function checkAccess($params=array(),$action=null,$controller=null)
	{
		if(!$this->canAccess($params,$action,$controller))
		{
			if(Yii::app()->user->getIsGuest())
				Yii::app()->user->loginRequired();
			else
				throw new CHttpException(403,('You are not authorized to perform this action.'));
		}
		
	}

	public $test_id=null;
	
	public $course=null;
	public $classRoom=null;
	public $course_id=0;
	public $course_title='';
	public $class_room_id=null;
	private function initInfo()
	{
		if($this->class_room_id==0)
		{
			if(isset($_GET['class_room_id']))$this->class_room_id=(int)$_GET['class_room_id'];
			if($this->classRoom!==null)$this->class_room_id=$this->classRoom->id;
		}
		if($this->class_room_id>0 && $this->classRoom===null)
		{
			$this->classRoom=ClassRoom::model()->findByPk($this->class_room_id);
		}
	
		if($this->course===null&&$this->classRoom!==null)$this->course=$this->classRoom->course;
		if(!$this->course_id && isset($_GET['course_id']))
		{
			$this->course=Course::model()->findByPk((int)$_GET['course_id']);
		}
	
	}
	public function getCourse()
	{
		if($this->course===null)$this->initInfo();
		return $this->course;
	}
	public function getCourseId()
	{
		if($this->course===null)$this->initInfo();
		return ($this->course===null)?0:$this->course->id;
	}
	public function getClassRoom()
	{
		if($this->class_room_id===null)$this->initInfo();
		return $this->classRoom;
	}	
	public function getClassRoomId()
	{
		if($this->class_room_id===null)$this->initInfo();
		return $this->class_room_id;
	}
	
	public function init()
	{
		if(isset($_REQUEST['lang'])&&$_REQUEST['lang']!="")
		{
			Yii::app()->language=$_REQUEST['lang'];
			setcookie('lang',$_REQUEST['lang']);
		}else if(isset($_COOKIE['lang'])&&$_COOKIE['lang']!="")
		{
			Yii::app()->language=$_COOKIE['lang'];
		}else if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])&& $_SERVER['HTTP_ACCEPT_LANGUAGE']!=""){
			$lang=explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			//var_dump($lang);
			Yii::app()->language=strtolower(str_replace('-','_',$lang[0]));
		}
	}
}