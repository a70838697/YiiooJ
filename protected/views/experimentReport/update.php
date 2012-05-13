<?php
	$this->breadcrumbs=array(
		'My Courses'=>array('/course/index/mine/1'),
		$model->experiment->course->title=>array('/course/'.$model->experiment->course->id),
		'Experiments'=>array('/course/experiments','id'=>$model->experiment->course->id),	
		$model->experiment->title=>array('/experiment/'.$model->experiment->id),
		"Experiment Report",
	);

$this->menu=array(
	array('label'=>'List ExperimentReport', 'url'=>array('index')),
	array('label'=>'Create ExperimentReport', 'url'=>array('create')),
	array('label'=>'View ExperimentReport', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ExperimentReport', 'url'=>array('admin')),
);
?>

<h2>Write Experiment Report for <?php echo CHtml::encode($model->experiment->title); ?></h2>
<?php
$canEdit=UUserIdentity::isAdmin()
	||Yii::app()->user->id==$model->user_id
	||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->course->user_id);

$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'Save',
            'icon-position'=>'left',
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>$canEdit,
        	'linkOptions'=>array('onclick'=>'return saver();',)
        ),
        array(
            'label'=>'Preview',
            'icon-position'=>'left',
            'icon'=>'document', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>$canEdit,
        	'linkOptions'=>array('onclick'=>'return preview();',)
        ),
        array(
            'label'=>'Submit',
            'icon-position'=>'left',
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>false,
        	'linkOptions'=>array('onclick'=>'return submitr();',)
        ),
        
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
echo CHtml::script('
function preview()
{
	$("#experiment-report-form").attr("target","_blank");
	var action=$("#experiment-report-form").attr("action");
	$("#experiment-report-form").attr("action",action+"/preview/1");
	$("#experiment-report-form").submit();
	$("#experiment-report-form").attr("action",action);
	$("#experiment-report-form").attr("target","");
	return false;
}
function submitr()
{
	if(confirm("Are you really want to submit the report?\r\n You will not be allowed to modify it then."))
	{
		var action=$("#experiment-report-form").attr("action");
		$("#experiment-report-form").attr("action",action+"/submited/1");
		$("#experiment-report-form").submit();
		$("#experiment-report-form").attr("action",action);
	}
	return false;	
}
function saver()
{
	$("#experiment-report-form").submit();
	return true;	
	
}
');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>