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
				<?php echo Yii::t("main","CVLPJU: Course & Virtual Lab Platform of Jinan University").($this->course?":".$this->course->title:"");//CHtml::encode(Yii::t("main",Yii::app()->name)); ?>
			</div>
		</div>
		<!-- header -->

		<div id="mainmenu">
			<?php
			$items=array(
					array('label'=>Yii::t('main','Home'), 'url'=>array('/site/index'),'visible'=>true),
					// array('url'=>array('/course/index'), 'label'=>Yii::t('main',"All courses"), 'visible'=>true),
					array('url'=>array('/classRoom/index/mine/1/term/1'), 'label'=>Yii::t('t',"My present classrooms"), 'visible'=>UUserIdentity::canHaveCourses()),
					array('url'=>array('/classRoom/index/term/1'), 'label'=>Yii::t('t',"Available classrooms"), 'visible'=>UUserIdentity::isStudent()),
					array('url'=>array('/course/index/mine'), 'label'=>Yii::t('main',"My courses"), 'visible'=>(UUserIdentity::isAdmin()||UUserIdentity::isTeacher())),
					array('url'=>array('/comments'), 'label'=>Yii::t('main',"Comments"), 'visible'=>(UUserIdentity::isAdmin())),
					array('url'=>array('/rbam'), 'label'=>Yii::t('main',"RBAM"), 'visible'=>(!Yii::app()->user->isGuest)&&(Yii::app()->user->id==1)),
					array('url'=>array('/schoolInfo/admin'), 'label'=>Yii::t('main',"Colledge users"), 'visible'=>(!Yii::app()->user->isGuest)&&(UUserIdentity::isAdmin())),
					array('url'=>array('/organization/index'), 'label'=>Yii::t('main',"Organization"), 'visible'=>(!Yii::app()->user->isGuest)&&(UUserIdentity::isAdmin())),
					array('url'=>Yii::app()->getModule('user')->loginUrl, 'label'=>Yii::app()->getModule('user')->t("Login"), 'visible'=>Yii::app()->user->isGuest),
					array('url'=>Yii::app()->getModule('user')->registrationUrl, 'label'=>Yii::app()->getModule('user')->t("Register"), 'visible'=>Yii::app()->user->isGuest),
					array('url' => Yii::app()->getModule('message')->inboxUrl,
							'label' => Yii::t('main',"Messages").
							(Yii::app()->getModule('message')->getCountUnreadedMessages(Yii::app()->user->getId()) ?
									' (' . Yii::app()->getModule('message')->getCountUnreadedMessages(Yii::app()->user->getId()) . ')' : ''),
							'visible' => !Yii::app()->user->isGuest),
					array('url'=>Yii::app()->getModule('user')->profileUrl, 'label'=>Yii::app()->getModule('user')->t("Profile"), 'visible'=>!Yii::app()->user->isGuest),
					array('url'=>Yii::app()->getModule('user')->logoutUrl, 'label'=>Yii::app()->getModule('user')->t("Logout").' ('.Yii::app()->user->name.')', 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>Yii::t('main','About'), 'url'=>array('/site/page', 'view'=>'about')),
					array('label'=>Yii::t('main','Contact'), 'url'=>array('/site/contact')),
			);

			$this->widget('zii.widgets.CMenu',array(
					'items'=>$items,
		)); ?>
		</div><?php
		$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
		));
		echo $content; ?>

		<div id="footer">
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
