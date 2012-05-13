<?php

/**
 * This is the model class for table "{{uploads}}".
 *
 * The followings are the available columns in table '{{uploads}}':
 * @property integer $id
 * @property string $filename
 * @property string $location
 * @property integer $access
 * @property integer $update_time
 * @property integer $user_id
 * @property string $ip
 * @property integer $revision
 */
class Upload extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Upload the static model class
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
		return '{{uploads}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('filename, location, access, user_id, ip', 'required'),
			array('access, revision', 'numerical', 'integerOnly'=>true),
			array('filename, location', 'length', 'max'=>255),
	        array('update_time','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'insert'), 			
	        array('update_time','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'update'), 			
	        // The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, filename, location, access, update_time, user_id, ip, revision', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'filename' => 'Filename',
			'location' => 'Location',
			'access' => 'Access',
			'update_time' => 'Update Time',
			'user_id' => 'User',
			'ip' => 'Ip',
			'revision' => 'Revision',
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
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('access',$this->access);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('revision',$this->revision);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}