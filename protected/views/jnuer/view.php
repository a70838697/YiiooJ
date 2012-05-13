<?php
$this->breadcrumbs=array(
	'Jnuers'=>array('index'),
	$model->user_id,
);
if(UUserIdentity::isAdmin()||UUserIdentity::isCommonUser())
{
$this->menu=array(
	array('label'=>'Update Jnuer','visible'=>UUserIdentity::isAdmin()||UUserIdentity::isCommonUser(), 'url'=>array('update', 'id'=>$model->user_id)),
	array('label'=>'Delete Jnuer', 'visible'=>UUserIdentity::isAdmin(), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->user_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Jnuer', 'visible'=>UUserIdentity::isAdmin(), 'url'=>array('admin')),
);
}
?>

<h1>View JNUer <?php echo CHtml::encode($model->profile->lastname.$model->profile->firstname); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('label'=>'User name',
			'value'=>$model->user->username
		),
		'identitynumber',
		'first_year',
		array('label'=>'Unit',
			'value'=>$model->unit->title,
		),
		array('label'=>'Status',
			'value'=>Jnuer::$USER_STATUS_MESSAGES[$model->status]
		),
	),
)); ?>
