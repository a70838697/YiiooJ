<?php
$this->breadcrumbs=array(
	'Multiple Choices'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List MultipleChoice', 'url'=>array('index')),
	array('label'=>'Manage MultipleChoice', 'url'=>array('admin')),
);
?>
<?php echo $this->renderPartial('_form', array('model'=>$model,'choiceOptionManager'=>$choiceOptionManager,'chapters'=>$chapters,'type'=>$type)); ?>