<?php $this->beginContent('//layouts/main_course'); ?>
<div class="container">
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
			array('label'=>Yii::t('main','Class home'), 'url'=>array('/classRoom/view','id'=>$this->getCourseId()),
				'visible'=>($this->getClassRoomId()>0),
				'items'=>array(
					array('label'=>Yii::t('course','Class information'), 'url'=>array('/classRoom/view','id'=>$this->getClassRoomId()),'visible'=>($this->getClassRoomId()>0)),
					array('label'=>'Create class', 'url'=>array('/classRoom/create'),'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin()),
				),
			),
			array('url'=>array('/classRoom/experiments','id'=>$this->getClassRoomId()), 'label'=>Yii::t('course',"Experiments"),
				'visible'=>($this->getClassRoomId()>0),
				'items'=>array(
					array('url'=>array('/classRoom/experiments','id'=>$this->getClassRoomId()), 'label'=>Yii::t('course',"View experiments"),'visible'=>($this->getClassRoomId()>0)),
					array('label'=>Yii::t('course','View reports'), 'url'=>array('/classRoom/reports','id'=>$this->getClassRoomId()),'visible'=>($this->getClassRoomId()>0)&&((UUserIdentity::isTeacher()&& $this->classRoom->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin())),
					array('label'=>Yii::t('course','Add an experiment'), 'url'=>array('/Experiment/create','id'=>$this->getClassRoomId()),'visible'=>($this->getClassRoomId()>0)&&((UUserIdentity::isTeacher()&& $this->classRoom->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin())),
				),
			),
			array('url'=>array('/classRoom/students','id'=>$this->getClassRoomId()), 'label'=>Yii::t('course',"View students"),
				'visible'=>($this->getClassRoomId()>0),
				'items'=>array(
					array('url'=>array('/classRoom/students','id'=>$this->getClassRoomId()), 'label'=>Yii::t('course',"View students"),'visible'=>($this->getClassRoomId()>0))
				),
			),
			array('label'=>Yii::t('course','Course introduction'), 'url'=>array('/course/view','id'=>$this->getCourseId(),'class_room_id'=>$this->getClassRoomId()),
				'visible'=>true, 
				'items'=>array(
					array('label'=>Yii::t('course',"Course content"), 'url'=>array('/chapter/view','id'=>isset($this->getCourse()->chapter_id)?$this->getCourse()->chapter_id:"1",'class_room_id'=>$this->getClassRoomId()), 'visible'=>UUserIdentity::canHaveCourses() &&isset($this->getCourse()->chapter_id) && ($this->getCourse()->chapter_id>0)),
					array('label'=>Yii::t('course','Course introduction'), 'url'=>array('/course/view','id'=>$this->getCourseId(),'class_room_id'=>$this->getClassRoomId()),'visible'=>true),						
					array('label'=>Yii::t('course','Teachers'), 'url'=>array('/course/view','id'=>$this->getCourseId(),'class_room_id'=>$this->getClassRoomId()),'visible'=>true),						
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
		<br style="clear: left; height:0; font-size: 1px; line-height: 0px; overflow: hidden; " />
		</div>
		<?php }
		if(property_exists($this,'toolbar') && !empty($this->toolbar)){
			$count=0;
			foreach($this->toolbar as $item) if($item['visible'])$count++;
			if($count>0){
				$content2=$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
					'items' => $this->toolbar,
					'htmlOptions' => array('style' => 'clear: both;'),
					),false);
			}
		}
		?>
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->		
	</div>
<?php $this->endContent(); ?>