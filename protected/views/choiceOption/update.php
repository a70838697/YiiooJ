<?php
$this->breadcrumbs=array(
	'Choice Options'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ChoiceOption', 'url'=>array('index')),
	array('label'=>'Create ChoiceOption', 'url'=>array('create')),
	array('label'=>'View ChoiceOption', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ChoiceOption', 'url'=>array('admin')),
);
?>

<h1>Update ChoiceOption <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>