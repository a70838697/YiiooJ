<?php

/**
 * This is the model class for table "{{exercises}}".
 *
 * The followings are the available columns in table '{{exercises}}':
 * @property integer $id
 * @property integer $type_id
 * @property integer $belong_to_id
 * 
 */
class Exercise extends CActiveRecord
{
	const EXERCISE_TYPE_COLLECTION=1;
	const EXERCISE_TYPE_COURSE=2;	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Exercise the static model class
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
		return '{{exercises}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id', 'required'),
			array('type_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type_id', 'safe', 'on'=>'search'),
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
			'exercise_problems' => array(self::HAS_MANY, 'ExerciseProblem', 'exercise_id'),
			'experiment' => array(self::BELONGS_TO, 'Experiment', 'belong_to_id'),
		);
		
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type_id' => 'Type',
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
		$criteria->compare('type_id',$this->type_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}