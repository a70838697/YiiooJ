<?php
$this->breadcrumbs=array(
	'Practices'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Practice', 'url'=>array('index')),
	array('label'=>'Create Practice', 'url'=>array('create')),
	array('label'=>'View Practice', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Practice', 'url'=>array('admin')),
);
?>

<h1>Update Practice <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>