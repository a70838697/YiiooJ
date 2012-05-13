<?php
$this->breadcrumbs=array(
	'Entries'=>array('index'),
	$model->title=>array('view','id'=>$model->title),
	'Update',
);

/*
$this->menu=array(
	array('label'=>'List Entry', 'url'=>array('index')),
	array('label'=>'Create Entry', 'url'=>array('create')),
	array('label'=>'View Entry', 'url'=>array('view', 'id'=>$model->title)),
	array('label'=>'Manage Entry', 'url'=>array('admin')),
);
*/
if($model->isNewRecord)
{
?>
<h1>Create Entry <?php echo $model->title; ?></h1>
<h3>The item "<?php echo $model->title; ?>" does not exist, please write one.</h3>
<?php }else{ ?>
<h1>Update Entry <?php echo $model->title; ?></h1>
<?php }?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>