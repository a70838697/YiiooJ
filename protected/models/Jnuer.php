<?php

/**
 * This is the model class for table "{{jnuers}}".
 *
 * The followings are the available columns in table '{{jnuers}}':
 * @property integer $user_id
 * @property integer $first_year
 * @property integer $status
 * @property integer $unit_id
 * @property string $identitynumber
 */
class Jnuer extends CActiveRecord
{
	const JNUER_STATUS_APPLIED=0;
	const JNUER_STATUS_ACCEPTED=1;
	const JNUER_STATUS_REJECTED=2;
	public static $USER_STATUS_MESSAGES=array(
		self::JNUER_STATUS_APPLIED=>'Applied',
		self::JNUER_STATUS_ACCEPTED=>'Accepted',
		self::JNUER_STATUS_REJECTED=>'Rejected',
	);	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Jnuer the static model class
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
		return '{{jnuers}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, first_year,identitynumber,unit_id', 'required'),
			array('user_id, first_year, status, unit_id', 'numerical', 'integerOnly'=>true),
			array('identitynumber', 'length', 'max'=>40),
			array('identitynumber', 'unique', 'message' => ("This user's identitynumber already exists.")),
			array('identitynumber', 'length', 'min'=>7),
			array('first_year','validateYear'),
						
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, first_year, status, unit_id, identitynumber', 'safe', 'on'=>'search'),
		);
	}
    public function validateYear($attribute,$params)
    {
    	if($this->first_year<1990||$this->first_year>(int)date("Y")){
            $this->addError('first_year','The year you entered is not validate.');
            return;
    	}
    }	

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'unit' => array(self::BELONGS_TO, 'Organization', 'unit_id'),		
			'user' => array(self::BELONGS_TO, 'UUser', 'user_id'),		
			'profile' => array(self::BELONGS_TO, 'UProfile', 'user_id'),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'first_year' => 'The First Year in JNU',
			'status' => 'Status',
			'unit_id' => 'Major/Unit',
			'identitynumber' => 'Student / Teacher number',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('first_year',$this->first_year);
		$criteria->compare('status',$this->status);
		$criteria->compare('unit_id',$this->unit_id);
		$criteria->compare('identitynumber',$this->identitynumber,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}