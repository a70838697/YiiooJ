<?php
$this->breadcrumbs=array(
	'Exercise Problems',
);

$this->menu=array(
	array('label'=>'Create ExerciseProblem', 'url'=>array('create')),
	array('label'=>'Manage ExerciseProblem', 'url'=>array('admin')),
);
?>

<h1>Exercise Problems</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
