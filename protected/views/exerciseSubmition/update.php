<?php
if($model->exercise->type_id== Exercise::EXERCISE_TYPE_COURSE)
{
	$this->breadcrumbs=array(
		'Courses'=>array('/course/index'),
		$model->exercise->experiment->course->title=>array('/course/'.$model->exercise->experiment->course->id),
		$model->exercise->experiment->title=>array('/experiment/'.$model->exercise->experiment->id),
	);
	if($exercise_problem!=null)
	{
		$this->breadcrumbs[$exercise_problem->sequence.'.'.$exercise_problem->title]=array('/exerciseProblem/'.$exercise_problem->id);	
	}
	$this->breadcrumbs['submitions']='';
	
}

$this->menu=array(
	array('label'=>'List Submition', 'url'=>array('index')),
	array('label'=>'Create Submition', 'url'=>array('create')),
	array('label'=>'View Submition', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Submition', 'url'=>array('admin')),
);
?>

<h1>Update Submition <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'problem'=>$model->problem)); ?>