<?php
$this->breadcrumbs=array(
	'Submitions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Submition', 'url'=>array('index')),
	array('label'=>'Manage Submition', 'url'=>array('admin')),
);
?>

<h1>Create Submition</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'problem'=>$problem)); ?>