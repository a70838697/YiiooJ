<?php

/**
 * This is the model class for table "{{quizzes}}".
 *
 * The followings are the available columns in table '{{quizzes}}':
 * @property integer $id
 * @property integer $status
 * @property integer $user_id
 * @property integer $created
 * @property integer $class_room_id
 * @property string $name
 * @property string $memo
 * @property integer $practice_id
 * @property integer $quiz_type
 * @property string $begin
 * @property string $end
 */
class Quiz extends CActiveRecord
{
	public function afterDeadline()
	{
		$timezone = "Asia/Chongqing";
		$nowt=CDateTimeParser::parse(date("Y-m-d H:i:s"),"yyyy-MM-dd hh:mm:ss");
		$end_date=CDateTimeParser::parse($this->end,"yyyy-MM-dd hh:mm:ss") ;
		return ($nowt>$end_date)?1:0;
	}
	
	public function isTimeOut()
	{
		$timezone = "Asia/Chongqing";
		if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);

		$nowt=CDateTimeParser::parse(date("Y-m-d H:i:s"),"yyyy-MM-dd hh:mm:ss");
		$begin_date=CDateTimeParser::parse($this->begin,"yyyy-MM-dd hh:mm:ss") ;
		$end_date=CDateTimeParser::parse($this->end,"yyyy-MM-dd hh:mm:ss") ;
		return ($nowt>$end_date || $nowt<$begin_date);
	}
		
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Quiz the static model class
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
		return '{{quizzes}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('class_room_id, name, practice_id', 'required'),
			array('status, user_id, created, class_room_id, practice_id, quiz_type', 'numerical', 'integerOnly'=>true),
			array('name, memo', 'length', 'max'=>255),
			array('begin, end', 'safe'),
			array('created','default',
					'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
					'setOnEmpty'=>false,'on'=>'insert'),
			array('user_id','default',
					'value'=>Yii::app()->user->id,
					'setOnEmpty'=>false,'on'=>'insert'),
			
			array('name, memo', 'length', 'max'=>255),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, status, user_id, created, class_room_id, name, memo, practice_id, quiz_type, begin, end', 'safe', 'on'=>'search'),
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
			'classRoom' => array(self::BELONGS_TO, 'ClassRoom', 'class_room_id'),
			'practice' => array(self::BELONGS_TO, 'Practice', 'practice_id'),
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
			'user_id' => 'User',
			'created' => 'Created',
			'class_room_id' => 'Class Room',
			'name' => 'Name',
			'memo' => 'Memo',
			'practice_id' => 'Practice',
			'quiz_type' => 'Quiz Type',
			'begin' => 'Begin',
			'end' => 'End',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created',$this->created);
		$criteria->compare('class_room_id',$this->class_room_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('memo',$this->memo,true);
		$criteria->compare('practice_id',$this->practice_id);
		$criteria->compare('quiz_type',$this->quiz_type);
		$criteria->compare('begin',$this->begin,true);
		$criteria->compare('end',$this->end,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}