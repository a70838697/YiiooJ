<?php
/**
 * TabularInputManager is a class of utility for manage tabular input.
 * it supplies all utlity necessary for create, save models in tabular input
 */
abstract class TabularInputManager extends CComponent
{
	/**
	 * @var array the items on wich to work
	 */
	public $_items;
	
	/**
	 * $var string the classname of the items
	 */
	protected $class;
	
	protected $_lastNew=0;
	
	
	/**
	 * Main function of the class.
	 * load the items from db and applys modification
	 * @param model the model on wich keywords belongs to
	 */
	public function manage($data)
	{
		// var for last new record
		$this->_lastNew=0;
		$classname=$this->class;
		$this->_items=array();
		foreach($data as $i=>$item_post)
		{	// if is ordred a deletion, we jump to the next one element
			if (($i=='command')||($i=='id'))
				continue;
			if (isset($data['command'])&&isset($data["id"]))
				if (($data['command']=="delete")&&($data["id"]==$i))
					continue;
			
			// if the code is like 'nxxx' is a new record
			if (substr($i, 0, 1)=='n')
			{ 
				// create of new record
				$item=new $classname();
				// rember of the last one code
				$this->_lastNew=substr($i, 1);
			} 
			else // load from db
				$item=CActiveRecord::model($this->class)->findByPk($i);
		
			$this->_items[$i]=$item;
			if(isset($data[$i]))
				$item->attributes=$data[$i];
		} // add of new keyword
		if (isset($data['command']))
			if ($data['command']=="add")
			{
				$newId='n'.($this->_lastNew+1);
				$item=new $classname();
				$this->_items[$newId]=$item;
			}
	}
	
	public function getLastNew()
	{
		return $this->_lastNew;
	}
	
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
	
	/**
	 * saves the tags on db and delete not needed tags
	 * @param photograph the photograph on wich tags belongs to
	 */
	public function save($model)
	{
		
		$itemOk=array();
		foreach ($this->_items as $i=>$item)
		{
			$this->setUnsafeAttribute($item, $model);
			$item->save();
			$itemOk[]=$item->primaryKey;
		}
		if (!$model->isNewRecord)
			$this->deleteOldItems($model, $itemOk);
	}
	
	/**
	 * set the unsafe attributes in the items, usually the primary key of the model
	 * @param model the item to save
	 * @param model the model on wich the items belongs to
	 */
	public abstract function setUnsafeAttribute($item, $model);
	
	/**
	 * Delete the items that the user deleted
	 * @param model the model on wich the items belongs to
	 * @param array the primary keys to preserve
	 */
	public abstract function deleteOldItems($model, $itemsPk);
	
	
	/**
	 * Create a tagManager and load the existent tags
	 * @param photograph the photograph on wich tags belongs to
	 * @return tagManager the newly created tagManager
	 */
	public abstract function load($model);
	


}
