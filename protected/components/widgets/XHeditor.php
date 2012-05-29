<?php

/*
 * XHeditor class file
 * @author Robert Campbell <waprave@gmail.com>
 * @since 1.0
 */

/*

Usage:

<?php

$this->widget('application.components.widgets.XHeditor',array(
	'language'=>'en', //options are en, zh-cn, zh-tw
	'config'=>array(
		'id'=>'xh1',
		'name'=>'xh',
		'tools'=>'mini', // mini, simple, full or from XHeditor::$_tools, tool names are case sensitive
		'width'=>'100%',
		//see XHeditor::$_configurableAttributes for more
	),
	'contentValue'=>'Enter your text here', // default value displayed in textarea/wysiwyg editor field
	'htmlOptions'=>array('rows'=>5, 'cols'=>10),// to be applied to textarea
));
?>
			
//Usage with a model
<?php
$this->widget('application.components.widgets.XHeditor',array(
	'model'=>$modelInstance,
	'modelAttribute'=>'attribute',
	'showModelAttributeValue'=>false, // defaults to true, displays the value of $modelInstance->attribute in the textarea
	'config'=>array(
		'tools'=>'full', // mini, simple, fill or from XHeditor::$_tools
		'width'=>'300',
	),
));
?>
*/

class XHeditor extends CWidget
{
	/*
	 * The options for the widget.
	 */
	public $config = array();
	
	/*
	 * An instance of the model that the field belongs to.
	 */
	public $model;
	
	/*
	 * The attribute of the model instance.
	 */
	public $modelAttribute;
	
	/*
	 * Determines whether or not the value of the model 
	 * attribute should be displayed in the textarea
	 */
	public $showModelAttributeValue = true;
	
	/*
	 * The language that the widget will be displayed in
	 */
	public $language;
	
	/*
	 * The value to be displayed in the textarea
	 * Precedence is given to {$this->model}->{$this->modelAttribute} if set
	 */
	public $contentValue;
	
	/*
	 * Html attributes to be applied to the textarea
	 */
	public $htmlOptions = array();
	
	/*
	 * Comma separated list of attributes that can be 
	 * passed to $this->config as array keys
	 */
	private $_configurableAttributes = 'html5Upload,upLinkUrl,upLinkExt,upImgExt,upImgUrl,upMediaUrl,upMediaExt,upFlashUrl,upFlashExt,id,name,tools,skin,showBlocktag,internalScript,internalStyle,width,height,loadCSS,fullscreen,beforeSetSource,beforeGetSource,focus,blur,forcePtag';
	
	/*
	 * Comma separated list of attributes that can be
	 * passed to $this->config['tools'] as array keys
	 */
	private $_tools = 'GStart,Cut,Copy,Paste,Pastetext,GEnd,Separator,GStart,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,Removeformat,GEnd,Separator,GStart,Align,List,Outdent,Indent,GEnd,Separator,GStart,Link,Unlink,Img,Flash,Media,Emot,Table,GEnd,Separator,GStart,Source,Preview,Fullscreen,About,GEnd';
	
	/*
	 * Array of languages that can be used by the widget (i.e self::$language)
	 */
	private $_languages = array('en','zh-cn','zh-tw');
	
	/*
	 * To store the base url of the assets for the widget.
	 */
	private $_baseUrl;
	
	/*
	 * Stores the markup to be rendered/displayed
	 */
	private $_field;
	
	/*
	 * Array of default values for widget properties
	 */
	private $_defaults = array(
		'language'=>'en',
		'config'=>array(
			'html5Upload'=>false
		),
		'htmlOptions'=>array(
			'rows'=>1,
			'cols'=>1,
		),
	);
	
	/*
	 * Merges the specified attributes with default values.
	 * Preference is given to the specified values.
	 */
	public function setDefaults()
	{
		// investigate if this will cause undesired side effects
		
		$this->config = array_merge($this->_defaults['config'], $this->config);
		$this->htmlOptions = array_merge($this->_defaults['htmlOptions'], $this->htmlOptions);
		if(empty($this->language))
			$this->language = $this->_defaults['language'];
	}
	
	/*
	 * Prepares widget to be used by setting necessary
	 * configurations, publishing assets and registering
	 * necessary javascripts and css to be rendered.
	 */
	public function init()
	{
		$this->setDefaults();
		$config = $this->cleanConfig();
		$model = $this->model;
		$modelAttribute = $this->modelAttribute;
		
		// self::$model and self::$modelAttribute are specified
		if(isset($model, $modelAttribute))
		{
			if(empty($config['id']))
				$config['id'] = $this->htmlOptions['id'] = CHtml::activeId($model, $modelAttribute);
			if(empty($config['name']))
				$config['name'] = CHtml::activeName($model, $modelAttribute);
			if($this->showModelAttributeValue===true)
			{
				$modelAttributeValue = $model->{$modelAttribute};
				$this->contentValue = !empty($modelAttributeValue) ? $modelAttributeValue : null;
			}
		}else{
			//if name and id attributes are not specified in self::$config, generate them
			if(empty($config['id']))
				$config['id'] = 'xheditor_' . rand(1, 1000);
			if(empty($config['name']))
				$config['name'] = 'xheditor';
		}
		
		if(empty($this->htmlOptions['id']))
			$this->htmlOptions['id'] = $config['id'];

		$this->_field = CHtml::textArea($config['name'],$this->contentValue,$this->htmlOptions);
		
		// publish assets
		$assets = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'xheditor';
		$this->_baseUrl = Yii::app()->getAssetManager()->publish($assets);
		
		// register css and js to be rendered
		//Yii::app()->clientScript->registerCss($config['id'],'#'.$config['id'].' {width:'.$config['width'].';height:'.$config['height'].';}');
		Yii::app()->clientScript->registerScriptFile($this->_baseUrl . '/xheditor-'. $this->language .'.min.js');
		Yii::app()->clientScript->registerScript($config['id'],'$("#'.$config['id'].'").xheditor('.CJavaScript::encode($config).');');
	}
	
	/*
	 * Ensures that only tools that are specified by self::$_tools
	 * are used by the widget.
	 */
	public function cleanTools($toolsParam = null)
	{
		if($toolsParam===null)
			return $toolsParam;
		$_validTools = explode(',', $this->_tools);
		$_configuredTools = explode(',', $toolsParam);
		$_tools = array();
		foreach($_configuredTools as $tool)
		{
			// if 'mini', 'simple' or 'full' is specified in 
			// $this->config['tools'], then the tool will be used.
			if($tool==='mini'||$tool==='simple'||$tool==='full')
				return $tool;

			if(in_array($tool, $_validTools))
			{
				$_tools[] = $this->_tools[$tool]; // revise this
			}
		}
		return implode(',', $_tools);
	}
	
	/*
	 * Ensures that only valid configuration values 
	 * are used by the widget. Valid attributes are
	 * stored in self::$_configurableAttributes
	 */
	public function cleanConfig()
	{
		$config = array();
		$configurableAttributes = explode(',', $this->_configurableAttributes);
		foreach($this->config as $key => $val)
		{
			if(in_array($key, $configurableAttributes))
			{
				if($key==='tools')
				{
					$tools = $this->cleanTools($this->config[$key]);
					// If no valid tools were specified, do not add tools
					// to the config so xheditor default tools will be used
					if(empty($tools))
						continue;
				}
				$config[$key] = $val;
			}
		}
		return $config;
	}
	
	/*
	 * Displays the textarea field
	 */
	public function run()
	{
		echo $this->_field;
	}
}