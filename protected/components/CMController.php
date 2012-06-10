<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CMController extends Controller
{
	public $course=null;
	public $classRoom=null;
	public $cours_id=0;
	public $course_title='';
	public $class_room_id=null;
	private function initInfo()
	{
		$this->class_room_id=0;
		
		if(isset($_GET['class_room_id']))$this->class_room_id=(int)$_GET['class_room_id'];
		if($this->classRoom!==null)$this->class_room_id=$this->classRoom->id;
		if($this->class_room_id>0 && $this->classRoom===null)
		{
			$this->classRoom=ClassRoom::model()->findByPk($this->class_room_id);
		}
		
		if($this->course===null&&$this->classRoom!==null)$this->course=$this->classRoom->course;
		
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
	public function getClassRoomId()
	{
		if($this->class_room_id===null)$this->initInfo();
		return $this->class_room_id;
	}
	public $layout='//layouts/course';
	public $prefix='';
}