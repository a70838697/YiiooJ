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
if(UUserIdentity::isAdmin()||Yii::app()->user->id==$model->user_id||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->course->user_id))
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'Edit',
            'icon-position'=>'left',
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('update', 'id'=>$model->id),
        ),
        array(
            'label'=>'Score',
            'icon-position'=>'left',
            'visible'=>$canscore,
	        'linkOptions'=>array('onclick'=>'return showDialogue();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('view', 'id'=>$model->id),
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
<?php $this->renderPartial('_report',array('model'=>$model));?>
<?php 
if($canscore){
echo CHtml::script('
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
		'height'=>500,
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
