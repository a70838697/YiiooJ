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
 * @property int $score
 * @property int $status
 * @property string $comment
 * @property integer $created
 * @property integer $updated
 */
class ExperimentReport extends CActiveRecord
{
	const STATUS_NORMAL=0;
	const STATUS_ALLOW_EDIT=1;
	const STATUS_SUBMITIED=2;
	const STATUS_ALLOW_LATE_EDIT=4;
	const STATUS_LATE_SUBMITTED=8;
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
	
	public function canScore()
	{
		if($this->isNewRecord)return false;
		if($this->status==self::STATUS_NORMAL && !($this->experiment->isTimeOut()))return false;
		if($this->status!=self::STATUS_SUBMITIED && $this->status!=self::STATUS_LATE_SUBMITTED
			&& !($this->status==self::STATUS_NORMAL && $this->experiment->isTimeOut()))
			return false;
		if(UUserIdentity::isAdmin())return true;
		if(UUserIdentity::isTeacher()) return (Yii::app()->user->id==$this->experiment->classRoom->user_id);
		return false;
	}	
	public function canExtend()
	{
		if($this->isNewRecord)return false;
		if($this->status==self::STATUS_NORMAL && !($this->experiment->isTimeOut()) ) return false;
		if($this->status==self::STATUS_ALLOW_EDIT || $this->status==self::STATUS_ALLOW_LATE_EDIT ) return false;
		if(UUserIdentity::isAdmin())return true;
		if(UUserIdentity::isTeacher()) return (Yii::app()->user->id==$this->experiment->classRoom->user_id);
		return false;
	}
	public function canEdit()
	{
		if($this->isNewRecord)return false;
		if(UUserIdentity::isAdmin())return true;
		if(UUserIdentity::isTeacher()) return (Yii::app()->user->id==$this->experiment->classRoom->user_id);
		if($this->user_id==Yii::app()->user->id){
			if($this->status==self::STATUS_NORMAL && !($this->experiment->isTimeOut()) ) return true;
			if($this->status==self::STATUS_ALLOW_EDIT || $this->status==self::STATUS_ALLOW_LATE_EDIT) return true;
		}
		return false;
	}
	public function canSubmit(){
		if($this->status==self::STATUS_SUBMITIED || $this->status==self::STATUS_LATE_SUBMITTED)
			return false;
		if(UUserIdentity::isAdmin())return true;
		if(UUserIdentity::isTeacher()) return (Yii::app()->user->id==$this->experiment->classRoom->user_id);
		if($this->user_id==Yii::app()->user->id) return true;
		return false;
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
			array('experiment_id,status', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>500),
			array('score', 'numerical', 'integerOnly'=>true, 'min'=>0, 'max'=>100),
			//array('updated','default',
	        //      'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	        //      'setOnEmpty'=>false,'on'=>'update'),	        
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
	/**
	 * @return how many person finished the experiment before me.
	 */
	public function getFinishRank()
	{
		return ExperimentReport::model()->count("experiment_id=:experiment_id and updated<:updated",
			array(":experiment_id"=>$this->experiment_id,":updated"=>$this->updated))+1;
	}
	
}