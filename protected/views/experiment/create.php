<?php
$this->breadcrumbs=array(
	Yii::t('course','My classes')=>array('/classRoom/index/mine/1'),
	$model->classRoom->title=>array('/classRoom/view','id'=>$model->class_room_id),
	Yii::t('course','Experiments')=>array('/classRoom/experiments','id'=>$model->class_room_id),
	Yii::t('main','Create')
);

$this->menu=array(
	array('label'=>'List Experiment', 'url'=>array('index')),
	array('label'=>'Manage Experiment', 'url'=>array('admin')),
);
?>

<h1>Create Experiment</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>