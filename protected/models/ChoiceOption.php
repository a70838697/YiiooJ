<?php

/**
 * This is the model class for table "{{choice_options}}".
 *
 * The followings are the available columns in table '{{choice_options}}':
 * @property integer $id
 * @property integer $multiple_choice_id
 * @property string $description
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $user_id
 * @property integer $isAnswer
 */
class ChoiceOption extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ChoiceOption the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public $isAnswer;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{choice_options}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description', 'required'),
			array('isAnswer','length', 'min'=>0),
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
			array('id, multiple_choice_id, description, create_time, update_time, user_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ksAnswer' => 'is it an answer',
			'id' => 'ID',
			'multiple_choice_id' => 'Multiple Choice',
			'description' => 'Description',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'user_id' => 'User',
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
		$criteria->compare('multiple_choice_id',$this->multipe_choice_id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('user_id',$this->user_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}