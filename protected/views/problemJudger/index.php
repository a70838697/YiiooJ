<?php
$this->breadcrumbs=array(
	'Problem Judgers',
);

$this->menu=array(
	array('label'=>'Create ProblemJudger', 'url'=>array('create')),
	array('label'=>'Manage ProblemJudger', 'url'=>array('admin')),
);
?>

<h1>Problem Judgers</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
