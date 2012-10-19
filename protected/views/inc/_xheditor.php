<?php
$arr_attrs=array(
	'model'=>$model,
	'modelAttribute'=>$field,
	'config'=>array(
		'tools'=>'full', // mini, simple, fill or from XHeditor::$_tools
		//see XHeditor::$_configurableAttributes for more
	),
	'htmlOptions'=>array(
		'rows'=>isset($rows)?(int)$rows:10,
		'style'=>isset($style)?$style:'',
		'cols'=>84,
	),
		
);
if(isset($config))
{
	$arr_attrs['config']=array_merge($arr_attrs['config'],$config);
}

if(isset($id))
{
	$arr_attrs['config']['id']=$id;
}
$this->widget('application.extensions.ultraeditor.XHeditor',$arr_attrs);