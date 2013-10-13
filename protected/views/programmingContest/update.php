<?php
/* @var $this ProgrammingContestController */
/* @var $model ProgrammingContest */

$this->breadcrumbs=array(
	'Programming Contests'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ProgrammingContest', 'url'=>array('index')),
	array('label'=>'Create ProgrammingContest', 'url'=>array('create')),
	array('label'=>'View ProgrammingContest', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ProgrammingContest', 'url'=>array('admin')),
);
?>

<h1>Update ProgrammingContest <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>