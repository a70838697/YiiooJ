<?php
$this->homelink=CHtml::link(CHtml::encode($model->classRoom->course->title),array('/course/view','id'=>$model->classRoom->course_id,'class_room_id'=>$model->classRoom->id), array('class'=>'home'));
$this->breadcrumbs=array(
	CHtml::encode($model->title)."(".$this->classRoom->begin.")"=>array('classRoom/view','id'=>$model->class_room_id),
	Yii::t("t",'Experiments')=>array('classRoom/experiments','id'=>$model->class_room_id),
	CHtml::encode($model->title)=>array('view','id'=>$model->id),
	Yii::t("t",'Update')
);
echo $this->renderPartial('_form', array('model'=>$model));
