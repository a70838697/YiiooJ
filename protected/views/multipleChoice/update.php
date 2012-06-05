<?php
$this->breadcrumbs=array(
	'Multiple Choices'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List MultipleChoice', 'url'=>array('index')),
	array('label'=>'Create MultipleChoice', 'url'=>array('create')),
	array('label'=>'View MultipleChoice', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage MultipleChoice', 'url'=>array('admin')),
);
echo CHtml::link(
		'view',
		array('view', 'id'=>$model->id));
?>

<h1>Update MultipleChoice <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'choiceOptionManager'=>$choiceOptionManager)); ?>