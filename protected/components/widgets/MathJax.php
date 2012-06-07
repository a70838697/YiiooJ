<?php

/*
 * MathJax class file
 * @author shpchen <worthful@qq.com>
 * @since 1.0
 */

/*

Usage:

<?php

$this->widget('application.components.widgets.XHeditor',array(
	'contentValue'=>'Enter your text here', // default value displayed in textarea/wysiwyg editor field
));
?>
			
*/

class MathJax extends CWidget
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
		'src'=>'http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML',
	);
	
	/*
	 * Merges the specified attributes with default values.
	 * Preference is given to the specified values.
	 */
	public function setDefaults()
	{
		// investigate if this will cause undesired side effects
		
	}
	
	/*
	 * Prepares widget to be used by setting necessary
	 * configurations, publishing assets and registering
	 * necessary javascripts and css to be rendered.
	 */
	public function init()
	{
		$this->_field = '
	<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    extensions: ["tex2jax.js"],
    jax: ["input/TeX", "output/HTML-CSS"],
    tex2jax: {
      inlineMath: [ [\'$\',\'$\'], ["\\\\(","\\\\)"] ],
      displayMath: [ [\'$$\',\'$$\'], ["\\\\[","\\\\]"] ],
      processEscapes: true
    },
    "HTML-CSS": { availableFonts: ["TeX"] }
  });
</script>	';
		
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js_plugins/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
	}
	

	/*
	 * Displays the textarea field
	 */
	public function run()
	{
		echo $this->_field;
	}
}