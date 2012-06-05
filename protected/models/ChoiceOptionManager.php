<?php
class ChoiceOptionManager extends TabularInputManager
{
 
    protected $class='ChoiceOption';
 
    public function getItems()
    {
        if (is_array($this->_items))
            return ($this->_items);
        else 
            return array(
                'n0'=>new ChoiceOption,
            );
    }
 
 
    public function deleteOldItems($model, $itemsPk)
    {
        $criteria=new CDbCriteria;
        $criteria->addNotInCondition('id', $itemsPk);
        $criteria->addCondition("multiple_choice_id= {$model->primaryKey}");
 
        ChoiceOption::model()->deleteAll($criteria); 
    }
 
 
    public function load($model)
    {
        foreach ($model->choiceOptions as $item)
            $this->_items[$item->primaryKey]=$item;
        return $this;
    }
 
 
    public function setUnsafeAttribute($item, $model)
    {
        $item->multiple_choice_id=$model->primaryKey;
 
    }
 
 
}