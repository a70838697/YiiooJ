<?php

/**
 * This is the model class for table "{{multiple_choices}}".
 *
 * The followings are the available columns in table '{{multiple_choices}}':
 * @property integer $id
 * @property string $description
 * @property integer $user_id
 * @property integer $create_time
 * @property integer $update_time
 * @property string $answer
 * @property integer $more_than_one_answer
 */
class MultipleChoice extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MultipleChoice the static model class
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
		return '{{multiple_choices}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description,answer', 'required'),
			array('more_than_one_answer', 'numerical', 'integerOnly'=>true),
			array('answer', 'length', 'max'=>255),
			array('create_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('update_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>array('update','insert')),
			array('user_id','default',
				'value'=>Yii::app()->user->id,
				'setOnEmpty'=>false,'on'=>'insert'),				
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, user_id, create_time, update_time, answer', 'safe', 'on'=>'search'),
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
			'choiceOptions' => array(self::HAS_MANY, 'ChoiceOption', 'multiple_choice_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'more_than_one_answer'=> 'The problem have more than one answer',
			'description' => 'Description',
			'user_id' => 'User',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'answer' => 'Answer',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('answer',$this->answer,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}