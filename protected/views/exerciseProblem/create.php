<?php
$this->breadcrumbs=array(
	'Exercise Problems'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ExerciseProblem', 'url'=>array('index')),
	array('label'=>'Manage ExerciseProblem', 'url'=>array('admin')),
);
?>

<h1>Create ExerciseProblem</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>