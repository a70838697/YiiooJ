<?php
class EAjaxUploadBasic extends CWidget
{
	public $id="fileUploader";
	public $postParams=array();
	public $config=array();
	public $css=null;

	public function run()
	{
		if(empty($this->config['action']))
		{
			throw new CException('EAjaxUpload: param "action" cannot be empty.');
		}

		if(empty($this->config['allowedExtensions']))
		{
			throw new CException('EAjaxUpload: param "allowedExtensions" cannot be empty.');
		}

		if(empty($this->config['sizeLimit']))
		{
			throw new CException('EAjaxUpload: param "sizeLimit" cannot be empty.');
		}

		//unset($this->config['element']);

		//echo '<div id="'.$this->id.'"><noscript><p>Please enable JavaScript to use file uploader.</p></noscript></div>';
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);

		Yii::app()->clientScript->registerScriptFile($baseUrl . '/fileuploader.js', CClientScript::POS_HEAD);

		$this->css=(!empty($this->css))?$this->css:$baseUrl.'/fileuploader.css';
		Yii::app()->clientScript->registerCssFile($this->css);

		$postParams = array('PHPSESSID'=>session_id(),'YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken);
		if(isset($this->postParams))
		{
			$postParams = array_merge($postParams, $this->postParams);
		}

		$config = array(
				//'button'=>'js:document.getElementById("'.($this->id).'")',
				'debug'=>false,
				'multiple'=>false
		);
		$config = array_merge($config, $this->config);
		$config['params']=$postParams;
		$config = CJavaScript::encode($config);
		Yii::app()->getClientScript()->registerScript("FileUploader_".$this->id, "var FileUploader_".$this->id." = new qq.FileUploaderBasic($config); ",CClientScript::POS_LOAD);
	}


}