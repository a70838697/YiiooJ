<?php

/**
 * This is the model class for table "{{courses}}".
 *
 * The followings are the available columns in table '{{courses}}':
 * @property integer $id
 * @property string $title
 * @property string $sequence
 * @property string $description
 * @property string $memo
 * @property integer $user_id
 * @property integer $visibility
 * @property string $chapter_id
 * @property integer $created
 * @property integer $flags
 */
class Course extends CActiveRecord
{
	const COURSE_OPTION_HAS_MATH_FORMULA=8;
	
	private function setFlagStat($bit,$set){
		if($set)$this->flags |=$bit;
		else $this->flags &= (~$bit);
	}
	public function getHasMathFormula()
	{
		return  ($this->flags & self::COURSE_OPTION_HAS_MATH_FORMULA)>0;
	}
	public function setHasMathFormula($value)
	{
		$this->setFlagStat(self::COURSE_OPTION_HAS_MATH_FORMULA,$value);
	}	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
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
			array('title', 'required'),
			array('hasMathFormula', 'boolean'),
			array('visibility', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>60),
			array('sequence', 'length', 'max'=>20),
			array('memo', 'length', 'max'=>100),
			array('description', 'length', 'min'=>0),
			array('user_id','default',
				'value'=>Yii::app()->user->id,
				'setOnEmpty'=>false,'on'=>'insert'),
			array('flags','default',
				'value'=>0,
				'setOnEmpty'=>true,'on'=>'insert'),
			array('created','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, sequence, description, memo, user_id, visibility, chapter_id, created', 'safe', 'on'=>'search'),
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
			'book' => array(self::BELONGS_TO, 'Chapter', 'chapter_id'),
			'classRooms' => array(self::HAS_MANY, 'ClassRoom', 'course_id','order'=>'created desc'),
			'myMemberShip' => array(self::HAS_ONE, 'GroupUser', '','select'=>'myMemberShip.status','on'=>' myMemberShip.group_id = t.user_group_id and myMemberShip.user_id=' . Yii::app()->user->id),
			'userGroup' => array(self::HAS_ONE, 'Group', 'belong_to_id','on'=>'userGroup.type_id='. (Group::GROUP_TYPE_COURSE_BUILDER)),
			//'studentCount' => array(self::STAT, 'GroupUser', '','select'=>'count(GroupUser.*)','condition'=>' GroupUser.user_id=t.user_group_id'),

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
			'memo' => 'Memo',
			'user_id' => 'Creator',
			'visibility' => 'Visible',
			'created' => 'Created',
			'hasMathFormula'=>'Support Latex math formula',
		);
	}

	public function beforeSave(){
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$chapter=new Chapter;
				$chapter->root=0;
				$chapter->level=1;
				$chapter->name=$this->title;
				$chapter->description=$this->description;
				if($chapter->saveNode(true))
				{
					$this->chapter_id=$chapter->id;
					return true;
				}
			}
			else return true;
		}
		return false;
	}
	public function scopes()
	{
		$alias = $this->getTableAlias(false,false);
		return array(
			'recentlist'=>array(
				'order'=>"{$alias}.created DESC",
				'select'=>array("{$alias}.id","{$alias}.user_id","{$alias}.title","{$alias}.visibility","{$alias}.created","{$alias}.memo","{$alias}.sequence"),
				'with'=>UUserIdentity::isStudent()? array('user:username','myMemberShip','userGroup.userCount'):array('user:username','userGroup.userCount'))
				,
				'mine'=>array(
					'condition'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin()?
					"{$alias}.visibility!=". UCourseLookup::COURSE_TYPE_DELETED ." AND ({$alias}.user_id=".Yii::app()->user->id." OR EXISTS(select 1 from {{group_users}} as gu where gu.group_id={$alias}.user_group_id and gu.user_id= ".Yii::app()->user->id ."))":
					"{$alias}.visibility!=". UCourseLookup::COURSE_TYPE_DELETED ." AND {$alias}.user_id=".Yii::app()->user->id,
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
		$criteria->compare('sequence',$this->sequence,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('memo',$this->memo,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('visibility',$this->visibility);
		$criteria->compare('chapter_id',$this->chapter_id,true);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}