<?php
$this->breadcrumbs=array(
	'Uploads',
);

$this->menu=array(
	array('label'=>'Create Upload', 'url'=>array('create')),
	array('label'=>'Manage Upload', 'url'=>array('admin')),
);
?>

<h1>Uploads</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
