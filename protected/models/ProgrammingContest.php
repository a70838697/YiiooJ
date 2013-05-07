<?php

/**
 * This is the model class for table "{{programming_contests}}".
 *
 * The followings are the available columns in table '{{programming_contests}}':
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property integer $user_group_id
 * @property string $begin
 * @property string $end
 * @property integer $exercise_id
 * @property integer $created
 * @property integer $modified
 * @property string $description
 * @property integer $application_option
 * @property integer $visibility
 */
class ProgrammingContest extends CActiveRecord
{
	const STUDENT_APPLICATION_OPTION_APPROVE=0;
	const STUDENT_APPLICATION_OPTION_ALLOW=1;
	const STUDENT_APPLICATION_OPTION_DENY=9;
	
	public static function getApplicationOptionMessage()
	{
		$a=array();
		$a[self::STUDENT_APPLICATION_OPTION_APPROVE]=Yii::t('course','Approve by teacher');
		$a[self::STUDENT_APPLICATION_OPTION_ALLOW]=Yii::t('course','Approve automatically');
		$a[self::STUDENT_APPLICATION_OPTION_DENY]=Yii::t('course','Deny');
		return $a;
	}	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProgrammingContest the static model class
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
		return '{{programming_contests}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('begin, end, application_option', 'required'),
			array('exercise_id, application_option, visibility', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>512),
			array('description', 'length', 'max'=>1024),
			
			array('user_id','default',
					'value'=>Yii::app()->user->id,
					'setOnEmpty'=>false,'on'=>'insert'),
			array('created,modified','default',
					'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
					'setOnEmpty'=>false,'on'=>'insert'),			
			array('modified','default',
					'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
					'setOnEmpty'=>false,'on'=>'update'),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, user_id, user_group_id, begin, end, exercise_id, created, modified, description, application_option, visibility', 'safe', 'on'=>'search'),
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
			'exercise' => array(self::BELONGS_TO, 'Exercise', 'exercise_id', 'with'=>'exercise_problems'),
			'user' => array(self::BELONGS_TO, 'UUser', 'user_id'),
			'userinfo' => array(self::BELONGS_TO, 'Profile', 'user_id'),
			'myMemberShip' => array(self::HAS_ONE, 'GroupUser', '','select'=>'myMemberShip.status','on'=>' myMemberShip.group_id = t.user_group_id and myMemberShip.user_id=' . Yii::app()->user->id),
			'studentGroup' => array(self::BELONGS_TO, 'Group', 'user_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'user_id' => 'User',
			'user_group_id' => 'User Group',
			'begin' => 'Begin',
			'end' => 'End',
			'exercise_id' => 'Exercise',
			'created' => 'Created',
			'modified' => 'Modified',
			'description' => 'Description',
			'application_option' => 'Application Option',
			'visibility' => 'Visibility',
		);
	}
	public function isAfterMatch()
	{
		$timezone = "Asia/Chongqing";
		if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
	
		$nowt= time();
		$end_date=CDateTimeParser::parse($this->end,"yyyy-MM-dd hh:mm:ss") ;
		return ($nowt>$end_date);
	}
	public function isTimeOut()
	{
		$timezone = "Asia/Chongqing";
		if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
	
		$nowt= time();
		$begin_date=CDateTimeParser::parse($this->begin,"yyyy-MM-dd hh:mm:ss") ;
		$end_date=CDateTimeParser::parse($this->end,"yyyy-MM-dd hh:mm:ss") ;
		return ($nowt>$end_date || $nowt<$begin_date);
	}
	public function denyStudent()
	{
		if(UUserIdentity::isStudent())
		{
			$timezone = "Asia/Chongqing";
			$date = time();
			$begin_date=CDateTimeParser::parse($this->begin,"yyyy-MM-dd hh:mm:ss") ;
			return  ($date-$begin_date>0);
		}
		return false;
	}	

	public function scopes()
	{
		$alias = $this->getTableAlias(false,false);
		return array(
				'recentlist'=>array(
						'order'=>"{$alias}.created DESC",
						'select'=>array("{$alias}.id","{$alias}.user_id","{$alias}.name","{$alias}.visibility","{$alias}.created","{$alias}.end","{$alias}.begin","{$alias}.description"),
						'with'=>UUserIdentity::isStudent()? array('user:username','myMemberShip','studentGroup.userCount'):array('user:username','studentGroup.userCount'))
						,
						'mine'=>array(
								'condition'=>UUserIdentity::isTeacher()?
								"{$alias}.visibility!=". UClassRoomLookup::CLASS_ROOM_TYPE_DELETED ." AND {$alias}.user_id=".Yii::app()->user->id:
								"{$alias}.visibility!=". UClassRoomLookup::CLASS_ROOM_TYPE_DELETED ." AND EXISTS(select 1 from {{group_users}} as gu where gu.group_id={$alias}.user_group_id and gu.user_id= ".Yii::app()->user->id .")",
						),
						'term'=>array(
								'condition'=>"{$alias}.end>=NOW() and {$alias}.begin<=NOW()",
						),
						'public'=>array(
								'condition'=>"{$alias}.visibility=".UClassRoomLookup::CLASS_ROOM_TYPE_PUBLIC,
						),
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('user_group_id',$this->user_group_id);
		$criteria->compare('begin',$this->begin,true);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('exercise_id',$this->exercise_id);
		$criteria->compare('created',$this->created);
		$criteria->compare('modified',$this->modified);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('application_option',$this->application_option);
		$criteria->compare('visibility',$this->visibility);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}