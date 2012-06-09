<?php
$this->breadcrumbs=array(
	Yii::t('course','My classes')=>array('/classRoom/index/mine/1'),
	$model->classRoom->title=>array('/classRoom/view','id'=>$model->class_room_id),
	Yii::t('course','Experiments')=>array('/classRoom/experiments','id'=>$model->class_room_id),
	$model->title,
);
$this->menu=array(
	array('label'=>'List Experiment', 'url'=>array('index')),
	array('label'=>'Create Experiment', 'url'=>array('create')),
	array('label'=>'View Experiment', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Experiment', 'url'=>array('admin')),
);
?>

<h2>Update Experiment <?php echo CHtml::encode( $model->title); ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>