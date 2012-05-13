<?php
$this->breadcrumbs=array(
	'Problems',
);

$this->menu=array(
	array('label'=>'Create Problem', 'url'=>array('create')),
	array('label'=>'Manage Problem', 'url'=>array('admin')),
);
?>

<h1>Problems</h1>

<?php
echo UCHtml::cssFile('pager.css');
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'problem-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>false,
	'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>15,),
	'template'=>'{summary}{pager}{items}{pager}',
	'columns'=>array(
		array(
			'name'=>'solved',
			'visible'=>!Yii::app()->user->isGuest,
			'type'=>'raw',
			'value'=>'$data->mySubmitedCount==0?"":(UCHtml::image($data->myAcceptedCount>0?"done.gif":"tried.gif").
			($data->myAcceptedCount==0?"0":CHtml::link($data->myAcceptedCount,array("'.$this->prefix.'submition/index/mine/1/problem/".$data->id)))
			."/".CHtml::link($data->mySubmitedCount,array("'.$this->prefix.'submition/index/mine/1/problem/".$data->id))
			)',
		),
		'id',
		array(
			'name'=>'title',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->title),array("view","id"=>$data->id))',
		),
		array(
			'header'=>'Ratio(Accepted/Total)',
			'type'=>'raw',
			'value'=>'($data->submitedCount==0)?"0%(0/0)":"".round($data->acceptedCount*100.0/$data->submitedCount,1)."%(".
			($data->acceptedCount==0?"0":CHtml::link($data->acceptedCount,array("'.$this->prefix.'submition/index/status/1/problem/".$data->id)))
			."/".CHtml::link($data->submitedCount,array("'.$this->prefix.'submition/index/problem/".$data->id))
			.")"',
		),
	),
));
?>
