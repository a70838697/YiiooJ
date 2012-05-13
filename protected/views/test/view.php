<?php
$this->breadcrumbs=array(
	'Problems'=>array('index'),
	$problem->title=>array('/problem/'.$problem->id),
	'Test data'=>array('/test/problem/'.$problem->id),
	$model->id
);
?>
<center><font size='6'><?php echo $problem->id.'. '.CHtml::encode($problem->title);?></font>
<?php echo ($problem->submitedCount==0)?"0%(0/0)":"".round($problem->acceptedCount*100.0/$problem->submitedCount,1)."%(".$problem->acceptedCount."/".$problem->submitedCount.")";?>
<font color='red'><?php echo $problem->time_limit.'ms,'.($problem->memory_limit>>20).'M'?></font>
</center>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'Update',
            'icon-position'=>'left',
            'url'=>array('update', 'id'=>$model->id),
        ), 
        array(
            'label'=>'view tests',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/test/problem/'.$problem->id),
        ),
        
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>

<h1>View Test #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'input_size',
		array('name'=>'input','type'=>'raw','value'=>nl2br(CHtml::encode($model->input))),
		'output_size',
		array('name'=>'output','type'=>'raw','value'=>nl2br(CHtml::encode($model->output))),
		array('name'=>'user_id','type'=>'raw','value'=>'<div>'.$model->user->username."</div>"),
		array('name'=>'description','type'=>'raw','value'=>'<div>'.$model->description."</div>"),
//		'created',
//		'modified',
	),
)); ?>
