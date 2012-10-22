<?php
$this->breadcrumbs=array(
	Yii::t('course','My classes')=>array('/classRoom/index/mine/1'),
	$model->classRoom->title=>array('/classRoom/view','id'=>$model->class_room_id),
	Yii::t('course','Experiments')=>array('/classRoom/experiments','id'=>$model->class_room_id),
	Yii::t('main','Create')
);

echo $this->renderPartial('_form', array('model'=>$model));
