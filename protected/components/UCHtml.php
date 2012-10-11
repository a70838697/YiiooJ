<?php
class UCHtml
{
	public static function addYiiGridViewScriptsForAjax(){
		Yii::app()->clientScript->registerCssFile(Yii::app()->assetManager->publish(Yii::getPathOfAlias('system.web.widgets.pagers.pager').'.css'));
		Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/styles.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/jquery.yiigridview.js');
		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('bbq');
		$cs->registerCoreScript('yii');
	}
    /**
    * Makes the given URL relative to the /image directory
    */
    public static function image($url) {
        return CHtml::image( Yii::app()->baseUrl."/images/".$url);
    }
    public static function theUrl($url) {
        return CHtml::normalizeUrl($url);
    }
    public static function url($url) {
        return Yii::app()->baseUrl.'/'.$url;
    }
    public static function imageFile($url) {
        return ( Yii::app()->baseUrl."/images/".$url);
    }    
    /**
    * Makes the given URL relative to the /css directory
    */
    public static function cssFile($url) {
        return CHtml::cssFile(Yii::app()->baseUrl.'/css/'.$url);
    }
    /**
    * Makes the given URL relative to the /js directory
    */
    public static function scriptFile($url) {
        return CHtml::scriptFile(Yii::app()->baseUrl.'/js/'.$url);
    }
}