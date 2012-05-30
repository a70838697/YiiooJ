<?php
class TestFiles extends MyActiveRecord
{
    public $input_file;
    public $output_file;
    // ... other attributes
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
		preg_match("/dbname=([^;]+)/i", $this->dbConnection->connectionString, $matches);
		return $matches[1].'.{{tests}}';
	}
    public function rules()
    {
        return array(
            array('input_file', 'file', 'types'=>'txt,dat,in'),
            array('output_file', 'file', 'types'=>'txt,dat,out'),
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
			);
    }
    public function setlength($attribute,$params)
    {
    	$this->output=file_get_contents($this->output_file->tempName);
    	$this->output_size=strlen($this->output);
    	$this->input=file_get_contents($this->input_file->tempName);
    	$this->input_size=strlen($this->input);
    }	    
}