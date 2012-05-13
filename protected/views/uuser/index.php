<?php
$this->breadcrumbs=array(
	'Rank list',
);

?>

<h1>Rank List</h1>

<?php 	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rank-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>true,
	'template'=>'{summary}{pager}{items}{pager}',
	'columns'=>array(
 
		array(
			'header'=>'rank',
			'type'=>'raw',
			'value'=>'$row+$this->grid->dataProvider->getPagination()->offset+1',
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
)); ?>
