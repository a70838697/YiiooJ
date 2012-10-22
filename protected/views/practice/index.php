<?php
$this->homelink=CHtml::link(CHtml::encode($this->course->title),array('/course/view','id'=>$this->courseId,'class_room_id'=>$this->classRoomId), array('class'=>'home'));
$this->breadcrumbs=array(
	Yii::t('t','Practices')
);

$this->menu=array(
	array('label'=>'Create Practice', 'url'=>array('create')),
	array('label'=>'Manage Practice', 'url'=>array('admin')),
);
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
));
