<?php
$this->breadcrumbs=array(
	'Choice Options'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ChoiceOption', 'url'=>array('index')),
	array('label'=>'Create ChoiceOption', 'url'=>array('create')),
	array('label'=>'Update ChoiceOption', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ChoiceOption', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ChoiceOption', 'url'=>array('admin')),
);
?>

<h1>View ChoiceOption #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'multipe_choice_id',
		'description',
		'create_time',
		'update_time',
		'user_id',
	),
)); ?>
