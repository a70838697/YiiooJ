<?php
class EditorSelector extends CWidget
{
	public $link;
	public $editor;
	public $options;
	public function init(){
		Yii::import("application.extensions.ultraeditor.XHeditor");
		$mWgt=new XHeditor();
		$mWgt->init();
		Yii::import("application.extensions.ultraeditor.jmarkitup.EMarkitupWidget");
		$mWgt=new EMarkitupWidget();
		$mWgt->settings='markdown';
		$mWgt->init();
		$mWgt->settings='wiki';
		$mWgt->init();
		Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.extensions.ultraeditor.ultraeditor').'.js'));
	}
	public function run()
	{
		if (isset($this->options[ULookup::CONTENT_TYPE_HTML]))
		{
			Yii::import("application.extensions.ultraeditor.XHeditor");
			$mWgt=new XHeditor();
			$mWgt->init();
		}
		if (isset($this->options[ULookup::CONTENT_TYPE_WIKI])||isset($this->options[ULookup::CONTENT_TYPE_MARKDOWN]))
		{
			Yii::import("application.extensions.ultraeditor.jmarkitup.EMarkitupWidget");
			$mWgt=new EMarkitupWidget();
			$mWgt->settings='markdown';
			$mWgt->init();
			$mWgt->settings='wiki';
			$mWgt->init();
			//$mWgt->settings='html';
			//$mWgt->init();
		}
		
		$options= CJavaScript::encode($this->options);
		Yii::app()->clientScript->registerScript('editorselection'.$this->link, "$('{$this->link}').editorselection('{$this->editor}',{$options})");
	}
	
}

