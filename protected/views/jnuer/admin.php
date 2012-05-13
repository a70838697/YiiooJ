<?php
$this->breadcrumbs=array(
	'Jnuers'=>array('index'),
	'Manage',
);


Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('jnuer-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Jnuers</h1>

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
	'id'=>'jnuer-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'user_id',
		array(
			'header'=>'login',
			'value'=>'$data->user->username'
		),
		array(
			'header'=>'name',
			'value'=>'$data->profile->lastname.$data->profile->firstname'
		),
		'identitynumber',
		'first_year',
		array(
			'name'=>'status',
			'value'=>'Jnuer::$USER_STATUS_MESSAGES[$data->status]'
		),
		array(
			'header'=>'group',
			'value'=>'UUserIdentity::$GROUP_MESSAGES[$data->profile->group]'
		),
		array(
			'name'=>'unit_id',
			'value'=>'$data->unit->title'
		),
		array(
			'class'=>'CButtonColumn',
			'updateButtonOptions'=>array('target'=>'_blank'),
		),
	),
)); ?>
