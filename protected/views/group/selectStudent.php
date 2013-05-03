Search by:   <b>Student number:</b><input id='identitynumber' value="<?php echo Yii::app()->request->getQuery('identitynumber',"");?>"  size ='5' onchange='research();' />
<?php
echo CHtml::script(
'
function research()
{
	var urlh=\''.CHtml::normalizeUrl(array("group/selectStudent/".$id)).'\';
	if($.trim($(\'#identitynumber\').val())!="")urlh+="/identitynumber/"+escape($.trim($(\'#identitynumber\').val()));
	$(\'#group-grid > div.keys\').attr(\'title\',urlh);
	$.fn.yiiGridView.update(\'group-grid\');
}
'
);

	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'group-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>true,
	'selectableRows'=>2,
	'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>0,),
	'template'=>'{pager}{items}{summary}',
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
		),
		array(
			'header'=>'Name',
			'type'=>'raw',
			'value'=>'CHtml::encode($data->profile->lastname.$data->profile->firstname)',
		),		
		array(
			'name'=>'identitynumber',
			'header'=>'Student number',
			'type'=>'raw',
		),
		array(
			'header'=>'Login name',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->user->username),array("user/user/view","id"=>$data->user_id),  array("target"=>"_blank"))',
		),		
	
	/*
		array(
			'name'=>'id',
			'type'=>'raw',
			'value'=>'"<div style=\'color:green\' alt=\'select me.\' title=\'select me.\' class=\'select_id\'>".$data->id.\'</div>\'',
		),
		array(
			'name'=>'title',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->title),array("'.$this->prefix.'problem/view","id"=>$data->id),  array("id"=>"ap".$data->id,"target"=>"_blank"))',
		),
		*/
	),
));
?>
<center>
<input id="dowithselected" type="button" value="Add the selected students"/>
</center>
