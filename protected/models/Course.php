<?php

/**
 * This is the model class for table "{{courses}}".
 *
 * The followings are the available columns in table '{{courses}}':
 * @property integer $id
 * @property string $name
 * @property string $sequence
 * @property string $description
 * @property string $location
 * @property string $environment
 * @property string $due_time
 * @property integer $user_id
 * @property integer $begin
 * @property integer $end
 * @property integer $visibility
 * @property integer $created
 */
class Course extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Course the static model class
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
		return '{{courses}}';
	}
	/**
	 * @return string the URL that shows the detail of the course
	 */
	public function getUrl()
	{
		return Yii::app()->createUrl('course/view', array(
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
			array('title, user_id, begin, end', 'required'),
			array('visibility', 'numerical', 'integerOnly'=>true),
			array('begin', 'type', 'type'=>'date','dateFormat'=>'yyyy-MM-dd'),
			array('end', 'type', 'type'=>'date','dateFormat'=>'yyyy-MM-dd'),
			array('title', 'length', 'max'=>60),
			array('memo', 'length', 'max'=>100),			
            array('sequence', 'length', 'max'=>20),
            array('description', 'length', 'min'=>0),
            array('location', 'length', 'max'=>32),
            array('environment', 'length', 'max'=>256),
            array('due_time', 'length', 'max'=>30),
	        array('created','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'insert'),            
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, description, location, environment, due_time, user_id, begin, end, visibility, created', 'safe', 'on'=>'search'),
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
       		'user' => array(self::BELONGS_TO, 'UUser', 'user_id'),
       		'userinfo' => array(self::BELONGS_TO, 'Profile', 'user_id'),
			'myMemberShip' => array(self::HAS_ONE, 'GroupUser', '','select'=>'myMemberShip.status','on'=>' myMemberShip.group_id = t.student_group_id and myMemberShip.user_id=' . Yii::app()->user->id),
			'studentGroup' => array(self::HAS_ONE, 'Group', 'belong_to_id','on'=>'studentGroup.type_id='. (Group::GROUP_TYPE_COURSE)),
			//'studentCount' => array(self::STAT, 'GroupUser', '','select'=>'count(GroupUser.*)','condition'=>' GroupUser.user_id=t.student_group_id'),
			'experiments' => array(self::HAS_MANY, 'Experiment', 'course_id'),
			'experimentCount' => array(self::STAT, 'Experiment', 'course_id'),
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
			'sequence' => 'Course number',
			'description' => 'Description',
			'location' => 'Classroom',
			'environment' => 'Environment',
			'due_time' => 'Weekly Time',
			'user_id' => 'Teacher',
			'begin' => 'Begin',
			'end' => 'End',
			'memo' => 'Memo',
			'visibility' => 'Visibile',
			'created' => 'Created',
		);
	}
	public function scopes()
    {
		$alias = $this->getTableAlias(false,false);
    	return array(
            'recentlist'=>array(
            	'order'=>"{$alias}.created DESC",
		        'select'=>array("{$alias}.id","{$alias}.user_id","{$alias}.title","{$alias}.visibility","{$alias}.created","{$alias}.end","{$alias}.due_time","{$alias}.begin","{$alias}.memo","{$alias}.sequence","{$alias}.location","{$alias}.environment"),
        		'with'=>UUserIdentity::isStudent()? array('user:username','myMemberShip','studentGroup.userCount'):array('user:username','studentGroup.userCount'))
    			,
        	'mine'=>array(
                'condition'=>UUserIdentity::isTeacher()?
        			"{$alias}.visibility!=". UCourseLookup::COURSE_TYPE_DELETED ." AND {$alias}.user_id=".Yii::app()->user->id:
        			"{$alias}.visibility!=". UCourseLookup::COURSE_TYPE_DELETED ." AND EXISTS(select 1 from {{group_users}} as gu where gu.group_id={$alias}.student_group_id and gu.user_id= ".Yii::app()->user->id .")",
        	        	),
            'public'=>array(
            	'condition'=>"{$alias}.visibility=".UCourseLookup::COURSE_TYPE_PUBLIC,
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('environment',$this->environment,true);
		$criteria->compare('due_time',$this->due_time,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('begin',$this->begin);
		$criteria->compare('end',$this->end);
		$criteria->compare('visibility',$this->visibility);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}