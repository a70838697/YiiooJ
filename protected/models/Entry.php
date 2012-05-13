<?php

/**
 * This is the model class for table "{{entries}}".
 *
 * The followings are the available columns in table '{{entries}}':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $access
 * @property integer $user_id
 * @property integer $ip
 * @property integer $revision
 * @property integer $create_time
 * @property integer $update_time
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property EntryRevisions[] $entryRevisions
 */
class Entry extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Entry the static model class
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
		return '{{entries}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, content', 'required'),
			array('access, create_time, update_time', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('revision','default',
				'value'=>1,
				'setOnEmpty'=>false,'on'=>'insert'),				
			array('create_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('update_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>array('update','insert')),
				// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('id, title, content, access, user_id, ip, revision, create_time, update_time', 'safe', 'on'=>'search'),
			array('title, content', 'safe', 'on'=>'search'),
		);
	}
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->ip=UCApp::getIpAsInt();
				$this->user_id=Yii::app()->user->id;
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
			'entryRevisions' => array(self::HAS_MANY, 'EntryRevision', 'entry_id'),
		);
	}

	/**
	 * @return string the URL that shows the detail of the post
	 */
	public function getUrl()
	{
		return Yii::app()->createUrl('entry/view', array(
//			'id'=>$this->id,
			'title'=>$this->title,
		));
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'content' => 'Content',
			'access' => 'Access',
			'user_id' => 'User',
			'ip' => 'Ip',
			'revision' => 'Revision',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
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

		//$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		//$criteria->compare('access',$this->access);
		//$criteria->compare('user_id',$this->user_id);
		//$criteria->compare('ip',$this->ip,true);
		//$criteria->compare('revision',$this->revision);
		//$criteria->compare('create_time',$this->create_time);
		//$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}