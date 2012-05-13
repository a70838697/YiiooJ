<?php
$this->breadcrumbs=array(
	'Problems'=>array('index'),
	$problem->title=>array('/problem/'.$problem->id),
	'Test data'=>array('/test/problem/'.$problem->id),
	$model->id=>array($model->id),
	'Update'
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
            'label'=>'view tests',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/test/problem/'.$problem->id),
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));

$this->menu=array(
	array('label'=>'List Test', 'url'=>array('index')),
	array('label'=>'Create Test', 'url'=>array('create')),
	array('label'=>'View Test', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Test', 'url'=>array('admin')),
);
?>

<h1>Update Test <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>