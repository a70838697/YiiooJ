<?php
$this->breadcrumbs=array(
	Yii::t('course','Multiple choice questions')=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List MultipleChoice', 'url'=>array('index')),
	array('label'=>'Create MultipleChoice', 'url'=>array('create')),
	array('label'=>'View MultipleChoice', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage MultipleChoice', 'url'=>array('admin')),
);
$this->toolbar= array(
		array(
				'label'=>Yii::t('main','View'),
				'icon-position'=>'left',
				'icon'=>'document', // This a CSS class starting with ".ui-icon-"
				'visible'=>UUserIdentity::isAdmin()||UUserIdentity::isTeacher()||$model->user_id==yii::app()->user->id,
				'url'=>array('view', 'id'=>$model->id,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()),
				'linkOptions'=>array('onclick'=>'return confirm("Are you want to discard your updates");'),
		),
);
?>

<h1>Update MultipleChoice <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'choiceOptionManager'=>$choiceOptionManager,'chapters'=>$chapters,'type'=>$type)); ?>