<?php
$this->breadcrumbs=array(
	Yii::t('course','My courses')=>array('course/index/mine/1'),
	Yii::t('course','Create course'),
);

$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
$this->contentMenu=null;
?>

<h1><?php echo Yii::t('course','Create course');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>