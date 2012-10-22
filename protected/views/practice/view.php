<?php
$this->homelink=CHtml::link(CHtml::encode($this->course->title),array('/course/view','id'=>$this->courseId,'class_room_id'=>$this->classRoomId), array('class'=>'home'));
$this->breadcrumbs=array(
	Yii::t('t',"Practices")=>array('/practice/index','course_id'=>$this->courseId,'class_room_id'=>$this->classRoomId),
	CHtml::encode($model->name),
);


$this->toolbar= array(
	array(
		'label'=>Yii::t('main','Update'),
		'icon-position'=>'left',
		'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
		'visible'=>UUserIdentity::isAdmin()||UUserIdentity::isTeacher(),
		'url'=>array('update', 'id'=>$model->id),
	),
);

//create an array open_nodes with the ids of the nodes that we want to be initially open
//when the tree is loaded.Modify this to suit your needs.Here,we open all nodes on load.
$categories= Examination::model()->findAll(array('condition'=>'root=:root_id','order'=>'lft','params'=>array(':root_id'=>$model->examination->id)));
$identifiers=array();
foreach($categories as $n=>$category)
{
	$identifiers[]="'".'node_'.$category->id."'";
}
$open_nodes=implode(',', $identifiers);

$baseUrl=Yii::app()->baseUrl;

$dataProvider=new CActiveDataProvider('Examination');
$this->renderPartial('/examination/index',array(
	'dataProvider'=>$dataProvider,
	'baseUrl'=> $baseUrl,
	'open_nodes'=> $open_nodes,
	'model'=>$model->examination,
	'quiz'=>$quiz,
));
