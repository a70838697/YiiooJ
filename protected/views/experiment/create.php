<?php
$this->breadcrumbs=array(
	'Experiments'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Experiment', 'url'=>array('index')),
	array('label'=>'Manage Experiment', 'url'=>array('admin')),
);
?>

<h1>Create Experiment</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>