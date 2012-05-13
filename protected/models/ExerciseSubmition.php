<?php

/**
 * This is the model class for table "{{submitions}}".
 *
 * The followings are the available columns in table '{{submitions}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $problem_id
 * @property integer $exercise_id
 * @property string $source
 * @property string $result
 * @property integer $used_time
 * @property integer $used_memory
 * @property integer $status
 * @property integer $compiler_id
 * @property string $created
 * @property string $modified
 */
class ExerciseSubmition extends CActiveRecord
{
	public $code_length;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Submition the static model class
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
		return '{{submitions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, problem_id, source, compiler_id', 'required'),
			array('user_id, problem_id, exercise_id, used_time, used_memory, status, compiler_id', 'numerical', 'integerOnly'=>true),
			array('result', 'length', 'max'=>500),

			array('modified','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'update'),
	        array('created,modified','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'insert'),
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, problem_id, exercise_id, source, result, used_time, used_memory, status, compiler_id, created, modified', 'safe', 'on'=>'search'),
		);
	}
	public $mySubmitedCount;
	public function scopes()
    {
		$alias = $this->getTableAlias(false,false);
    	return array(
            'list'=>array(
		        'select'=>array("{$alias}.id","LENGTH({$alias}.source) AS code_length","{$alias}.user_id","{$alias}.problem_id","{$alias}.status","{$alias}.created","{$alias}.used_time","{$alias}.used_memory","{$alias}.compiler_id","{$alias}.result"),
        		'with'=>array(
        			'user:username',
        			'problem:titled',
        		),
        	),
            'recent'=>array(
            	'order'=>"{$alias}.created DESC",
        	),
        	
        	'mine'=>array(
                'condition'=>Yii::app()->user->isGuest?
        			"{$alias}.visibility=".ULookup::RECORD_STATUS_PUBLIC :
        			("{$alias}.user_id=".Yii::app()->user->id ." AND {$alias}.visibility!=".ULookup::RECORD_STATUS_PRIVATE),
        	),
            'accepted'=>array(
            	'condition'=>"{$alias}.status=".ULookup::JUDGE_RESULT_ACCEPTED,
            ),
            'public'=>array(
            	'condition'=>"{$alias}.visibility=".ULookup::RECORD_STATUS_PUBLIC,
            ),
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
		'user' => array(self::BELONGS_TO, 'UUser', 'user_id','select'=>array('username')),
		'exercise' => array(self::BELONGS_TO, 'Exercise', 'exercise_id'),
		'problem' => array(self::BELONGS_TO, 'Problem', 'problem_id','select'=>array('title','compiler_set'),'joinType'=>'INNER JOIN'),
		);
	}
	public function getUrl($model=null)
	{
		if($model===null) return Yii::app()->createUrl('submition/view', array(
			'id'=>$this->id,
			//'title'=>$this->name,
		));
		//	$course=$this->course;
		return $model->url.'#c'.$this->id;
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'problem_id' => 'Problem',
			'exercise_id' => 'Exercise',
			'source' => 'Source',
			'result' => 'Message',
			'used_time' => 'Used Time',
			'used_memory' => 'Used Memory',
			'status' => 'Status',
			'compiler_id' => 'Language',
			'code_length'=>'Code Len.',
			'created' => 'Submiting time',
			'modified' => 'Re-submiting time',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('problem_id',$this->problem_id);
		$criteria->compare('exercise_id',$this->exercise_id);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('result',$this->result,true);
		$criteria->compare('used_time',$this->used_time);
		$criteria->compare('used_memory',$this->used_memory);
		$criteria->compare('status',$this->status);
		$criteria->compare('compiler_id',$this->compiler_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}