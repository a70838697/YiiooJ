<?php
$this->breadcrumbs=array(
	'Problem Judgers'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ProblemJudger', 'url'=>array('index')),
	array('label'=>'Create ProblemJudger', 'url'=>array('create')),
	array('label'=>'Update ProblemJudger', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ProblemJudger', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ProblemJudger', 'url'=>array('admin')),
);
?>

<h1>View ProblemJudger #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'problem_id',
		'source',
		'user_id',
		'compiler_id',
		'created',
	),
)); ?>
