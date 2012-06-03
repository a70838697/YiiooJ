<?php
$this->breadcrumbs=array(
	'SchoolInfos'=>array('index'),
	$model->user_id,
);
if(UUserIdentity::isAdmin()||UUserIdentity::isCommonUser())
{
$this->menu=array(
	array('label'=>'Update SchoolInfo','visible'=>UUserIdentity::isAdmin()||UUserIdentity::isCommonUser(), 'url'=>array('update', 'id'=>$model->user_id)),
	array('label'=>'Delete SchoolInfo', 'visible'=>UUserIdentity::isAdmin(), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->user_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage SchoolInfo', 'visible'=>UUserIdentity::isAdmin(), 'url'=>array('admin')),
);
}
?>

<h1>View School Information <?php echo CHtml::encode($model->profile->lastname.$model->profile->firstname);?> <?php echo CHtml::link(Yii::t('main',"send a message"), array("message/compose/". $model->user_id));?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('label'=>'User name',
			'value'=>$model->user->username
		),
		'identitynumber',
		'first_year',
		array('label'=>'Unit',
			'value'=>$model->unit->name,
		),
		array('label'=>'Status',
			'value'=>SchoolInfo::$USER_STATUS_MESSAGES[$model->status]
		),
	),
)); ?>
