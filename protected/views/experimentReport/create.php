<?php
$this->breadcrumbs=array(
	'Experiment Reports'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ExperimentReport', 'url'=>array('index')),
	array('label'=>'Manage ExperimentReport', 'url'=>array('admin')),
);
?>

<h1>Create ExperimentReport</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>