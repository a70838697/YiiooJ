<?php

/**
 * This is the model class for table "{{problems}}".
 *
 * The followings are the available columns in table '{{problems}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property integer $time_limit
 * @property integer $memory_limit
 * @property string $description
 * @property string $source
 * @property string $input
 * @property string $output
 * @property string $input_sample
 * @property string $output_sample
 * @property string $hint
 * @property integer $visibility
 * @property string $created
 * @property string $modified
 */
class Problem extends CActiveRecord
{

	public $hisSubmitedCount;
	public $hisAcceptedCount;	
	
	function behaviors() {
	    return array(
	        'tags' => array(
	            'class' => 'ext.yiiext.behaviors.model.taggable.ETaggableBehavior',
	            // Table where tags are stored
	            'tagTable' => '{{tags}}',
	            // Cross-table that stores tag-model connections.
	            // By default it's your_model_tableTag
	            'tagBindingTable' => '{{problem_tags}}',
	            // Foreign key in cross-table.
	            // By default it's your_model_tableId
	            'modelTableFk' => 'problem_id',
	            // Tag table PK field
	            'tagTablePk' => 'id',
	            // Tag name field
	            'tagTableName' => 'name',
	            // Tag counter field
	            // if null (default) does not write tag counts to DB
	            'tagTableCount' => 'count',
	            // Tag binding table tag ID
	            'tagBindingTableTagId' => 'tag_id',
	            // Caching component ID. If false don't use cache.
	            // Defaults to false.
	            //'cacheID' => 'cache',
	 
	            // Save nonexisting tags.
	            // When false, throws exception when saving nonexisting tag.
	            'createTagsAutomatically' => true,
	 
	            // Default tag selection criteria
	            'scope' => array(
	                //'condition' => ' t.user_id = :user_id ',
	                //'params' => array( ':user_id' => Yii::app()->user->id ),
	            ),
	 
	            // Values to insert to tag table on adding tag
	            'insertValues' => array(
	                'user_id' => Yii::app()->user->id,
	            ),
	        )
	    );
	}	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Problem the static model class
	 */
	public $compiler_set_array=array();
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function afterConstruct()
	{
	    parent::afterConstruct();
	    $this->compiler_set=UCompilerLookup::values(-1);
	}
	/**
	 * @return array a list of links that point to the post list filtered by every tag of this post
	 */
	public function getTagLinks()
	{
		$links=array();
		foreach($this->getTags() as $tag)
			$links[]=CHtml::link(CHtml::encode($tag), array('problem/index', 'tag'=>$tag));
		return $links;
	}	
	protected function afterFind()
	{
	    parent::afterFind();
	    $this->compiler_set=UCompilerLookup::values($this->compiler_set);
	}
	protected function afterSave()
	{
	    parent::afterSave();
	    if(is_int($this->compiler_set))
	    	$this->compiler_set=UCompilerLookup::values($this->compiler_set);
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{problems}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('compiler_set','lookupComiplers'),
			array('description, title,time_limit, memory_limit, compiler_set', 'required'),
			array('user_id, time_limit, memory_limit,compiler_set,  visibility', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>512),
			array('input,input_sample,output,output_sample,hint', 'length', 'min'=>0),
			array('source', 'length', 'max'=>128),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, title, time_limit, memory_limit, description, source, input, output, input_sample, output_sample, hint,  visibility, created, modified', 'safe', 'on'=>'search'),
			array('modified','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'update'),
	        array('created,modified','default',
	              'value'=>new CDbExpression('NOW()'),
	              'setOnEmpty'=>false,'on'=>'insert'),			
		);
	}
    public function lookupComiplers($attribute,$params)
    {
    	$compiler_bit_set=0;
    	foreach ($this->compiler_set as $bit)
    	{
    		$compiler_bit_set|=$bit;
    	}
    	$validated_compiler_set=UCompilerLookup::validateCompilerSet($compiler_bit_set);
        if($validated_compiler_set==0)
        {
            $this->addError('compiler_set','Please choose at least one compiler.');
            return;
        }
    	if($validated_compiler_set==UCompilerLookup::validateCompilerSet(-1))
    	{
    		$this->compiler_set=-1;
    	}
    	else
    	{
	    	$this->compiler_set=$compiler_bit_set;
    	}
    	    	
    }
	
