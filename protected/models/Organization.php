<?php

/**
 * This is the model class for table "Organizations".
 *
 * The followings are the available columns in table 'Organizations':
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 */
class Organization extends CActiveRecord
{
	const ORGANIZATION_TYPE_UNIVERSITY=1;
	const ORGANIZATION_TYPE_SCHOOLE=2;
	const ORGANIZATION_TYPE_DEPARTMENT=3;
	const ORGANIZATION_TYPE_MAJOR=4;
	const ORGANIZATION_TYPE_CLASS=5;
	public static $USER_STATUS_MESSAGES=array(
		self::ORGANIZATION_TYPE_UNIVERSITY=>'University',
		self::ORGANIZATION_TYPE_SCHOOLE=>'School',
		self::ORGANIZATION_TYPE_DEPARTMENT=>'Department',
		self::ORGANIZATION_TYPE_MAJOR=>'Major',
		self::ORGANIZATION_TYPE_CLASS=>'Class',
		);	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Organization the static model class
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
		return '{{organizations}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('title', 'length', 'max'=>100),
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, root, lft, rgt, level', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
		);
	}
    public function behaviors(){
        return array(
            'tree' => array(
                'class' => 'ext.yiiext.behaviors.model.trees.ENestedSetBehavior',
                // store multiple trees in one table
                'hasManyRoots' => true,
                // where to store each tree id. Not used when $hasManyRoots is false
                'rootAttribute' => 'root',
                // required fields
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'levelAttribute' => 'level',
            ),
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('root',$this->root,true);
		$criteria->compare('lft',$this->lft,true);
		$criteria->compare('rgt',$this->rgt,true);
		$criteria->compare('level',$this->level);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}