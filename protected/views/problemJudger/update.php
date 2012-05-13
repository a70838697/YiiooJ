<?php
$this->breadcrumbs=array(
	'Problem Judgers'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ProblemJudger', 'url'=>array('index')),
	array('label'=>'Create ProblemJudger', 'url'=>array('create')),
	array('label'=>'View ProblemJudger', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ProblemJudger', 'url'=>array('admin')),
);
?>

<h1>Update ProblemJudger <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>