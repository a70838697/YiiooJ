<?php
$this->breadcrumbs=array(
	'Practices'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Practice', 'url'=>array('index')),
	array('label'=>'Manage Practice', 'url'=>array('admin')),
);
?>

<h1>Create Practice</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'chapters'=>$chapters)); ?>