	public function scopes()
    {
		$alias = $this->getTableAlias(false,false);
        return array(
            'titled'=>array(
		        'select'=>array("{$alias}.title","{$alias}.id"),
        	),     
            'public'=>array(
            	'condition'=>"{$alias}.visibility=".ULookup::RECORD_STATUS_PUBLIC,
            ),
            'mine'=>array(
            	'condition'=>(Yii::app()->user->isGuest?"":"{$alias}.user_id=". Yii::app()->user->id ." and " ). "{$alias}.visibility!=".ULookup::RECORD_STATUS_DELETE,
            ),            
            'allCount'=>array(
			        'with'=>Yii::app()->user->isGuest?array('acceptedCount','submitedCount'):array('acceptedCount','submitedCount','myAcceptedCount','mySubmitedCount'),
            ),
            'myCount'=>array(
			        'with'=>array('myAcceptedCount','mySubmitedCount'),
            ),
            'mySubmited'=>array(
            	'condition'=>"exists(select 'X' from {{submitions}} where {{submitions}}.problem_id={$alias}.id and {{submitions}}.user_id=".Yii::app()->user->id.")",
            	'with'=>array('myAcceptedCount','mySubmitedCount'),
            ),
            'myAccepted'=>array(
            	'condition'=>"exists(select 'X' from {{submitions}} as cs where cs.problem_id={$alias}.id and cs.user_id=".Yii::app()->user->id." and cs.status=". ULookup::JUDGE_RESULT_ACCEPTED .")",
            	'with'=>array('myAcceptedCount','mySubmitedCount'),
            ),
            'myNotAccepted'=>array(
            	//'condition'=>"exists(select 'X' from {{submitions}} where {{submitions}}.problem_id={$alias}.id and {{submitions}}.user_id=".Yii::app()->user->id.") and not exists(select 'X' from {{submitions}} as cs where cs.problem_id={$alias}.id  and cs.user_id=".Yii::app()->user->id." and cs.status=". ULookup::JUDGE_RESULT_ACCEPTED .")",
            	'condition'=>"exists(select 'X' from {{submitions}} where {{submitions}}.problem_id={$alias}.id and {{submitions}}.user_id=".Yii::app()->user->id.") and not exists(select 'X' from {{submitions}} as cs where cs.problem_id={$alias}.id  and cs.user_id=".Yii::app()->user->id." and cs.status=". ULookup::JUDGE_RESULT_ACCEPTED .")",
            	'with'=>array('myAcceptedCount','mySubmitedCount'),
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
		$results=array(
	        'tests' => array(self::HAS_MANY, 'Test', 'problem_id'
//	            'condition'=>'comments.status='.Comment::STATUS_APPROVED,
//	            'order'=>'comments.create_time DESC'
			),
       		'user' => array(self::BELONGS_TO, 'UUser', 'user_id','select'=>array('username')),
			'submitions' => array(self::HAS_MANY, 'Submition', 'problem_id'),
	        'judger' => array(self::HAS_ONE, 'ProblemJudger', 'problem_id'),
			'submitedCount' => array(self::STAT, 'Submition', 'problem_id'),
			'acceptedCount' => array(self::STAT, 'Submition', 'problem_id','condition'=>'status='.ULookup::JUDGE_RESULT_ACCEPTED,),
		);
		if(!Yii::app()->user->isGuest)
		{
			$results['mySubmitedCount']=array(self::STAT, 'Submition', 'problem_id','condition'=>'user_id='.Yii::app()->user->id);
			$results['myAcceptedCount']=array(self::STAT, 'Submition', 'problem_id','condition'=>'status='.ULookup::JUDGE_RESULT_ACCEPTED .' and user_id='.Yii::app()->user->id,);
		}
		return $results;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'title' => 'Problem Title',
			'compiler_set' => 'Pragramming Languages',
			'time_limit' => 'Time Limit',
			'memory_limit' => 'Memory Limit',
			'description' => 'Description',
			'source' => 'Source',
			'input' => 'Input',
			'output' => 'Output',
			'input_sample' => 'Input Sample',
			'output_sample' => 'Output Sample',
			'hint' => 'Hint',
			'visibility' => 'Visibility',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('time_limit',$this->time_limit);
		$criteria->compare('memory_limit',$this->memory_limit);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('input',$this->input,true);
		$criteria->compare('output',$this->output,true);
		$criteria->compare('input_sample',$this->input_sample,true);
		$criteria->compare('output_sample',$this->output_sample,true);
		$criteria->compare('hint',$this->hint,true);
		$criteria->compare('visibility',$this->visibility);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}