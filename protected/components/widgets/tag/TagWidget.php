<?php
/**
 * TagWidget creates a tag form / auto complete
 *
 * @version 0.2
 * @author Chris
 * @link http://con.cept.me
 */
class TagWidget extends CWidget {

    /**
     * Html ID
     * @var string
     */
    public $id = 'tagWidget';

    /**
     * Initial tags
     * @var array
     */
    public $tags;

    /**
     * The url to get json data
     * @var string
     */
    public $url;

    public function init()
    {
        $cs=Yii::app()->clientScript;
        $cs->registerCoreScript('jquery.ui');

        $cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css');

        $cs->registerScriptFile(
            Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/tag-it.js'),
            CClientScript::POS_END
        );

        $cs->registerCssFile(
            Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/tag-it.css')
        );

        $cs->registerScript($this->id,'
            $("#'.$this->id.'").tagit({
                tags: "'.$this->tags.'",
                url: "'.$this->url.'"
            });
        ', CClientScript::POS_READY);
    }

    public function run()
    {
        $this->tags = CJSON::encode($this->tags);
        $this->render('TagView', array(
            'id' => $this->id
        ));
    }
}