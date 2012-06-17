<?php
$this->breadcrumbs=array(
	'Practices',
);

$this->menu=array(
	array('label'=>'Create Practice', 'url'=>array('create')),
	array('label'=>'Manage Practice', 'url'=>array('admin')),
);
?>

<h1>Practices</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
