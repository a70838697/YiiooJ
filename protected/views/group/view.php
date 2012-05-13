<h1><?php 
if($model->type_id==Group::GROUP_TYPE_COURSE)echo'Students';
if($model->type_id==Group::GROUP_TYPE_TEAM)echo'Members';
?></h1>
<?php

echo UCHtml::cssFile('pager.css');
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'groupUser-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>false,
	'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>4,),
	'template'=>'{summary}{pager}{items}{pager}',
	'columns'=>array(
		array(
			'name'=>'User',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->user->username),array("user/user/view","id"=>$data->user->id))',
		),
		array(
			'name'=>'Name',
			'type'=>'raw',
			'value'=>'CHtml::encode($data->user->info->lastname.$data->user->info->firstname)',
		),
		array(
			'name'=>'Student number',
			'type'=>'raw',
			'value'=>'CHtml::encode($data->user->jnuer->identitynumber)',
		),
		array(
			'name'=>'Status',
			'type'=>'raw',
			'value'=>'GroupUser::$USER_STATUS_MESSAGES[$data->status]',
		),
		array(
			'name'=>'Action',
			'type'=>'raw',
			'value'=>'\'<input type="button" class="capply" tag="\'.$data->id .\'" value="Reject">\' .($data->status==GroupUser::USER_STATUS_APPLIED?\'<input class="apply" tag="\'.$data->id .\'" value="Accept"  type="button">\':"") ',
		),		
	),
));
?>

