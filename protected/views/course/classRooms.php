<?php
$this->breadcrumbs=array(
	Yii::t('course','My courses')=>array('/course/index/mine/1'),
	$model->title=>array('/course/view','id'=>$model->id),
	Yii::t('course','classes')
);

?>

<center><font size='6'><?php echo CHtml::encode($model->title);?></font></center>
<table>
	<tr>
	<td><b><?php echo CHtml::encode($model->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/schoolInfo/view', 'id'=>$model->userinfo->user_id)); ?> | <?php echo CHtml::link(Yii::t('main',"send a message"), array("message/compose/". $model->user_id));?>
	</td>
	</tr>
</table>
<?php
$this->toolbar= array(
       array(
            'label'=>Yii::t('course','Add a class'),
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>$classRoom!==null,
        	'linkOptions'=>array('onclick'=>'return showDialogue();',)
        ),
		/*
        array(
            'label'=>Yii::t('course','View students'),
            'icon-position'=>'left',
        	'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/classRoom/students/'.$model->id),
        ),
    	array(
    		'label'=>Yii::t('course','View reports'),
    		'icon-position'=>'left',
    		'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
    		'icon'=>'document',
    		'url'=>array('/classRoom/reports/'.$model->id),
    	),
		/*
    	array(
            'label'=>Yii::t('course','Class information'),
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/classRoom/view/'.$model->id.''),
        ),*/
        array(
            'label'=>Yii::t('course','Update course'),
            'icon-position'=>'left',
	        'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'url'=>array('update', 'id'=>$model->id),
        ), 

    );

?>

<?php if(!(Yii::app()->user->isGuest)){?>
<div id="classRooms">
		<h3>
			<?php echo count($model->classRooms)!=1 ? count($model->classRooms) . ' classes' : '1 class'; ?>
		</h3>

	<?php if(count($model->classRooms)>=1): ?>
		<?php $this->renderPartial('_classRooms',array(
			'course'=>$model,
			'classRooms'=>$model->classRooms,
		)); ?>
	<?php endif; ?>
</div><!-- classRooms -->
<?php 
if($classRoom!=null){
echo CHtml::script('
function showDialogue()
{
	$("#submitiondialog").dialog("open");
	//this.blur();
	return false;	
}
');

if(Yii::app()->user->hasFlash('classRoomSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('classRoomSubmitted'); ?>
		</div>
<?php endif;	
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'submitiondialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>Yii::t('course','Create a class'),
        'autoOpen'=>$classRoom->hasErrors(),
		'minWidth'=>800,
		'height'=>700,
		'modal'=>true,
    ),
));
?>
		<?php $this->renderPartial('/classRoom/_form',array(
			'model'=>$classRoom,
		)); ?>

<?php 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
}
?>
<?php } ?>

