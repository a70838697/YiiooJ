<?php
$this->homelink=CHtml::link(CHtml::encode($model->course->title),array('/course/view','id'=>$model->course_id,'class_room_id'=>$model->id), array('class'=>'home'));
$this->breadcrumbs=array(
	CHtml::encode($model->title)."(".$this->classRoom->begin.")",
);
$this->toolbar=array();
$this->toolbar[]=array(
	'label'=>Yii::t('course','View students'),
	'icon-position'=>'left',
	'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
	'icon'=>'document',
	'url'=>array('/classRoom/students/'.$model->id),
);
if($model->hasExperiment)
	$this->toolbar[]=array(
		'label'=>Yii::t('course','View experiments'),
		'icon-position'=>'left',
		'visible'=>!Yii::app()->user->isGuest,
		'icon'=>'document',
		'url'=>array('/classRoom/experiments/'.$model->id),
	);
if($model->hasExperiment)
	$this->toolbar[]=array(
		'label'=>Yii::t('t','Experiment reports'),
		'icon-position'=>'left',
		'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
		'icon'=>'document',
		'url'=>array('/classRoom/reports/'.$model->id),
	);

if($model->hasExercise)
	$this->toolbar[]=array(
		'label'=>Yii::t('t','Quizzes'),
		'icon-position'=>'left',
		'visible'=>true,
		'icon'=>'document',
		'url'=>array('/classRoom/quizzes','id'=>$model->id),
	);
if($model->hasExercise)
	$this->toolbar[]=array(
		'label'=>Yii::t('t','Practices'),
		'icon-position'=>'left',
		'visible'=>true,
		'icon'=>'document',
		'url'=>array('/practice/index','class_room_id'=>$model->id),
	);
$this->toolbar[]=array(
	'label'=>Yii::t('t','Update classroom'),
	'icon-position'=>'left',
	'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),
	'url'=>array('update', 'id'=>$model->id),
);
?>

<center><font size='6'><?php echo CHtml::encode($model->title);?></font></center>
<table>
	<tr>
	<td><b><?php echo CHtml::encode($model->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/user/user/view', 'id'=>$model->userinfo->user_id)); ?> |  <?php echo CHtml::link(Yii::t('main',"send a message"), array("message/compose/". $model->user_id));?></td>
	<td><center><b><?php echo CHtml::encode($model->getAttributeLabel('due_time')); ?>:</b>
	<?php echo CHtml::encode($model->due_time); ?></center></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('location')); ?>:</b>
	<?php echo CHtml::encode($model->location); ?></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('hasExperiment')); ?>:</b>
	<?php echo $model->hasExperiment?"Have":"None"; ?></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('hasExercise')); ?>:</b>
	<?php echo $model->hasExercise?"Have":"None"; ?></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('showScore')); ?>:</b>
	<?php echo $model->showScore?"Show":"Hidden"; ?></td>
	</tr>
</table>
<?php

$APPPLICATION_MSG=ClassRoom::getApplicationOptionMessage();
 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        //'name',
		array(
			'name'=>'user_id',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),
                                 array('user/user/view','id'=>$model->user_id)),
        ),
		array(
			'label'=>'Begin ~ End',
            'type'=>'raw',
            'value'=>$model->begin . ' ~ ' .$model->end,
        ),
		'due_time',
        'location',
        'environment',
        array(
			'name'=>'application_option',
            'value'=>$APPPLICATION_MSG[$model->application_option],
        ),
		array(
			'name'=>'visibility',
            'value'=>UCourseLookup::$COURSE_TYPE_MESSAGES[$model->visibility],
        ),
        array(
			'name'=>'created',
            'value'=>date('Y-m-d',$model->created),
        ),      
        'memo',
		array(
			'name'=>'description',
            'type'=>'raw',
            'value'=>'<div>'.$model->description.'</div>',
        ),
	),
)); ?>
