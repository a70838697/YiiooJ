<?php
$this->homelink=CHtml::link(CHtml::encode($model->title),array('/course/view','id'=>$model->id,'class_room_id'=>$this->getClassRoomId()), array('class'=>'home'));
$this->breadcrumbs=array(
	Yii::t("t","Course introduction")=>array('view','id'=>$model->id,'class_room_id'=>$this->getClassRoomId()),
	Yii::t("t",'Update'),
);

$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'View Course', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>

<h1>Update Course <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>