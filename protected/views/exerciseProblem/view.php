<?php
if(!isset($model->exercise))
	$this->breadcrumbs=array(
		'Problems'=>array('index'),
		$problem->title,
	);
else if($model->exercise->type_id== Exercise::EXERCISE_TYPE_COURSE)
	$this->breadcrumbs=array(
		'My Courses'=>array('/course/index/mine/1'),
		$model->exercise->experiment->course->title=>array('/course/'.$model->exercise->experiment->course->id),
		'Experiments'=>array('/course/experiments','id'=>$model->exercise->experiment->course->id),	
		$model->exercise->experiment->title=>array('/experiment/'.$model->exercise->experiment->id),
		$model->title,
	);

/*
$this->menu=array(
	array('label'=>'List Problem', 'url'=>array('index')),
	array('label'=>'Create Problem', 'url'=>array('create')),
	array('label'=>'Update Problem', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Problem', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),),
);
*/

$attrs=$problem->attributeLabels();

?>



<center><font size='6'><?php echo CHtml::encode($model->title);?></font>
<?php echo ($problem->submitedCount==0)?"0%(0/0)":"".round($problem->acceptedCount*100.0/$problem->submitedCount,1)."%(".$problem->acceptedCount."/".$problem->submitedCount.")";?>
<font color='red'><?php echo $problem->time_limit.'ms,'.($problem->memory_limit>>20).'M'?></font>
</center>
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
            'label'=>'Submit a solution',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>!Yii::app()->user->isGuest&& $buttons['submit'],
        	'linkOptions'=>array('onclick'=>'return showDialogue();',)
        ),
        array(
            'label'=>'Update this problem',
            'icon-position'=>'left',
	        'visible'=>!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$problem),'update'),
            'url'=>array('update', 'id'=>$problem->id),
        ), 
        array(
            'label'=>'My submitions to this problem',
            'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'circle-plus',
        	'url'=>array('/exerciseSubmition/index/exercise/'.$model->exercise_id.'/problem/'.$problem->id.'/mine/1'),
        ),        
        array(
            'label'=>'Submitions statistics',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/exerciseSubmition/index/exercise/'.$model->exercise_id.'/problem/'.$problem->id.''),
        ),
        array(
            'label'=>'Accepted Submitions',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/exerciseSubmition/index/exercise/'.$model->exercise_id.'/problem/'.$problem->id.'/status/1'),
        ),                 
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
echo CHtml::script('
function showDialogue()
{
	$("#submitiondialog").dialog("open");
	//this.blur();
	return false;	
}
');
$tabs=array(
		$attrs['description']=>'<div>'.($problem->description).'</div>',
);
if(strlen($problem->input)>0) $tabs[$attrs['input']]='<div>'.($problem->input).'</div>';
if(strlen($problem->output)>0) $tabs[$attrs['output']]='<div>'.($problem->output).'</div>';
if(strlen($problem->input_sample)>0) $tabs[$attrs['input_sample']]='<pre style="border: 1px solid blue">'.($problem->input_sample)."\r\n".'</pre>';
if(strlen($problem->output_sample)>0) $tabs[$attrs['output_sample']]='<pre style="border: 1px solid blue">'.($problem->output_sample)."\r\n".'</pre>';
if(strlen(trim($problem->hint))>0) $tabs[$attrs['hint']]='<div>'.($problem->hint).'</div>';

$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
    ),
));
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$problem,
	'attributes'=>array(
        array(
			'name'=>'user_id',
        	'label'=>'Recommend',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($problem->user->username),
                                 array('user/user/view','id'=>$problem->user_id)),
        ), 
        'source',
		'created',
	),
)); ?>

<?php 
if($buttons['submit']){
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'submitiondialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Submit a solution',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>500,
		'modal'=>true,
    ),
));
?>

<div id="submition">
	<?php if(Yii::app()->user->hasFlash('submitionSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('submitionSubmitted'); ?>
		</div>
	<?php else: ?>
		<?php $this->renderPartial('/submition/_form',array(
			'model'=>$submition,
			'problem'=>$problem,
			'exer'
		)); ?>
	<?php endif; ?>

</div><!-- submition -->
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
}
?>

