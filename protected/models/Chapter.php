<?php

/**
 * This is the Nested Set  model class for table "chapter".
 *
 * The followings are the available columns in table 'chapter':
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 * @property string $name
 * @property string $description
 */
class Chapter extends CActiveRecord
{

         /**
	 * Id of the div in which the tree will berendered.
	 */
    const ADMIN_TREE_CONTAINER_ID='chapter_admin_tree';


	/**
	 * Returns the static model of the specified AR class.
	 * @return Chapter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

        /**
	 * @return string the class name
	 */
          public static function className()
	{
		return __CLASS__;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{chapters}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE1: you should only define rules for those attributes that
		// will receive user inputs.
                // NOTE2: Remove ALL rules associated with the nested Behavior:
                //rgt,lft,root,level,id.
		return array(
			array('name', 'required'),
			array('description', 'length', 'min'=>0),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, description', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    //'products'=>array(self::HAS_MANY, 'Product', 'category_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Name',
			'description' => 'Description',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('root',$this->root,true);
		$criteria->compare('lft',$this->lft,true);
		$criteria->compare('rgt',$this->rgt,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        public function behaviors()
{
    return array(
        'NestedSetBehavior'=>array(
            'class'=>'ext.nestedBehavior.NestedSetBehavior',
            'leftAttribute'=>'lft',
            'rightAttribute'=>'rgt',
            'levelAttribute'=>'level',
            'hasManyRoots'=>true
            )
    );
}

  public static  function printULTree(){
     $categories=Chapter::model()->findAll(array('condition'=>'root=1 and id!=1','order'=>'lft'));
     $level=0;

foreach($categories as $n=>$category)
{

    if($category->level==$level)
        echo CHtml::closeTag('li')."\n";
    else if($category->level>$level)
        echo CHtml::openTag('ul')."\n";
    else
    {
        echo CHtml::closeTag('li')."\n";

        for($i=$level-$category->level;$i;$i--)
        {
            echo CHtml::closeTag('ul')."\n";
            echo CHtml::closeTag('li')."\n";
        }
    }

    echo CHtml::openTag('li',array('id'=>'node_'.$category->id,'rel'=>$category->name));
      echo CHtml::openTag('a',array('href'=>'#'));
    echo CHtml::encode($category->name);
      echo CHtml::closeTag('a');

    $level=$category->level;
}

for($i=$level;$i;$i--)
{
    echo CHtml::closeTag('li')."\n";
    echo CHtml::closeTag('ul')."\n";
}

}

public static  function printULTree_noAnchors(){
    $categories=Chapter::model()->findAll(array('order'=>'lft'));
    $level=0;

foreach($categories as $n=>$category)
{
    if($category->level == $level)
        echo CHtml::closeTag('li')."\n";
    else if ($category->level > $level)
        echo CHtml::openTag('ul')."\n";
    else         //if $category->level<$level
    {
        echo CHtml::closeTag('li')."\n";

        for ($i = $level - $category->level; $i; $i--) {
                    echo CHtml::closeTag('ul') . "\n";
                    echo CHtml::closeTag('li') . "\n";
                }
    }

    echo CHtml::openTag('li');
    echo CHtml::encode($category->name);
    $level=$category->level;
}

for ($i = $level; $i; $i--) {
            echo CHtml::closeTag('li') . "\n";
            echo CHtml::closeTag('ul') . "\n";
        }

}



}