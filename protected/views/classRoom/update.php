<?php
$this->homelink=CHtml::link(CHtml::encode($model->course->title),array('/course/view','id'=>$model->course_id,'class_room_id'=>$model->id), array('class'=>'home'));
$this->breadcrumbs=array(
	CHtml::encode($model->title)."(".$this->classRoom->begin.")"=>array('view','id'=>$model->id),
	Yii::t("t","Update")
);

echo $this->renderPartial('_form', array('model'=>$model));
