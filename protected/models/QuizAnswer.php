<?php

/**
 * This is the model class for table "{{quiz_answers}}".
 *
 * The followings are the available columns in table '{{quiz_answers}}':
 * @property integer $id
 * @property integer $quiz_id
 * @property integer $examination_id
 * @property string $answer
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $user_id
 * @property double $score
 * @property integer $reviewer_id
 * @property string $review
 * @property integer $review_time
 */
class QuizAnswer extends CActiveRecord
{
	public $answer_var;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QuizAnswer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	public function checkAnswer($examination=null)
	{
		if($examination==null) $examination=$this->examination;
		if($examination->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE)
		{
			$this->makeReview(0, $this->multiple_choice_problem->answer==$this->answer?$this->examination->score:0);
		}
		
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{quiz_answers}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quiz_id, examination_id, answer', 'required'),
			array('quiz_id, examination_id, user_id', 'numerical', 'integerOnly'=>true),
			array('score', 'numerical'),
			array('create_time, update_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('update_time','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('user_id','default',
				'value'=>Yii::app()->user->id,
				'setOnEmpty'=>false,'on'=>'insert'),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, quiz_id, examination_id, answer, create_time, update_time, user_id, score, reviewer_id, review, review_time', 'safe', 'on'=>'search'),
		);
	}
	

	public function makeReview($user_id,$score)
	{
		$this->reviewer_id=$user_id;
		$this->score=$score;
		$this->review_time=new CDbExpression('UNIX_TIMESTAMP()');
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
			}
			else 
			{
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
			'examination' => array(self::BELONGS_TO, 'Examination', 'examination_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'quiz_id' => 'Quiz',
			'examination_id' => 'Examination',
			'answer' => 'Answer',
			'answer_var' => 'Answer',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'user_id' => 'User',
			'score' => 'Score',
			'reviewer_id' => 'Reviewer',
			'review' => 'Review',
			'review_time' => 'Review Time',
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
		$criteria->compare('quiz_id',$this->quiz_id);
		$criteria->compare('examination_id',$this->examination_id);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('score',$this->score);
		$criteria->compare('reviewer_id',$this->reviewer_id);
		$criteria->compare('review',$this->review,true);
		$criteria->compare('review_time',$this->review_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}