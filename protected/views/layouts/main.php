<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="en" />
<?php echo CHtml::script("var urlBase='".Yii::app()->request->baseUrl."';"); ?>

<!-- blueprint CSS framework -->
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css"
	media="screen, projection" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css"
	media="print" />
<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<div class="container" id="page">
		<div id="header">
			<div id="logo">
				<?php
				if($this->course && strlen($this->course->website_title)>0)
				{
					echo CHtml::encode($this->course->website_title);
				}
				else
					echo Yii::t("main","CVLPJU: Course & Virtual Lab Platform of Jinan University").($this->course?":".$this->course->title:"");//CHtml::encode(Yii::t("main",Yii::app()->name)); ?>
			</div>
		</div>
		<!-- header -->

		<div id="mainmenu">
			<?php
			$this->getClassRoom();
			$items=array(
					array('label'=>Yii::t('main','Home'), 'url'=>array('/site/index'),'visible'=>true),
					array('label'=>Yii::t('main','ACM'), 'url'=>array('/problem'),'visible'=>true),
					array('label'=>Yii::t('main','Contest'), 'url'=>array('/programmingContest'),'visible'=>true),
					array('url'=>$this->course!=null?array('/course/view','id'=>$this->getCourseId(),'class_room_id'=>$this->classRoomId):array('/course/index/mine'), 'label'=>Yii::t('t',"Courses"). ($this->course!=null?":".$this->course->title:""),'itemOptions'=>array('class'=>'rootVoice {menu: \'box_menu_course\'}'), 'visible'=>(UUserIdentity::isAdmin()||UUserIdentity::isTeacher())),
					// array('url'=>array('/course/index'), 'label'=>Yii::t('main',"All courses"), 'visible'=>true),
					array('url'=>$this->classRoom!=null?array('/classRoom/view','id'=>$this->classRoomId):array('/classRoom/index/mine/1/term/1'), 'label'=>Yii::t('t',"Classrooms"). ($this->classRoom!=null?":".$this->classRoom->title."(".$this->classRoom->begin.")":""),'itemOptions'=>array('class'=>'rootVoice {menu: \'box_menu_classroom\'}'), 'visible'=>$this->course!==null||UUserIdentity::canHaveCourses()),
					//array('label'=>$this->classRoom!=null?CHtml::encode($this->classRoom->title):"",'linkOptions'=>array('style'=>'color:#B404AE;'),'itemOptions'=>array('class'=>'rootVoice {menu: \'box_menu_classroom\'}'),'url'=>array('/classRoom/view','id'=>$this->classRoomId),'visible'=>UUserIdentity::canHaveCourses() && $this->classRoom!==null),
					array('url'=>array('/comments'), 'label'=>Yii::t('main',"Comments"), 'visible'=>(UUserIdentity::isAdmin())),
					array('url'=>array('#'), 'label'=>Yii::t('t',"Administration"),'itemOptions'=>array('class'=>'rootVoice {menu: \'box_menu_admin\'}'), 'visible'=>UUserIdentity::isAdmin()),
					array('url'=>Yii::app()->getModule('user')->loginUrl, 'itemOptions'=>array('class'=>'rootVoice {menu: \'box_menu_login\'}'), 'label'=>Yii::app()->getModule('user')->t("Login"), 'visible'=>Yii::app()->user->isGuest),
					//array('url'=>Yii::app()->getModule('user')->registrationUrl, 'label'=>Yii::app()->getModule('user')->t("Register"), 'visible'=>Yii::app()->user->isGuest),
					array('url' => Yii::app()->getModule('message')->inboxUrl,
							'label' => Yii::t('main',"Messages").
							(Yii::app()->getModule('message')->getCountUnreadedMessages(Yii::app()->user->getId()) ?
									' (' . Yii::app()->getModule('message')->getCountUnreadedMessages(Yii::app()->user->getId()) . ')' : ''),
							'visible' => !Yii::app()->user->isGuest),
					array('url'=>Yii::app()->getModule('user')->profileUrl, 'label'=>Yii::app()->getModule('user')->t("Profile"), 'visible'=>!Yii::app()->user->isGuest),
					array('url'=>Yii::app()->getModule('user')->logoutUrl, 'label'=>Yii::app()->getModule('user')->t("Logout").' ('.Yii::app()->user->name.')', 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>Yii::t('main','About'), 'itemOptions'=>array('class'=>'rootVoice {menu: \'box_menu_about\'}'),'url'=>array('/site/page', 'view'=>'about')),
			);

			$wMenu=	$this->widget('zii.widgets.CMenu', array(
				'items'=>$items,
		));
			?></div><?php //zii.widgets.CBreadcrumbs
		$this->widget('ext.exbreadcrumbs.EXBreadcrumbs', array(
			'homeLink'=>$this->homelink,
				'links'=>$this->breadcrumbs,
		));
		echo $content;
		
