<?php

/**
 * This is the model class for table "{{users}}".
 */
class CourseUser extends UUser
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GroupUsers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	public function scopes()
    {
        return parent::scopes();
    }


    
    private $reports=null;
    public function getReports($course_id)
    {
    	if($this->reports==null)$this->reports=$this->experimentReports(array("params"=>array(":course_id" =>$course_id)));
    	return $this->reports;
    }
    public function getCourseExperimentColumn($course_id,$experiment_id)
    {
    	$this->getReports($course_id);
    	// more than one phone exists for the user
    	if(sizeof($this->reports) > 0)
    	{
    		foreach($this->reports as $report)
    		{
    			if($report->experiment_id==$experiment_id){
					return CHtml::link( ($report->score>0)?$report->score:"V",array("experimentReport/view","id"=>$report->id),  array("target"=>"_blank","onclick"=>'return showReport('.$report->id.');'));
				}
    		}
    	}
    	return '';
    }
    
    public function getAverageScore($course_id)
    {
    	$this->getReports($course_id);
    	// more than one phone exists for the user
    	if(sizeof($this->reports) > 1)
    	{
    		$count=0;
    		$sum=0;
    		foreach($this->reports as $report)
    		{
    			if($report->score>0)
    			{
    				$count++;
    				$sum+=$report->score;
    			}
    		}
   			if($count>0)return ((int)($sum/$count+0.5)).'/'.$count;
    	}
    	return '';
    }
    
    
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array_merge( parent::relations(),
			array(
				'group'=>array(self::HAS_ONE, 'GroupUser','user_id','select'=>'id','condition' => 'group.group_id=:group_id and group.status ='.GroupUser::USER_STATUS_ACCEPTED),
				'experimentReport'=>array(self::HAS_ONE, 'ExperimentReport','user_id','select'=>'id,status,score','on' => 'experiment_id=:experiment_id','joinType'=>'LEFT JOIN'),
				'experimentReports'=>array(self::HAS_MANY, 'ExperimentReport','user_id','select'=>'experiment_id,id,status,score','on'=>'EXISTS(select * from {{experiments}} bd where bd.id= experimentReports.experiment_id and bd.course_id=:course_id) '),
			)
		);
	}
}