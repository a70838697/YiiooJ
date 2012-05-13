<?php
$this->breadcrumbs=array(
	'Experiment Reports',
);

$this->menu=array(
	array('label'=>'Create ExperimentReport', 'url'=>array('create')),
	array('label'=>'Manage ExperimentReport', 'url'=>array('admin')),
);
?>

<h1>Experiment Reports</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