/******************************************menu items***********************************/
?>
<?php if(UUserIdentity::isAdmin()){ ?>
<div id="box_menu_admin" class="mbmenu boxMenu">
	<table style="border:0;" >
	<tr><td>
		<?php
			echo CHtml::link(Yii::t('t',"User management"),array('/user/admin','id'=>$this->classRoomId)) ;
			echo CHtml::link(Yii::t('main',"Colledge users"),array('/schoolInfo/admin','id'=>$this->classRoomId)) ;
			echo CHtml::link(Yii::t('main',"Organization"),array('/organization/index','id'=>$this->classRoomId)) ;
			if(Yii::app()->user->id==1)
				echo CHtml::link(Yii::t('main',"RBAM"),array('/rbam','id'=>$this->classRoomId)) ;
		?>
	</td></tr>
	</table>
</div>
<?php }?>
<div id="box_menu_classroom" class="mbmenu boxMenu">
	<table style="border:0;" >
	<tr>
		<?php if($this->classRoom!==null){ ?>
		<?php
		if($this->classRoom->hasExperiment)
		{
			echo "<td>";
			echo "<div style='color:black'>".CHtml::encode($this->classRoom->title)."</div>";
			echo CHtml::link(Yii::t('t',"Experiments"),array('/classRoom/experiments','id'=>$this->classRoomId)) ;
			if(((UUserIdentity::isTeacher()&& $this->classRoom->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin())){
				echo CHtml::link(Yii::t('t',"Experiment reports"),array('/classRoom/reports','id'=>$this->classRoomId)) ;
			}
			if(UUserIdentity::isTeacher()||UUserIdentity::isAdmin())
			{
				echo CHtml::link(Yii::t('t',"Add an experiment"),array('/Experiment/create','id'=>$this->classRoomId)) ;
			}
			echo "</td>";
		}
		?>
	<td>
		<?php
		echo "<div style='color:black'>".CHtml::encode($this->classRoom->title)."</div>";
		if($this->classRoom->hasExercise)echo CHtml::link(Yii::t('t',"Quizzes"),array('/classRoom/quizzes','id'=>$this->classRoomId)) ;
		if(((UUserIdentity::isTeacher()&& $this->classRoom->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin())){
			echo CHtml::link(Yii::t('t',"Students"),array('/classRoom/students','id'=>$this->classRoomId)) ;
		}
		echo CHtml::link(Yii::t('t',"Classroom information"),array('/classRoom/view','id'=>$this->classRoomId)) ;
		?>
	</td>
		<?php
	}
		if((!(UUserIdentity::isAdmin()||UUserIdentity::isAdmin())) &&$this->course!=null)
		{
			echo "<td>";
			echo "<div style='color:black'>".CHtml::encode($this->course->title)."</div>";
			if(isset($this->getCourse()->chapter_id) && ($this->getCourse()->chapter_id>0))
				echo CHtml::link(Yii::t('t',"Course content"),array('/chapter/view','id'=>isset($this->getCourse()->chapter_id)?$this->getCourse()->chapter_id:"1",'class_room_id'=>$this->classRoomId)) ;
			echo CHtml::link(Yii::t('t',"Practices"),array('/practice/index','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
			echo CHtml::link(Yii::t('t',"Opened classrooms"),array('/course/classRooms','id'=>$this->getCourseId(),'class_room_id'=>$this->classRoomId)) ;
			echo CHtml::link(Yii::t('t',"Course introduction"),array('/course/view','id'=>$this->getCourseId(),'class_room_id'=>$this->classRoomId)) ;
			echo "</td>";
		}
		if(UUserIdentity::canHaveCourses())
		{
			echo "<td>";
			echo CHtml::link(Yii::t('t',"My present classrooms"),array('/classRoom/index/mine/1/term/1')) ;
			echo CHtml::link(Yii::t('t',"Available classrooms"),array('/classRoom/index/term/1')) ;
			echo CHtml::link(Yii::t('t',"My classrooms"),array('/classRoom/index/mine/1')) ;
			echo CHtml::link(Yii::t('t',"All classrooms"),array('/classRoom/index/')) ;
			echo "</td>";
		}
		?>
		</tr>
	</table>
</div>
<?php
if((UUserIdentity::isAdmin()||UUserIdentity::isTeacher())){
?>
<div id="box_menu_course" class="mbmenu boxMenu">
	<table style="border:0;" >
	<tr>
<?php
if($this->course!=null){
		echo "<td>";
		echo "<div style='color:black'>".CHtml::encode($this->course->title)."</div>";
		echo CHtml::link(Yii::t('t',"Course introduction"),array('/course/view','id'=>$this->getCourseId(),'class_room_id'=>$this->classRoomId)) ;
		if(isset($this->getCourse()->chapter_id) && ($this->getCourse()->chapter_id>0))
			echo CHtml::link(Yii::t('t',"Course content"),array('/chapter/view','id'=>isset($this->getCourse()->chapter_id)?$this->getCourse()->chapter_id:"1",'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t',"Opened classrooms"),array('/course/classRooms','id'=>$this->getCourseId(),'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t',"Add a classroom"),array('/classRoom/create','id'=>$this->getCourseId(),'class_room_id'=>$this->classRoomId)) ;
		echo "</td>";
		echo "<td>";
		echo "<div style='color:black'>".CHtml::encode($this->course->title)."</div>";
		echo CHtml::link(Yii::t('t','Multiple choices single answer questions'),array('/multipleChoice/list/0/type/1','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t','Multiple choices many answers questions'),array('/multipleChoice/list/0/type/2','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t','Answer questions'),array('/multipleChoice/list/0/type/6','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t',"Practices"),array('/practice/index','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t','Programming problems'),array('/courseProblem/index','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo "</td>";
		echo "<td>";
		echo "<div style='color:black'>".CHtml::encode($this->course->title)."</div>";
		echo CHtml::link(Yii::t('t','Create a multiple choices question'),array('/multipleChoice/create/0','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t','Create an answer question'),array('/multipleChoice/createFill/0','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo CHtml::link(Yii::t('t',"Create a practice"),array('/practice/create','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		
		echo CHtml::link(Yii::t('t','Create a programming problem'),array('/courseProblem/create','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId)) ;
		echo "</td>";		
}
?>	
		<td>
		<?php
		echo CHtml::link(Yii::t('t',"My courses"),array('/course/index/mine/1')) ;
		echo CHtml::link(Yii::t('t',"Create a course"),array('/course/create')) ;
		echo CHtml::link(Yii::t('t',"All courses"),array('/course/index/')) ;
		?>
		</td>
		</tr>
	</table>
</div>
<?php
}
?>
<div id="box_menu_login" class="mbmenu boxMenu">
	<table style="border:0;" >
	<tr>
		<td>
		<?php
		if(Yii::app()->user->isGuest)
		{
			echo CHtml::link(Yii::app()->getModule('user')->t("Login"),Yii::app()->getModule('user')->loginUrl) ;
			echo CHtml::link(Yii::app()->getModule('user')->t("Register"),Yii::app()->getModule('user')->registrationUrl) ;
		}
		?>
		</td>
	</tr>
	</table>
</div>
<div id="box_menu_about" class="mbmenu boxMenu">
	<table style="border:0;" >
	<tr>
		<td>
		<?php
			echo CHtml::link(Yii::t('main','About'),array('/site/page', 'view'=>'about')) ;
			echo CHtml::link(Yii::t('main','Contact'),array('/site/contact')) ;
		?>
		</td>
	</tr>
	</table>
</div><?php
		$this->widget('application.extensions.mb.MbMenu',array(
			'options'=>array(
				'id'=>'#'.$wMenu->id,
				'minZindex'=>9999,
				'menuWidth'=>100,
				'openOnClick'=>false,
				'closeOnMouseOut'=>true,
			)
		));		
		?><div id="footer">
			Copyright &copy;
			<?php echo date('Y'); ?>
			by Shuangping Chen.<br />
			<?php echo Yii::t('main','All Rights Reserved');?>
			.<br />
			<?php //echo Yii::powered(); ?>
		</div>
		<!-- footer -->

	</div>
	<!-- page -->

</body>
</html>
