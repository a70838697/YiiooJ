<?php
$this->breadcrumbs=array(
	'Entries'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List Entry', 'url'=>array('index')),
	array('label'=>'Create Entry', 'url'=>array('create')),
	array('label'=>'Update Entry', 'url'=>array('update', 'id'=>$model->title)),
//	array('label'=>'Delete Entry', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Entry', 'url'=>array('admin')),
);
?>

<h1>Entry <?php echo  CHtml::encode($model->title); echo "  ".CHtml::link("Edit",array("update",'id'=>$model->title)); ?></h1>
<div class="view">
	<div>
	<?php
	Yii::import('application.extensions.SimpleWiki.ImWiki');
	
	$wiki=new ImWiki($model->content);
	 echo $wiki->get_html(); ?>
	</div>

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('create_time')); ?>:</b>
	<?php echo CHtml::encode($data->create_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('update_time')); ?>:</b>
	<?php echo CHtml::encode($data->update_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('accessed_time')); ?>:</b>
	<?php echo CHtml::encode($data->accessed_time); ?>
	<br />

	*/ ?>

</div>
