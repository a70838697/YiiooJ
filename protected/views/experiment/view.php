<?php
$this->breadcrumbs=array(
	'My classes'=>array('/classRoom/index/mine/1'),
	$model->classRoom->title=>array('/classRoom/view','id'=>$model->class_room_id),
	'Experiments'=>array('/classRoom/experiments','id'=>$model->class_room_id),
	$model->title,
);

$this->menu=array(
	array('label'=>'List Experiment', 'url'=>array('index')),
	array('label'=>'Create Experiment', 'url'=>array('create')),
	array('label'=>'Update Experiment', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Experiment', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Experiment', 'url'=>array('admin')),
);
?>
<h1>View Experiment <?php echo CHtml::encode($model->title); ?></h1>
<?php
$cansubmit=false;
$report=null;
if(UUserIdentity::isStudent())
{
	$report=ExperimentReport::model()->find(array(
			'condition'=>'experiment_id=:experimentID and user_id='.Yii::app()->user->id,
			'params'=>array(':experimentID'=>$model->id),
	));
	if( $report==null)$cansubmit=!$model->isTimeOut();
	else {
		$cansubmit=($report->status==ExperimentReport::STATUS_ALLOW_EDIT ) ||($report->status==ExperimentReport::STATUS_ALLOW_LATE_EDIT);
	}
	
}
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'Add an problem',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),
        	'linkOptions'=>array('onclick'=>'return showDialogue();',)
        ),
        array(
            'label'=>'Update this experiment',
            'icon-position'=>'left',
	        'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),//!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$model),'update'),
            'url'=>array('update', 'id'=>$model->id),
        ), 
        array(
            'label'=>'List reports',
            'icon-position'=>'left',
	        'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),//!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$model),'update'),
            'url'=>array('reports', 'id'=>$model->id),
        ),
        array(
            'label'=>($report==null)?'Write a report':"Update the report",
            'icon-position'=>'left',
	        'visible'=>$cansubmit,//!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$model),'update'),
            'url'=>($report==null)?array('/experimentReport/write', 'id'=>$model->id):array('/experimentReport/update', 'id'=>$report->id),
        ), 
    	array(
    		'label'=>'View my report',
    		'icon-position'=>'left',
    		'visible'=>($report!=null),//!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$model),'update'),
    		'url'=>array('/experimentReport/view', 'id'=>($report==null)?1:$report->id),
    	),
    		
      
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>
<?php
$gMessages=(UClassRoomLookup::getEXPERIMENT_TYPE_MESSAGES());
 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'sequence',
		'title',
		array(
			'name'=>'class_room',
			'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->classRoom->title),
                                 array('course/view','id'=>$model->class_room_id)),		
		),
		array(
			'name'=>'experiment_type_id',
			'type'=>'raw',
            'value'=>$gMessages[$model->experiment_type_id],		
		),
		array(
			'name'=>'due_time',
			'type'=>'raw',
            'value'=>date_format(date_create($model->due_time),'Y年m月d日  H:i'),		
		),
		array(
			'label'=>'Begin~End',
			'type'=>'raw',
            'value'=>$model->begin.'~'.$model->end,		
		),
		array(
			'name'=>'aim',
			'type'=>'raw',
            'value'=>"<div>".$model->aim."</div>",		
		),
		array(
			'name'=>'description',
			'type'=>'raw',
            'value'=>"<div>".$model->description."</div>",		
		),
	),
)); ?>
<?php if(!(Yii::app()->user->isGuest)){?>
<div id="exercise">
	<?php if($model->exercise!==null && count($model->exercise->exercise_problems)>=1): ?>
		<?php $this->renderPartial('/exerciseProblem/_exercise_problems',array(
			'exercise'=>$model->exercise,
			'exerciseProblems'=>$model->exercise->exercise_problems,
		)); ?>
	<?php endif; ?>


</div><!-- exercise_problem -->
<?php } ?>
	<?php if(Yii::app()->user->hasFlash('exercise_problemSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('exercise_problemSubmitted'); ?>
		</div>
	<?php endif; ?>

<?php 
if ($exercise_problem!==null): 
echo CHtml::script('
function showDialogue()
{
	$("#submitiondialog").dialog("open");
	//this.blur();
	return false;	
}
');
	
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'submitiondialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Add a problem',
        'autoOpen'=>$exercise_problem->hasErrors(),
		'minWidth'=>800,
		'height'=>710,
		'modal'=>true,
    ),
));
?>
		<?php $this->renderPartial('/exerciseProblem/_form',array(
			'model'=>$exercise_problem,
		)); ?>

<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
endif;
?>

<?php

$this->widget('comments.widgets.ECommentsListWidget', array(
    'model' => $model,
));
?>
