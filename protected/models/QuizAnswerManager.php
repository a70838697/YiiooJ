<?php
class QuizAnswerManager
{
	/**
	 * @var array the items on wich to work
	 */
	public $_items;
	
	/**
	 * $var string the classname of the items
	 */
    protected $class='QuizAnswer';
 
	/**
	 * retrive the items
	 * @return array the items loaded
	 */
	public function getItems()
	{
		if (is_array($this->_items))
			return ($this->_items);
		else 
			return array();
	}
    /**
     * Validates items
     * @return boolean weather the validation is successfully
     */
    public function validate()
    {
    
    	$valid=true;
    	foreach ($this->_items as $i=>$model)
    		//we want to validate all tags, even if there are errors
    		$valid=$model->validate() && $valid;
    	return $valid;
    
    }
    public function load($trees,$save_post=false)
    {
    	$success=true;
    	$this->_items=array();
    	foreach ($trees as $node)
    	{
    		if($node->answer===null){
    			$node->answer=new QuizAnswer();
    			$node->answer->examination_id=$node->id;
    			$node->answer->quiz_id=(int)Yii::app()->params['quiz'];
    		}
    		if($save_post)
    		{
    			if(isset($_POST['QuizAnswer'][$node->id])){
		    		$node->answer->attributes=$_POST['QuizAnswer'][$node->id];
		    		$node->answer->checkAnswer();
		    		if(! $node->answer->save()) $success=false;
    			}
	    		//print_r($node->answer->getErrors());
    		}
    		$this->_items[$node->id]=$node->answer;
    	}
    	return $success;
    }
 
}