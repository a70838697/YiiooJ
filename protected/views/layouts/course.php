<?php $this->beginContent('//layouts/main_course'); ?>
<div class="container">
	<div id="content">
		<?php 
		if(property_exists($this,'contentMenu') && !empty($this->contentMenu)){
	$this->contentMenu=array(
		'htmlOptions' => array( 'style' => 'position: relative; z-index: 1' ),
		'items'=>array(
			array('label'=>'My', 'url'=>array('/classRoom/index/mine/1'),
				'visible'=>!Yii::app()->user->isGuest, 
				'items'=>array(
					array('label'=>'My classes', 'url'=>array('/classRoom/index/mine/1')),
					array('label'=>'My Problems', 'url'=>array('/courseproblem/index/mine/1')),
				),
			),
			array('label'=>'Course', 'url'=>array('/classRoom/index'), 
				'items'=>array(
					array('label'=>'All classes', 'url'=>array('/classRoom/index')),
					array('label'=>'Create class', 'url'=>array('/classRoom/create'),'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin()),
					
				),
			),
			array('label'=>'Problem', 'url'=>array('#'), 
				'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(), 
				'items'=>array(
					array('label'=>'Create Problem', 'url'=>array('/courseproblem/create')),
				),
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