<?php

/**
 * This is the model class for table "{{groups}}".
 *
 * The followings are the available columns in table '{{groups}}':
 * @property integer $id
 * @property integer $status
 * @property integer $type_id
 * @property integer $belong_to_id
 */
class Group extends CActiveRecord
{
	const GROUP_TYPE_TEAM=1;
	const GROUP_TYPE_COURSE=2;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Groups the static model class
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
		return '{{groups}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, belong_to_id', 'required'),
			array('status, type_id, belong_to_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, status, type_id, belong_to_id', 'safe', 'on'=>'search'),
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
			'userCount' => array(self::STAT, 'GroupUser', 'group_id'),		
			'users' => array(self::HAS_MANY, 'GroupUser', 'group_id'),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
			'type_id' => 'Type',
			'belong_to_id' => 'Belong To',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('belong_to_id',$this->belong_to_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}