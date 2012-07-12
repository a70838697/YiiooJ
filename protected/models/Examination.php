<?php

/**
 * This is the Nested Set  model class for table "{{examinations}}".
 *
 * The followings are the available columns in table '{{examinations}}':
 * @property integer $id
 * @property integer $problem_id
 * @property integer $type_id
 * @property string $sequence
 * @property integer $root
 * @property string $lft
 * @property string $rgt
 * @property float $score
 * @property integer $level
 * @property string $name
 * @property string $description
 */
class Examination extends CActiveRecord
{

	/**
	 * Id of the div in which the tree will berendered.
	 */
    const ADMIN_TREE_CONTAINER_ID='examination_admin_tree';


	/**
	 * Returns the static model of the specified AR class.
	 * @return Examination the static model class
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
		return '{{examinations}}';
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
				array('problem_id', 'numerical', 'integerOnly'=>true),
				array('type_id', 'numerical', 'integerOnly'=>true),
				array('sequence', 'length', 'max'=>20),
				array('score', 'numerical'),
				
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
		;
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'examinations' => array(self::HAS_MANY, 'Examination', 'root','order'=> 'examinations.lft'),
				'examination' => array(self::BELONGS_TO, 'Examination', 'root'),
				'practice' => array(self::HAS_ONE, 'Practice', 'examination_id'),
				'multiple_choice_problem' => array(self::BELONGS_TO, 'MultipleChoice', 'problem_id'),
				'Problem' => array(self::BELONGS_TO, 'Problem', 'problem_id'),
				'answer'=>array(self::HAS_ONE,'QuizAnswer','examination_id','condition'=>'answer.user_id='.(isset(Yii::app()->params['hisId'])?(int)yii::app()->params['hisId']:(int)yii::app()->user->id).' and answer.quiz_id='.(isset(Yii::app()->params['quiz']) && (Yii::app()->params['quiz']!==null)?(int)Yii::app()->params['quiz']:-1)),
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
				'sequence' => 'Sequence',
				'name' => 'Name',
			'score'=>'Score',
			'type_id' => 'Type',
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

	public static  function printULTree($root_id=null){
		if($root_id==null){
			//by default, the whole tree is loaded
			$categories=Examination::model()->findAll(array('order'=>'root,lft'));
		}
		else
		{
			//only load the children of one tree having a root whose id equals $root_id
			$categories=Examination::model()->findAll(array('condition'=>'root=:root','order'=>'lft','params'=>array(':root'=>$root_id)));
		}
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
			echo CHtml::encode($category->sequence."($category->score points)".$category->name);
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
		$categories=Examination::model()->findAll(array('order'=>'lft'));
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