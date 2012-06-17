<?php

/**
 * This is the model class for table "{{practices}}".
 *
 * The followings are the available columns in table '{{practices}}':
 * @property integer $id
 * @property integer $status
 * @property integer $user_id
 * @property integer $created
 * @property integer $chapter_id
 * @property string $name
 * @property string $memo
 * @property integer $examination_id
 */
class Practice extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Practice the static model class
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
		return '{{practices}}';
	}
	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$examination= new Examination;
				$examination->name=$this->name;
				$examination->root=0;
				$examination->level=1;
				
				$examination->description="test";
				
				if($examination->saveNode(true))
				{
					$this->examination_id=$examination->id;
					return true;
				}
			}
		}
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
			array('name', 'required'),
			array('status, chapter_id', 'numerical', 'integerOnly'=>true),
			array('created','default',
				'value'=>new CDbExpression('UNIX_TIMESTAMP()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('user_id','default',
				'value'=>Yii::app()->user->id,
				'setOnEmpty'=>false,'on'=>'insert'),
				
			array('name, memo', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, status, user_id, created, chapter_id, name, memo, examination_id', 'safe', 'on'=>'search'),
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
			'chapter' => array(self::BELONGS_TO, 'Chapter', 'chapter_id'),
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
			'status' => 'Status',
			'user_id' => 'User',
			'created' => 'Created',
			'chapter_id' => 'Chapter',
			'name' => 'Name',
			'memo' => 'Memo',
			'examination_id' => 'Examination',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created',$this->created);
		$criteria->compare('chapter_id',$this->chapter_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('memo',$this->memo,true);
		$criteria->compare('examination_id',$this->examination_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}