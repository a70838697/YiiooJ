<?php
$this->breadcrumbs=array(
	'Problems'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Problem', 'url'=>array('index')),
	array('label'=>'Create Problem', 'url'=>array('create')),
	array('label'=>'View Problem', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Problem', 'url'=>array('admin')),
);
?>

<h1>Update Problem <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('/problem/_form', array('model'=>$model)); ?>