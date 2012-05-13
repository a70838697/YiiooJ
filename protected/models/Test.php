<?php

/**
 * This is the model class for table "{{tests}}".
 *
 * The followings are the available columns in table '{{tests}}':
 * @property integer $id
 * @property integer $problem_id
 * @property string $input
 * @property integer $input_size
 * @property string $output
 * @property integer $output_size
 * @property integer $user_id
 * @property string $description
 * @property string $created
 * @property string $modified
 */
class Test extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Test the static model class
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
		return '{{tests}}';
	}
	
	public function afterSave()
	{
		parent::afterSave();
		$connection=Yii::app()->db;
		$command=$connection->createCommand("update {{submitions}} set status=0 where problem_id=".$this->problem_id);
		$command->execute();
		return true;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('output_length','setlength'),		
			array('problem_id, input, output', 'required'),
			array('problem_id, input_size, output_size, user_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'min'=>0),
			array('modified','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'update'),
	        array('created,modified','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'insert'),						
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, problem_id, input, input_size, output, output_size, user_id, description, created, modified', 'safe', 'on'=>'search'),
		);
	}
    public function setlength($attribute,$params)
    {
    	$this->output_size=strlen($this->output);
    	$this->input_size=strlen($this->input);
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
			'problem' => array(self::BELONGS_TO, 'Problem', 'problem_id','select'=>array('title','compiler_set','visibility','user_id'),'joinType'=>'INNER JOIN'),
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
			'input' => 'Input',
			'input_size' => 'Input Size',
			'output' => 'Output',
			'output_size' => 'Output Size',
			'user_id' => 'User',
			'description' => 'Description',
			'created' => 'Created',
			'modified' => 'Modified',
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
		$criteria->compare('input',$this->input,true);
		$criteria->compare('input_size',$this->input_size);
		$criteria->compare('output',$this->output,true);
		$criteria->compare('output_size',$this->output_size);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}