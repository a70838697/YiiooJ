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

?>
<h1>View Submition #<?php echo $model->id; ?> <?php echo  ' to '.CHtml::link($exercise_problem->sequence.'.'.CHtml::encode($exercise_problem->title),array("exerciseProblem/view","id"=>$exercise_problem->id));?></h1>
<?php
if(!Yii::app()->user->isGuest)
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
            'label'=>'Update this submition',
            'icon-position'=>'left',
	        'visible'=>$this->canAccess(array('model'=>$model),'update','submition'),
            'url'=>array('update', 'id'=>$model->id),
        ), 
        array(
            'label'=>'My submitions to the problem',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/exerciseSubmition/index/problem/'.$model->problem_id.'/mine/1/exercise/'.$model->exercise->id),
        ),
        array(
            'label'=>'Accepted submitions to the problem',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/exerciseSubmition/index/problem/'.$model->problem_id.'/status/1/exercise/'.$model->exercise->id),
        ),
         
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>
<?php 
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
        array(
            'name'=>'problem.title',
        	'label'=>'Problem',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($exercise_problem->title),
                                 array('exerciseProblem/view','id'=>$exercise_problem->id)),
        ),
        array(
			'name'=>'user_id',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->user->username),
                                 array('user/user/view','id'=>$model->user_id)),
        ),      
		array(
            'name'=>'status',
            'type'=>'raw',
            'value'=> '<div style="display:none" id="loading">'.UCHtml::image("loading.gif").'loading</div><div id="toRefresh1">'.ULookup::$JUDGE_RESULT_MESSAGES[$model->status].'</div>',
        ),
		array(
            'name'=>'used_time',
            'type'=>'raw',
            'value'=>($model->used_time).'ms',
			'visible'=>$model->status==ULookup::JUDGE_RESULT_ACCEPTED,
        ),
        array(
            'name'=>'used_memory',
            'type'=>'raw',
            'value'=>($model->used_memory>>10).'K',
			'visible'=>$model->status==ULookup::JUDGE_RESULT_ACCEPTED,
        ),
        array(
			'name'=>'result',
            'type'=>'raw',
            'value'=>'<pre id="toRefresh2">'.CHtml::encode($model->result).'</pre>',
			//'visible'=>strlen($model->result)>0,
        ),
		'created',
        array(
            'name'=>'modified',
        	'visible'=>($model->created!=$model->modified),
        ),        
        array(
            'name'=>'compiler_id',
            'type'=>'raw',
            'value'=>CHtml::encode(UCompilerLookup::item($model->compiler_id)),
        ),
        array(
            'name'=>'source',
            'type'=>'raw',
        	'template'=>'<tr class="even"><td colspan=2><b>Source</b></br>{value}</td></tr>',
            'value'=>'<pre class="brush :'.UCompilerLookup::ext($model->compiler_id).'">'.CHtml::encode($model->source).'</pre>',
        	'visible'=>(Yii::app()->user->id==$model->user_id)||(UUserIdentity::isTeacher())||(UUserIdentity::isAdmin()),
        ),
    ),
)); ?>

<?php ?>
<?php Yii::app()->syntaxhighlighter->addHighlighter(); ?>

<?php if($model->status==ULookup::JUDGE_RESULT_PENDING){
echo CHtml::script('
$("#loading").show();
function refreshsubmition()
{
	$(\'#toRefresh1\').html(\'\');
	$.getJSON(\''.$model->getUrl().'\',function(data){if(data.ok){
			$(\'#toRefresh1\').html(data.status);
			$(\'#toRefresh2\').html(data.result);
		   $("#loading").hide();
		}
		else setTimeout("refreshsubmition()",2000);
	})
}
refreshsubmition();
');
}
?>
