<?php
$this->breadcrumbs=array(
	'My Courses'=>array('/course/index/mine/1'),
	$model->title=>array('view','id'=>$model->id),
	'Reports'
);

$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Update Course', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Course', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>
<h1>Experiment Reports for <?php echo CHtml::encode($model->title); ?></h1>

<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'View experiments',
            'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/course/experiments/'.$model->id),
        ),
        array(
            'label'=>'View students',
            'icon-position'=>'left',
        	'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/course/students/'.$model->id),
        ),
    	array(
            'label'=>'View this course',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/course/view/'.$model->id.''),
        ),
        array(
            'label'=>'Update this course',
            'icon-position'=>'left',
	        'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'url'=>array('update', 'id'=>$model->id),
        ),               
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>
<?php
$columns=array(
				array(
						'header'=>'Student number',
						'name'=>'schoolInfo.identitynumber',
						'type'=>'raw',
						'value'=>'CHtml::encode($data->schoolInfo->identitynumber)',
				),
				array(
						'header'=>'Name',
						'name'=>'name',
						'type'=>'raw',
						'value'=>'CHtml::encode($data->info->lastname.$data->info->firstname)',
				),
		/*
				array(
						'header'=>'Login name',
						'name'=>'username',
						'type'=>'raw',
						'value'=>'CHtml::link(CHtml::encode($data->username),array("user/user/view","id"=>$data->id),  array("target"=>"_blank"))',
				),
				*/
		/*
				array(
						'header'=>'Score',
						'name'=>'experimentReport.score',
						'type'=>'raw',
						'value'=>'$data->experimentReport!=null && $data->experimentReport->score>0?$data->experimentReport->score:""',
				),
				array(
						'class'=>'CButtonColumn',
						'template' => '{view} ',
						'viewButtonUrl'=>'array("/experimentReport/view/".( ($data->experimentReport!=null)?$data->experimentReport->id:""))',
						'buttons' => array(
								'view'=>array(
										'visible'=>'($data->experimentReport!=null)',
										'options'=>array('target'=>'_blank'),
								)
						)
						 
				)
				*/
		);

foreach($model->experiments as $experiment) 
{

	$columns[]=	array(
						'header'=>$experiment->sequence,
						'name'=>'score',
						'type'=>'raw',
						'value'=>'$data->getCourseExperimentColumn('.$model->id.','.$experiment->id.')',
				);
	
}
if(count($model->experiments)>0)
{
	$columns[]=	array(
			'header'=>"Average/Times",
			'type'=>'raw',
			'value'=>'$data->getAverageScore('.$model->id.')',
	);
	
}

$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'groupUser-grid',
		'dataProvider'=>$dataProvider,
		'ajaxUpdate'=>false,
		'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>4,),
		'template'=>'{summary}{pager}{items}{pager}',
		'columns'=>$columns,
));

echo UCHtml::cssFile('pager.css');
/*
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
*/
?>

