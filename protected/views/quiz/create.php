<?php
$this->breadcrumbs=array(
	'Quizs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Quiz', 'url'=>array('index')),
	array('label'=>'Manage Quiz', 'url'=>array('admin')),
);
?>

<h1>Create Quiz</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>