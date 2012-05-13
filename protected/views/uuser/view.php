<?php
$this->breadcrumbs=array(
	'Rank List'=>array('index'),
	$model->username,
);

?>

<h1>View User <?php echo $model->username; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('label'=>'Rank',
			'value'=>$rank),
		'acceptedCount',
		'submitedCount',
		'acceptedProbCount',
		'submitedProbCount',
		array('name'=>'createtime',
		'value'=>date('Y-m-d',$model->createtime)
		),
		array('name'=>'lastvisit',
		'value'=>date('Y-m-d h:i',$model->lastvisit)
		),
	),
)); 

?>
<div><center><h3>List of solved problems</h3></center>
<?php 
foreach($problemDataProvider->getData() as $problem)
{
	if($problem->hisAcceptedCount!=0)
	{
		echo(CHtml::link($problem->id, array("/problem/view","id"=>$problem->id)));
		echo '<font size="-2">';
		echo ($problem->hisAcceptedCount==0?"0":CHtml::link($problem->hisAcceptedCount,array("submition/index/status/1/user/".$model->id."/problem/".$problem->id)))
				."/".CHtml::link($problem->hisSubmitedCount,array("submition/index/user/".$model->id."/problem/".$problem->id));
		echo '</font>';
		echo " ";
	}
}
?>
</div>
<div><center><h3>List of unsolved problems</h3></center>
<?php 
foreach($problemDataProvider->getData() as $problem)
{
	if($problem->hisAcceptedCount==0)
	{
		echo(CHtml::link($problem->id, array("/problem/view","id"=>$problem->id)));
		echo '<font size="-2">';
		echo ($problem->hisAcceptedCount==0?"0":CHtml::link($problem->hisAcceptedCount,array("submition/index/status/1/user/".$model->id."/problem/".$problem->id)))
				."/".CHtml::link($problem->hisSubmitedCount,array("submition/index/user/".$model->id."/problem/".$problem->id));
		echo '</font>';
		echo " ";
	}
}
?>
</div>
<center><h3>Neighbours</h3></center>
<?php 	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rank-grid',
	'dataProvider'=>$rankDataProvider,
	'ajaxUpdate'=>true,
	'template'=>'{items}',
	'columns'=>array(
 
		array(
			'header'=>'rank',
			'type'=>'raw',
			'value'=>'$row+'.($rank>4?$rank-3:1),
		),
		array(
			'name'=>'id',
			'type'=>'raw',
			'value'=>'CHtml::link($data->username,array("view","id"=>$data->id))',
		),
		'acceptedProblemCount',
		'submissionCount',
		array(
			'name'=>'AC ratio',
			'type'=>'raw',
			'value'=>'$data->submissionCount>0?round($data->acceptedProblemCount*100.0/$data->submissionCount,1)."%":""',
		),		
	),
)); 
?>