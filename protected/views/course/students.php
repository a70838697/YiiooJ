<?php
$this->breadcrumbs=array(
	'My Courses'=>array('/course/index/mine/1'),
	$model->title=>array('view','id'=>$model->id),
	'Students'
);

$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Update Course', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Course', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>

<center><font size='6'><?php echo CHtml::encode($model->title);?></font></center>
<table>
	<tr>
	<td><b><?php echo CHtml::encode($model->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/user/user/view', 'id'=>$model->userinfo->user_id)); ?></td>
	<td><center><b><?php echo CHtml::encode($model->getAttributeLabel('due_time')); ?>:</b>
	<?php echo CHtml::encode($model->due_time); ?></center></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('location')); ?>:</b>
	<?php echo CHtml::encode($model->location); ?></td>
	</tr>
</table>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'Add a student',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
        	'linkOptions'=>array('onclick'=>'return showDialogue();',)
        ),
        array(
            'label'=>'View Experiments',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/course/experiments/'.$model->id.''),
        ),
   		array(
			'label'=>'View Reports',
			'icon-position'=>'left',
    		'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
    		'icon'=>'document',
    		'url'=>array('/course/reports/'.$model->id),
    	),
        array(
            'label'=>'View this course',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/course/view/'.$model->id.''),
        ),
        array(
            'label'=>'Update this course',
            'icon-position'=>'left',
	        'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'url'=>array('update', 'id'=>$model->id),
        ),               
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>

<?php if(!(Yii::app()->user->isGuest)){?>
<div id="students">

</div><!-- students -->
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->assetManager->publish(Yii::getPathOfAlias('system.web.widgets.pagers.pager').'.css'));?>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/styles.css');?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/jquery.yiigridview.js');?>

<?php 
/*
$cs=Yii::app()->getClientScript();
$cs->registerCoreScript('bbq');
$cs->registerCoreScript('yii');
echo CHtml::script('

$("#students").load("'.CHtml::normalizeUrl(array('group/view/'.$model->student_group_id)) .'",{},function(){'.
"
jQuery('#groupUser-grid').yiiGridView({'ajaxUpdate':['1','groupUser-grid'],'ajaxVar':'ajax','pagerClass':'pager','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items','selectableRows':1,'pageVar':'Problem_page'});
".
'});
');
*/
?>

<h1>Students</h1>
<?php

echo UCHtml::cssFile('pager.css');
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'groupUser-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>false,
	'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>4,),
	'template'=>'{summary}{pager}{items}{pager}',
	'columns'=>array(
			/*
		array(
			'header' =>'User',
			'name'=>'username',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data["username"]),array("user/user/view","id"=>$data["user_id"]))',
		),
		*/
		array(
			'header' =>'Student number',
			'name'=>'identitynumber',
			'type'=>'raw',
			'value'=>'CHtml::encode($data["identitynumber"])',
		),
		array(
			'header' =>'Name',
			'name'=>'name',
			'type'=>'raw',
			'value'=>'CHtml::encode($data["lastname"].$data["firstname"])',
		),
		array(
			'header' =>'Status',
			'name'=>'status',
			'type'=>'raw',
			'value'=>'GroupUser::$USER_STATUS_MESSAGES[$data["status"]]',
		),
		array(
			'name'=>'Action',
			'type'=>'raw',
			'value'=>'\'<input type="button" class="capply" tag="\'.$data["id"] .\'" value="Reject">\' .($data["status"]==GroupUser::USER_STATUS_APPLIED?\'<input class="apply" tag="\'.$data["id"] .\'" value="Accept"  type="button">\':"") ',
		),		
	),
));

echo CHtml::script('
$(".apply").live("click", 
function ()
{
	return apply_course($(this).attr("tag"),"/op/agree");
}
);
function apply_course(id,op)
{
	if(id!="")
	{
		$.get("'.CHtml::normalizeUrl(array("/group/apply/")).'"+"/"+id+op, function(data) {
			$.fn.yiiGridView.update(\'groupUser-grid\');
		});
	}
	return false;
}
$(".capply").live("click", 
function ()
{
	return apply_course($(this).attr("tag"),"/op/deny");
}
);

');
?>
<?php 

echo CHtml::script('
var isfirstload=true;
function showDialogue()
{
		$("#selectstudent").load("'.CHtml::normalizeUrl(array('group/selectStudent/'.$model->student_group_id)) .'",{},function(){'.
			"
			jQuery('#group-grid').yiiGridView({'ajaxUpdate':['1','group-grid'],'ajaxVar':'ajax','pagerClass':'pager','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items','selectableRows':2,'pageVar':'SchoolInfo_page'});
			".
		'});
	$("#submitiondialog").dialog("open");
	//this.blur();
	return false;	
}
$("#dowithselected").live("click",function()
{
	var selectedids=$.fn.yiiGridView.getSelection("group-grid");
	if(selectedids==""){
		alert("Please select at least one member");
	}
	else
	{
		$("#students_ids").val(selectedids);
		$("#addStudent").submit();
	}
});

');
	
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'submitiondialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Create an experiment',
        'autoOpen'=>false,
		'minWidth'=>400,
		'height'=>500,
		'modal'=>true,
    ),
));
?>
<form id="addStudent" action="" method="post">
<input id="students_ids" name="students_ids" type=hidden value="" />
</form>
<div id='selectstudent'>
</div>
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php } ?>
