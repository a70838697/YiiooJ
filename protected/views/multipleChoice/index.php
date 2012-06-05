<?php
$this->breadcrumbs=array(
	'Multiple Choices',
);

$this->menu=array(
	array('label'=>'Create MultipleChoice', 'url'=>array('create')),
	array('label'=>'Manage MultipleChoice', 'url'=>array('admin')),
);
?>

<h1>Multiple Choices</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
