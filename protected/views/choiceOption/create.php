<?php
$this->breadcrumbs=array(
	'Choice Options'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ChoiceOption', 'url'=>array('index')),
	array('label'=>'Manage ChoiceOption', 'url'=>array('admin')),
);
?>

<h1>Create ChoiceOption</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>