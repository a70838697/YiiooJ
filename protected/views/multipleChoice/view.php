<?php
$this->breadcrumbs=array(
	'Multiple Choices'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List MultipleChoice', 'url'=>array('index')),
	array('label'=>'Create MultipleChoice', 'url'=>array('create')),
	array('label'=>'Update MultipleChoice', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete MultipleChoice', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage MultipleChoice', 'url'=>array('admin')),
);
?>
<?php 
echo CHtml::link(
    'update', 
    array('update', 'id'=>$model->id));
 
?>
<div>
<?php echo $model->description; ?>
</div>
<table>
<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
 
<?php $this->renderPartial('_viewChoiceOption', array('id'=>$id, 'data'=>$choiceOption));?>
 
<?php endforeach;?>
</table>
