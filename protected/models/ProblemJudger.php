<?php

/**
 * This is the model class for table "{{problem_judgers}}".
 *
 * The followings are the available columns in table '{{problem_judgers}}':
 * @property integer $id
 * @property integer $problem_id
 * @property string $source
 * @property integer $user_id
 * @property integer $compiler_id
 * @property string $created
 */
class ProblemJudger extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ProblemJudger the static model class
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
		return '{{problem_judgers}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('problem_id, source, user_id, compiler_id', 'required'),
			array('problem_id, user_id, compiler_id', 'numerical', 'integerOnly'=>true),
	        array('created','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'insert'),				
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, problem_id, source, user_id, compiler_id, created', 'safe', 'on'=>'search'),
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
       		'problem' => array(self::BELONGS_TO, 'Problem', 'problem_id','select'=>array('title','id')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'problem_id' => 'Problem',
			'source' => 'Source',
			'user_id' => 'User',
			'compiler_id' => 'Compiler',
			'created' => 'Created',
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
		$criteria->compare('problem_id',$this->problem_id);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('compiler_id',$this->compiler_id);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}