<?php
/* @var $this ProgrammingContestController */
/* @var $model ProgrammingContest */

$this->breadcrumbs=array(
	'Programming Contests'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ProgrammingContest', 'url'=>array('index')),
	array('label'=>'Manage ProgrammingContest', 'url'=>array('admin')),
);
?>

<h1>Create ProgrammingContest</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>