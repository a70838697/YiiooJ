<?php
$this->breadcrumbs=array(
	'Problem Judgers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ProblemJudger', 'url'=>array('index')),
	array('label'=>'Manage ProblemJudger', 'url'=>array('admin')),
);
?>

<h1>Create ProblemJudger</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>