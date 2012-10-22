<?php
class EditorSelector extends CWidget
{
	public $link;
	public $editor;
	public $markitup_theme="simple";
	public $options;
	static public function convert($type,$data)
	{
		switch ($type)
		{
			case ULookup::CONTENT_TYPE_MARKDOWN:
			case 'markdown2html':
				$parser=new CMarkdownParser;
				//$pattern = '/\\[\\[Attachment:(\d+)|(.*)\\]\\]/i';
				//$replacement = '${1}1,$3';
		
				//preg_replace($pattern, $replacement, $string);
		
				$parsedText = $parser->safeTransform($data);
				return $parsedText;
				break;
			case ULookup::CONTENT_TYPE_WIKI:
			case 'wiki2html':
				Yii::import('ext.ultraeditor.SimpleWiki.ImWiki');
		
				$wiki=new ImWiki($data);
				return $wiki->get_html();
				break;
			case 'html2markdown':
				Yii::import('ext.ultraeditor.markdownify.Markdownify_Extra');
				$md = new Markdownify_Extra(false, false, true);
				return $md->parseString($data);
				break;
			case 'html2wiki':
				break;
			default:
				return $data;
				break;
		}
		return false;
	}
	public function init(){
		Yii::import("application.extensions.ultraeditor.XHeditor");
		$mWgt=new XHeditor();
		$mWgt->init();
		Yii::import("application.extensions.ultraeditor.jmarkitup.EMarkitupWidget");
		$mWgt=new EMarkitupWidget();
		$mWgt->theme=$this->markitup_theme;
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
			$mWgt->theme=$this->markitup_theme;
			$mWgt->settings='markdown';
			$mWgt->init();
			$mWgt->settings='wiki';
			$mWgt->init();
			//$mWgt->settings='html';
			//$mWgt->init();
		}
		
		$options= CJavaScript::encode($this->options);
		Yii::app()->clientScript->registerScript('editorselection'.$this->editor, "$('{$this->editor}').editorselection('{$this->link}',{$options})");
	}
	
}

