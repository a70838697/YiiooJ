<?php
	$this->breadcrumbs=array(
		'My classes'=>array('/classRoom/index/mine/1'),
		$model->experiment->classRoom->title=>array('/classRoom/'.$model->experiment->classRoom->id),
		'Experiments'=>array('/classRoom/experiments','id'=>$model->experiment->classRoom->id),	
		$model->experiment->title=>array('/experiment/'.$model->experiment->id),
		"Experiment Report",
	);

$this->menu=array(
	array('label'=>'List ExperimentReport', 'url'=>array('index')),
	array('label'=>'Create ExperimentReport', 'url'=>array('create')),
	array('label'=>'Update ExperimentReport', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ExperimentReport', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ExperimentReport', 'url'=>array('admin')),
);
$this->widget('application.components.widgets.MathJax',array());

$canscore=$model->canScore();
$canedit=$model->canEdit();
if(UUserIdentity::isAdmin()||Yii::app()->user->id==$model->user_id||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->classRoom->user_id))
$this->toolbar= array(
        array(
            'label'=>Yii::t('main','Update'),
            'icon-position'=>'left',
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'visible'=>$canedit,
        	'url'=>array('update', 'id'=>$model->id),
        ),
        array(
            'label'=>Yii::t('main','Submit'),
            'icon-position'=>'left',
            'visible'=>$model->canSubmit(),
	        'linkOptions'=>array('onclick'=>'return submitr();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id,'submited'=>'1'),
        ),
        array(
            'label'=>Yii::t('course','Score'),
            'icon-position'=>'left',
            'visible'=>$model->canScore(),
	        'linkOptions'=>array('onclick'=>'return showDialogue();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id),
        ),
		array(
            'label'=>Yii::t('course','Extend deadline'),
            'icon-position'=>'left',
            'visible'=>$model->canExtend() ,
	        'linkOptions'=>array('onclick'=>'return extend();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id,'extended'=>'1'),
        ),
    	array(
            'label'=>Yii::t('main','Print'),
    		'visible'=>true,
            'icon-position'=>'left',
	        'linkOptions'=>array('target'=>'_blank;',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('report', 'id'=>$model->id),
        ),
    );
?>
<?php /*$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'experiment_id',
		'user_id',
		'report',
		'conclusion',
		'created',
		'updated',
	),
));*/ ?>
<?php $this->renderPartial('viewReport',array('model'=>$model));?>
<?php 
	echo CHtml::script('
			function submitr()
			{
			return confirm("Are you really want to submit the report?\r\n You will not be allowed to modify it then.");
}
			');

if($canscore){
echo CHtml::script('
function extend()
{
	return confirm("Are you really want to let her/him resubmit?");
}		
function showDialogue()
{
	$("#scoredialog").dialog("open");
	//this.blur();
	return false;	
}
');
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'scoredialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Give a score',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>360,
		'modal'=>true,
    ),
));
?>

<div id="submition">
	<?php if(Yii::app()->user->hasFlash('scoreSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('scoreSubmitted'); ?>
		</div>
	<?php else: ?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'score-form',
	'enableAjaxValidation'=>false,
));

 ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'score'); ?>
		<?php echo $form->textField ($model,'score'); ?>
		<?php echo $form->error($model,'score'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'comment'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'comment','rows'=>6,
			'config'=>array('upLinkUrl'=>UCHtml::url('upload/create/type/report/classRoom/'.$model->experiment->class_room_id),'upLinkExt'=>"zip,rar,txt,sql,ppt,pptx,doc,docx",'upImgUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->class_room_id),'upImgExt'=>"jpg,jpeg,gif,png",)),true); ?>
		<?php echo $form->error($model,'comment'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
	<?php endif; ?>

</div><!-- score -->
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
}
?>
