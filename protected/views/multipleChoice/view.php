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
$fill="Fill";
if($model->question_type==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_MULTIPLE||
	$model->question_type==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE
	)$fill="";
if($model->chapter->course->hasMathFormula)
	$this->widget('application.components.widgets.MathJax',array());
$this->toolbar= array(
		array(
				'label'=>Yii::t('main','Update'),
				'icon-position'=>'left',
				'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
				'visible'=>true,
				'url'=>array('update'.$fill, 'id'=>$model->id,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()),
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
