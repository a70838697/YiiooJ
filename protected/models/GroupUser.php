<?php

/**
 * This is the model class for table "{{group_users}}".
 *
 * The followings are the available columns in table '{{group_users}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $group_id
 * @property integer $status
 * @property integer $created
 * @property integer $modified
 */
class GroupUser extends CActiveRecord
{
	const USER_STATUS_APPLIED=1;
	const USER_STATUS_ACCEPTED=2;
	public $data;
	public $score;
	public static $USER_STATUS_MESSAGES=array(
		self::USER_STATUS_APPLIED=>'Applied',
		self::USER_STATUS_ACCEPTED=>'Accepted',
	);
	/**
	 * Returns the static model of the specified AR class.
	 * @return GroupUsers the static model class
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
		return '{{group_users}}';
	}
	
	public function scopes()
    {
		$alias = $this->getTableAlias(false,false);
        return array(
            'common'=>array(
		        'select'=>array("{$alias}.status","{$alias}.created","{$alias}.id"),
		        'with'=>array('user'),
        	),
       );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, group_id, status', 'required'),
			array('user_id, group_id, status', 'numerical', 'integerOnly'=>true),
	        array('created','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'insert'),
			array('modified','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'update'),	        
	        array('created,modified','default',
	              'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
	              'setOnEmpty'=>false,'on'=>'insert'),		        
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, group_id, status, created, modified', 'safe', 'on'=>'search'),
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
       		'user' => array(self::BELONGS_TO, 'UUser', 'user_id','select'=>array('username',),'with'=>'info'),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'group_id' => 'Group',
			'status' => 'Status',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created);
		$criteria->compare('modified',$this->modified);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}