<?php
$this->breadcrumbs=array(
	'Submitions'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Submition', 'url'=>array('index')),
	array('label'=>'Create Submition', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('submition-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Submitions</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'submition-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'user_id',
		'problem_id',
		'exercise_id',
		'source',
		'result',
		/*
		'used_time',
		'used_memory',
		'status',
		'compiler_id',
		'created',
		'modified',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
