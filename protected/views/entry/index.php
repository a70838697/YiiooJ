<?php
$this->breadcrumbs=array(
	'Entries',
);

$this->menu=array(
	array('label'=>'Create Entry', 'url'=>array('create')),
	array('label'=>'Manage Entry', 'url'=>array('admin')),
);
?>

<h1>Entries</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
