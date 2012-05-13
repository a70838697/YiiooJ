<?php

/**
 * This is the model class for table "{{experiment_reports}}".
 *
 * The followings are the available columns in table '{{experiment_reports}}':
 * @property integer $id
 * @property integer $experiment_id
 * @property integer $user_id
 * @property string $report
 * @property string $conclusion
 * @property integer $created
 * @property integer $updated
 */
class ExperimentReport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ExperimentReport the static model class
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
		return '{{experiment_reports}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('experiment_id, report, conclusion', 'required'),
			array('experiment_id, score', 'numerical', 'integerOnly'=>true),
			array('updated','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'update'),	        
	        array('created,updated','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'insert'),				
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, experiment_id, user_id, report, conclusion, created, updated', 'safe', 'on'=>'search'),
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
			'experiment' => array(self::BELONGS_TO, 'Experiment', 'experiment_id'),
			'user' => array(self::BELONGS_TO, 'UUser', 'user_id'),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'experiment_id' => 'Experiment',
			'user_id' => 'User',
			'report' => 'Report',
			'conclusion' => 'Conclusion',
			'created' => 'Created',
			'updated' => 'Updated',
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
		$criteria->compare('experiment_id',$this->experiment_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('report',$this->report,true);
		$criteria->compare('conclusion',$this->conclusion,true);
		$criteria->compare('created',$this->created);
		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}