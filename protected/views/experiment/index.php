<?php
$this->breadcrumbs=array(
	'Experiments',
);

$this->menu=array(
	array('label'=>'Create Experiment', 'url'=>array('create')),
	array('label'=>'Manage Experiment', 'url'=>array('admin')),
);
?>

<h1>Experiments</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
