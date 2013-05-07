<?php
/* @var $this ProgrammingContestController */
/* @var $model ProgrammingContest */

$this->breadcrumbs=array(
	'Programming Contests'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List ProgrammingContest', 'url'=>array('index')),
	array('label'=>'Create ProgrammingContest', 'url'=>array('create')),
	array('label'=>'Update ProgrammingContest', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ProgrammingContest', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ProgrammingContest', 'url'=>array('admin')),
);
$canUpdate=!Yii::app()->user->isGuest && UUserIdentity::isTeacher();

?>

<center><h1><?php echo $model->name; ?></h1></center>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
		'items' => array(
		/*
		 array(
		 		'label'=>'Menu button 1',
		 		'icon-position'=>'left',
		 		'url'=>array('create') //urls like 'create', 'update' & 'delete' generates an icon beside the button
		 ),
*/
				array(
						'label'=>'Update',
						'icon-position'=>'left',
						'visible'=>$canUpdate,
						'url'=>array('update', 'id'=>$model->id),
				),
				array(
						'label'=>'Students',
						'icon-position'=>'left',
						'visible'=>$canUpdate,
						'url'=>array('students', 'id'=>$model->id),
				),
			array(
					'label'=>Yii::t('t','Add a programming problem'),
					'icon-position'=>'left',
					'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
					'url'=>array('exerciseProblem/addProblemToProgrammingContest','id'=>$model->id),
					'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),
					'linkOptions'=>array('class'=>'create')
			),
				array(
						'label'=>Yii::t('course','View contest'),
						'icon-position'=>'left',
						'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
						'url'=>array('view','id'=>$model->id),
						'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
				),
				array(
						'label'=>'Submitions',
						'icon-position'=>'left',
						'visible'=>true,
						'url'=>array('/exerciseSubmition/index', 'exercise'=>$model->exercise_id),
				),
			/*			
				array(
						'label'=>'Rank',
						'icon-position'=>'left',
						'visible'=>!Yii::app()->user->isGuest,
						'icon'=>'circle-plus',
						'url'=>array('/'.$this->prefix.'submition/index/problem/'.$model->id.'/mine/1'),
				),
				*/
		),
		'htmlOptions' => array('style' => 'clear: both;'),
));
$APPPLICATION_MSG=ClassRoom::getApplicationOptionMessage();
 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        //'name',
		array(
			'label'=>'Begin ~ End',
            'type'=>'raw',
            'value'=>$model->begin . ' ~ ' .$model->end,
        ),
		array(
			'name'=>'description',
            'type'=>'raw',
            'value'=>'<div>'.$model->description.'</div>',
        ),
	),
));
$columns=array(
				array(
					'name'=>'rank',
					'value'=>'$data["rank"]',
				),
				array(
						'type' => 'raw',
						'name'=>'name',
						'value'=>'$data["username"]',
				),
				array(
						'name'=>'solved',
						'value'=>'$data["solveproblem"]',
				),
				array(
						'name'=>'used time',
						'value'=>'sprintf("%02d:%02d:%02d",$data["score"]/3600,$data["score"]/60%60,$data["score"]%60)',
						//'htmlOptions'=>array('style'=>'width:10px;')
				),
		);
 foreach($model->exercise->exercise_problems as $exercise_problem){
 	$columns[]=array(
		'name'=>$exercise_problem->sequence,
		'value'=>'$data["solved'.$exercise_problem->problem_id.'"].\'/\'.$data["total'.$exercise_problem->problem_id.'"]'
 	);
 }
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'effectivepolicy-grid',
		'dataProvider'=>$dataProvider,
		'emptyText'=>'no data found.',
		'nullDisplay'=>'-',
		'columns'=>$columns,
));