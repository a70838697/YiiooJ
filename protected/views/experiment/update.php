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
	array('label'=>'View Experiment', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Experiment', 'url'=>array('admin')),
);
?>

<h1>Update Experiment <?php echo CHtml::encode( $model->title); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>