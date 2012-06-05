<?php
$this->breadcrumbs=array(
	'Choice Options',
);

$this->menu=array(
	array('label'=>'Create ChoiceOption', 'url'=>array('create')),
	array('label'=>'Manage ChoiceOption', 'url'=>array('admin')),
);
?>

<h1>Choice Options</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
