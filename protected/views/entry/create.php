<?php
$this->breadcrumbs=array(
	'Entries'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Entry', 'url'=>array('index')),
	array('label'=>'Manage Entry', 'url'=>array('admin')),
);
?>

<h1>Create Entry</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>