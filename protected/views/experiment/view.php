<?php
$this->breadcrumbs=array(
	'My Courses'=>array('/course/index/mine/1'),
	$model->course->title=>array('/course/view','id'=>$model->course_id),
	'Experiments'=>array('/course/experiments','id'=>$model->course_id),
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
            'label'=>'Submit a report',
            'icon-position'=>'left',
	        'visible'=>UUserIdentity::isStudent(),//!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$model),'update'),
            'url'=>array('/experimentReport/write', 'id'=>$model->id),
        ),         
      
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'sequence',
		'title',
		array(
			'name'=>'course_id',
			'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->course->title),
                                 array('course/view','id'=>$model->course_id)),		
		),
		array(
			'name'=>'experiment_type_id',
			'type'=>'raw',
            'value'=>UCourseLookup::$EXPERIMENT_TYPE_MESSAGES[$model->experiment_type_id],		
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