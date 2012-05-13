<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'test-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		'id',
		'input_size',
		array(
			'header'=>'Input',
			'type'=>'raw',
			'value'=>'$data->input.($data->input_size>32?"...":"")'
		),
		'output_size',
		array(
			'header'=>'Output',
			'type'=>'raw',
			'value'=>'$data->output.($data->output_size>32?"...":"")'
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); 