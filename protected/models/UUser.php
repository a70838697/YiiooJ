<?php

/**
 * This is the model class for table "{{Users}}".
 *
 * The followings are the available columns in table '{{Users}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $activkey
 * @property integer $createtime
 * @property integer $lastvisit
 * @property integer $superuser
 * @property integer $status
 */
class UUser extends User
{
	public $acceptedProblemCount;
	public $submissionCount;
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return '{{Users}}';
	}

	/**
	 * @return array validation rules for model attributes.
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, email', 'required'),
			array('createtime, lastvisit, superuser, status', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>20),
			array('password, email, activkey', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, email, activkey, createtime, lastvisit, superuser, status', 'safe', 'on'=>'search'),
		);
	}
	 */
	public function scopes()
    {
		$alias = $this->getTableAlias(false,false);
        return array(
            'username'=>array(
		        'select'=>array("{$alias}.username"),
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
			'submitedCount' => array(self::STAT, 'Submition', 'user_id'),
			'acceptedCount' => array(self::STAT, 'Submition', 'user_id','condition'=>'t.status='.ULookup::JUDGE_RESULT_ACCEPTED),
			'submitedProbCount' => array(self::STAT, 'Submition', 'user_id','select'=>'count(DISTINCT(t.problem_id))'),
			'acceptedProbCount' => array(self::STAT, 'Submition', 'user_id','select'=>'count(DISTINCT(t.problem_id))', 'condition'=>'t.status='.ULookup::JUDGE_RESULT_ACCEPTED),
			'info' => array(self::HAS_ONE, 'UProfile', 'user_id','select'=>'firstname,lastname'),
			'jnuer' => array(self::HAS_ONE, 'Jnuer', 'user_id','select'=>'identitynumber,unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'activkey' => 'Activkey',
			'createtime' => 'Registered on',
			'lastvisit' => 'Last visitation on',
			'superuser' => 'Superuser',
			'status' => 'Status',
			'submitedCount' => 'Submition Count',
			'acceptedCount' => 'Accepted Count',
			'submitedProbCount' => 'Submited Problem Count',
			'acceptedProbCount' => 'Accepted Problem Count',
		);
	}
	public function defaultScope()
    {
        return array(
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('activkey',$this->activkey,true);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('lastvisit',$this->lastvisit);
		$criteria->compare('superuser',$this->superuser);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}