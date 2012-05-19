<?php
$this->breadcrumbs=array(
	'My Courses'=>array('/course/index/mine/1'),
	$model->course->title=>array('/course/view','id'=>$model->course_id),
	'Experiments'=>array('/course/experiments','id'=>$model->course_id),
	$model->title=>array('/experiment','id'=>$model->course_id),
	'Reports'
);
?>
<h1>Experiment Reports for <?php echo CHtml::encode($model->title); ?></h1>
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
			'name'=>'Name',
			'type'=>'raw',
			'value'=>'CHtml::encode($data->user->info->lastname.$data->user->info->firstname)',
		),
		array(
			'name'=>'Student number',
			'type'=>'raw',
			'value'=>'CHtml::encode($data->user->schoolInfo->identitynumber)',
		),
		array(
			'header'=>'Login name',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->user->username),array("user/user/view","id"=>$data->user_id),  array("target"=>"_blank"))',
		),	
		array(
			'header'=>'Score',
			'type'=>'raw',
			'value'=>'$data->score!=0?$data->score:""',
		),
		array(
            'class'=>'CButtonColumn',
            'template' => '{view} ',
       		'viewButtonUrl'=>'array("/experimentReport/view/".$data->data)',
       		'buttons' => array(
       			'view'=>array(
       				'visible'=>'($data->data!=0)',
       				'options'=>array('target'=>'_blank'),
       			)
       		)
       
		)
	),
));
?>

