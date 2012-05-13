<?php

/**
 * This is the model class for table "{{entry_revision}}".
 *
 * The followings are the available columns in table '{{entry_revision}}':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $access
 * @property integer $update_time
 * @property integer $user_id
 * @property string $ip
 * @property integer $revision
 * @property integer $accessed_time
 */
class EntryRevision extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return EntryRevision the static model class
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
		return '{{entry_revisions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, content, ip, revision', 'required'),
			array('access, update_time, ip, revision, accessed_time', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('title, content', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'content' => 'Content',
			'access' => 'Access',
			'update_time' => 'Update Time',
			'user_id' => 'User',
			'ip' => 'Ip',
			'revision' => 'Revision',
			'accessed_time' => 'Accessed Time',
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
		//$criteria->compare('update_time',$this->update_time);
		//$criteria->compare('user_id',$this->user_id);
		//$criteria->compare('ip',$this->ip,true);
		//$criteria->compare('revision',$this->revision);
		//$criteria->compare('accessed_time',$this->accessed_time);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}