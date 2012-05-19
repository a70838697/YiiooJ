<?php
	$this->breadcrumbs=array(
		'My Courses'=>array('/course/index/mine/1'),
		$model->experiment->course->title=>array('/course/'.$model->experiment->course->id),
		'Experiments'=>array('/course/experiments','id'=>$model->experiment->course->id),	
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
?>

<h1>View ExperimentReport #<?php echo $model->id; ?></h1>
<?php
$canscore=UUserIdentity::isAdmin()||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->course->user_id);
$canedit=$canscore
	||(UUserIdentity::isStudent() && (
			($model->status==ExperimentReport::STATUS_ALLOW_EDIT ) 
			|| ( (!$model->experiment->isTimeOut()) &&  $model->status==ExperimentReport::STATUS_NORMAL)
		)
	);
if(UUserIdentity::isAdmin()||Yii::app()->user->id==$model->user_id||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->course->user_id))
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'Edit',
            'icon-position'=>'left',
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'visible'=>$canedit,
        	'url'=>array('update', 'id'=>$model->id),
        ),
        array(
            'label'=>'Score',
            'icon-position'=>'left',
            'visible'=>$canscore && ( ($model->status==ExperimentReport::STATUS_SUBMITIED )|| ( ($model->status==ExperimentReport::STATUS_NORMAL) && ($model->experiment->isTimeOut()) )),
	        'linkOptions'=>array('onclick'=>'return showDialogue();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id),
        ),
        array(
            'label'=>'Submit',
            'icon-position'=>'left',
            'visible'=>($canscore|| (Yii::app()->user->id==$model->user_id)) && ($model->status !=ExperimentReport::STATUS_SUBMITIED) ,
	        'linkOptions'=>array('onclick'=>'return submitr();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id,'submited'=>'1'),
        ),
        array(
            'label'=>'Extend deadline',
            'icon-position'=>'left',
            'visible'=>($canscore) && ($model->status ==ExperimentReport::STATUS_SUBMITIED) ,
	        'linkOptions'=>array('onclick'=>'return extend();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id,'extended'=>'1'),
        ),
    	array(
            'label'=>'Print',
            'icon-position'=>'left',
	        'linkOptions'=>array('target'=>'_blank;',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('report', 'id'=>$model->id),
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
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
if($canscore){
echo CHtml::script('
function extend()
{
	return confirm("Are you really want to let her/him resubmit?");
}		
function submitr()
{
	return confirm("Are you really want to submit the report?\r\n You will not be allowed to modify it then.");
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
			'config'=>array('upLinkUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upLinkExt'=>"zip,rar,txt,sql,ppt,pptx,doc,docx",'upImgUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upImgExt'=>"jpg,jpeg,gif,png",)),true); ?>
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
