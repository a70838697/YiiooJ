<?php
/* SVN FILE: $Id: _menu.php 9 2010-12-17 13:21:39Z Chris $*/
/**
* RBAM menu partial view.
* 
* @copyright	Copyright &copy; 2010 PBM Web Development - All Rights Reserved
* @package		RBAM
* @since			V1.0.0
* @version		$Revision: 9 $
* @license		BSD License (see documentation)
*/
$module = $this->getModule();
$user = Yii::app()->getUser();
$this->widget('zii.widgets.CMenu', array(
	'id'=>'rbam-menu',
	'firstItemCssClass'=>'first',
	'items'=>array(
		array(
			'label'=>Yii::t('RbamModule.rbam','Auth Assignments'),
			'url'=>array('authAssignments/index'),
			'active'=>$this->id==='authAssignments',
			'visible'=>$user->checkAccess($module->authAssignmentsManagerRole)
		),
		array(
			'label'=>Yii::t('RbamModule.rbam','Auth Items'),
			'url'=>array('authItems/index'),
			'active'=>$this->id==='authItems' && $this->action->id!=='generate',
			'visible'=>$user->checkAccess($module->authItemsManagerRole),
			'items'=>array(
				array(
					'label'=>Yii::t('RbamModule.rbam','Create {type}', array('{type}'=>Yii::t('RbamModule.rbam','Role'))),
					'url'=>array('authItems/create', 'type'=>CAuthItem::TYPE_ROLE),
					'active'=>$this->id==='authItems' && $this->action->id==='create' && strpos(Yii::app()->getRequest()->queryString,'type='.CAuthItem::TYPE_ROLE)!==false,
				),
				array(
					'label'=>Yii::t('RbamModule.rbam','Create {type}', array('{type}'=>Yii::t('RbamModule.rbam','Task'))),
					'url'=>array('authItems/create', 'type'=>CAuthItem::TYPE_TASK),
					'active'=>$this->id==='authItems' && $this->action->id==='create' && strpos(Yii::app()->getRequest()->queryString,'type='.CAuthItem::TYPE_TASK)!==false,
				),
				array(
					'label'=>Yii::t('RbamModule.rbam','Create {type}', array('{type}'=>Yii::t('RbamModule.rbam','Operation'))),
					'url'=>array('authItems/create', 'type'=>CAuthItem::TYPE_OPERATION),
					'active'=>$this->id==='authItems' && $this->action->id==='create' && strpos(Yii::app()->getRequest()->queryString,'type='.CAuthItem::TYPE_OPERATION)!==false,
				),
			)
		),
		array(
			'label'=>Yii::t('RbamModule.rbam','Generate Auth Data'),
			'url'=>array('authItems/generate'),
			'active'=>$this->id==='authItems' && $this->action->id==='generate',
			'visible'=>$module->development && $user->checkAccess($module->rbacManagerRole)
		),
		array(
			'label'=>Yii::t('RbamModule.initialisation','Re-Initialise RBAC'),
			'url'=>array('rbamInitialise/initialise'),
			'active'=>$this->id==='rbaminitialise',
			'visible'=>$module->development && !empty($module->initialise) && $user->checkAccess($module->rbacManagerRole)
		)
	),
));

// show and hide auth items sub-menu
Yii::app()->getClientScript()->registerScript('rbamMenu', 'jQuery("#rbam-menu li").hover(
	function() {
		jQuery("ul:first", this).slideDown("fast");
	},
	function() {
		jQuery("ul:first", this).slideUp("fast");
	}
);');