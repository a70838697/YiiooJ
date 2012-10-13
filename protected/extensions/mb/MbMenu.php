<?php
class MbMenu extends CWidget
{
	/*
	 * To store the base url of the assets for the widget.
	*/
	private $_baseUrl;
	public $options=array();
		
	public function init()
	{
		parent::init();
		if(!isset($this->options['css']))
		{
			$this->options['css']='/css/mbmenu/menu_black.css';
		}
					
		// publish assets
		$assets = dirname(__FILE__) . '/' . 'jquery.mb.menu';
		$this->_baseUrl = Yii::app()->getAssetManager()->publish($assets);
		
		$scriptDir=$this->_baseUrl  . '/' . 'inc'. '/';
		Yii::app()->clientScript->registerScriptFile($scriptDir.'jquery.metadata.js');
		Yii::app()->clientScript->registerScriptFile($scriptDir.'jquery.hoverIntent.js');
		Yii::app()->clientScript->registerScriptFile($scriptDir.'mbMenu.js');
		Yii::app()->clientScript->registerCSSFile(Yii::app()->request->baseUrl.$this->options['css']);
		
	}

	public function run()
	{
		$options= CJavaScript::encode($this->options);
		if(isset($this->options['id'])){
			Yii::app()->clientScript->registerScript('mb.mbmenu#'.$this->options['id'] ,
				'$("'.$this->options['id'].'").buildMenu('.$options.')');
		}

	}
}