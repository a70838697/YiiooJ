<?php

/**
 * This is the model class for table "{{exercise_problems}}".
 *
 * The followings are the available columns in table '{{exercise_problems}}':
 * @property integer $id
 * @property integer $exercise_id
 * @property string $title
 * @property integer $problem_id
 * @property string $memo
 * @property string $created
 */
class ExerciseProblem extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ExerciseProblem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{exercise_problems}}';
	}
	
	/**
	 * @return string the permalink URL for this exercise problem
	 */
	public function getUrl()
	{
		return Yii::app()->createUrl('exerciseproblem/view', array(
			'id'=>$this->id,
			'title'=>$this->title,
		));
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('exercise_id, problem_id,sequence', 'required'),
			array('exercise_id, problem_id', 'numerical', 'integerOnly'=>true),
			array('title,memo', 'length', 'max'=>100),
			array('sequence', 'length', 'max'=>20),			
	        array('created','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'insert'),	
	        // The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, exercise_id, title,sequence, problem_id, memo, created', 'safe', 'on'=>'search'),
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
			'problem' => array(self::BELONGS_TO, 'Problem', 'problem_id'),
			'exercise' => array(self::BELONGS_TO, 'Exercise', 'exercise_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'exercise_id' => 'Exercise',
			'sequence' => 'Sequence',
		
			'title' => 'Displayed Title',
			'problem_id' => 'Problem',
			'memo' => 'Memo',
			'created' => 'Created',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('exercise_id',$this->exercise_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('problem_id',$this->problem_id);
		$criteria->compare('memo',$this->memo,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}