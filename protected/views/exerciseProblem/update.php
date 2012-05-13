<?php
$this->breadcrumbs=array(
	'Exercise Problems'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ExerciseProblem', 'url'=>array('index')),
	array('label'=>'Create ExerciseProblem', 'url'=>array('create')),
	array('label'=>'View ExerciseProblem', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ExerciseProblem', 'url'=>array('admin')),
);
?>

<h1>Update ExerciseProblem <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>