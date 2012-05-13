<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
	<div id="content">
		<?php 
		if(property_exists($this,'contentMenu')){// && !empty($this->contentMenu)){
echo CHtml::script('
function view_problem_by_id()
{
	var n = prompt("Put the problem number here", "1");
	
	n = parseInt(n);
	
	if (n > 0)
	{
		window.location.href="'.UCHtml::url('problem/view/'). '"+n;
	}
	return false;
}
'); 
	$this->contentMenu=array(
		'htmlOptions' => array( 'style' => 'position: relative; z-index: 1' ),
		'items'=>array(
			array('label'=>'My', 'url'=>array('#'),
				'visible'=>!Yii::app()->user->isGuest, 
				'items'=>array(
					array('label'=>'Status', 'url'=>array('/uuser/view/'.Yii::app()->user->id)),
					array('label'=>'Recent Submitions', 'url'=>array('/submition/index/mine/1/refresh')),
					array('label'=>'Submited Problems', 'url'=>array('/problem/submited')),
					array('label'=>'Accepted Problems', 'url'=>array('/problem/accepted')),
					array('label'=>'Un-accepted Problems', 'url'=>array('/problem/notAccepted')),
				),
			),	
			array('label'=>'Problem', 'url'=>array('#'), 
				'items'=>array(
					array('label'=>'List Problem', 'url'=>array('/problem/index')),
					array('label'=>'Create Problem', 'url'=>array('/problem/create')),
					array('label'=>'Go to Problem', 'url'=>array('#'),'linkOptions'=>array('onclick'=>'return view_problem_by_id();')),
					
				),
			),
			array('label'=>'Submition', 'url'=>array('#'), 
				'items'=>array(
					array('label'=>'Recent Submitions', 'url'=>array('/submition/index/refresh')),
				),
			),
	        array(
	            'label'=>'Rank list',
	            'icon-position'=>'left',
	        	'url'=>array('/uuser/index'),
	        ),		
	        array(
	            'label'=>'Wiki',
	            'icon-position'=>'left',
	        	'url'=>array('/entry/ACM Algorithm'),
	        ),		
	        array(
	            'label'=>'<< Back',
	            'icon-position'=>'left',
	            'icon'=>'back',
	        	'url'=>'#',
		        'linkOptions'=>array('onclick'=>'history.go(-1);return false;')
	        ),		
		),
     );
			$jqueryslidemenupath = Yii::app()->assetManager->publish(Yii::app()->basePath.'/scripts/jqueryslidemenu/');
			//Register jQuery, JS and CSS files
			Yii::app()->clientScript->registerCssFile($jqueryslidemenupath.'/jqueryslidemenu.css');
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerScriptFile($jqueryslidemenupath.'/jqueryslidemenu.js');
		?>

		<div id="myslidemenu" class="jqueryslidemenu">
		<?php
			$this->widget('zii.widgets.CMenu',$this->contentMenu);
		?>
		<br style="clear: left" />
		</div><!-- myslidemenu-->
		<?php }?>
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<?php $this->endContent(); ?>