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
$this->widget('application.components.widgets.MathJax',array());
$this->toolbar= array(
		array(
				'label'=>Yii::t('main','Update'),
				'icon-position'=>'left',
				'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
				'visible'=>true,
				'url'=>array('update', 'id'=>$model->id),
		),
);
?>
<?php if($model->chapter){?>
<div>
Chapter:<?php echo $model->chapter->name; ?>
</div>
<?php }?>
<div>
<?php echo $model->description; ?>
</div>
<table>
<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
 
<?php $this->renderPartial('_viewChoiceOption', array('id'=>$id, 'data'=>$choiceOption));?>
 
<?php endforeach;?>
</table>
