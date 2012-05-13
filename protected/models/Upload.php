<?php

/**
 * This is the model class for table "{{uploads}}".
 *
 * The followings are the available columns in table '{{uploads}}':
 * @property integer $id
 * @property string $filename
 * @property integer $filesize
 * @property string $location
 * @property integer $access
 * @property integer $create_time
 * @property integer $user_id
 * @property integer $ip
 * @property integer $revision
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class Upload extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
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
			array('filename, filesize, location, access', 'required'),
			array('filesize, access, revision', 'numerical', 'integerOnly'=>true),
			array('filename, location', 'length', 'max'=>255),
			array('create_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('create_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'update'),
			//array('ip', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, filename, filesize, location, access, create_time, user_id, ip, revision', 'safe', 'on'=>'search'),
		);
	}
	/**
	 * set initial value
	 * @return true or false.
	 */
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->user_id=Yii::app()->user->id;
				$this->ip=UCApp::getIpAsInt();
			}
			return true;
		}
		else
			return false;
	}	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
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
			'filesize' => 'Filesize',
			'location' => 'Location',
			'access' => 'Access',
			'create_time' => 'Create Time',
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
		$criteria->compare('filesize',$this->filesize);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('access',$this->access);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('revision',$this->revision);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}