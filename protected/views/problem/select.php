
Search by:   <b>Category:</b> <select id='pro_type' onchange='research();'><option value='public'>Public</option><option value='mine'>Mine</option></select>
&nbsp;&nbsp;<b>ID:</b> <input id='prob_id'  size ='5' onchange='research();' />
&nbsp;&nbsp; <b>Problem Title:</b> <input  id='prob_title' maxLength='40'  size ='15'  onchange='research();' /> &nbsp;&nbsp;<input type=button onclick='research();' value="Search" />
<br/><font size="3">Click the green ID to select a problem Or <?php echo CHtml::link("Create a problem", array('/'.$this->prefix.'problem/create'))?> </font>
<?php
echo CHtml::script(
'
function research()
{
	var urlh=\''.CHtml::normalizeUrl(array($this->prefix."/problem/select")).'\';
	if($(\'#pro_type\').val()=="mine")urlh+="/mine/1";
	if($.trim($(\'#prob_id\').val())!=""){
		var id=parseInt($.trim($(\'#prob_id\').val()));
		if(id>0)urlh+="/id/"+id;
		else {alert("not a validate ID");return;}
	}
	if($.trim($(\'#prob_title\').val())!="")urlh+="/title/"+escape($.trim($(\'#prob_title\').val()));
	//alert(urlh);
	$(\'#problem-grid > div.keys\').attr(\'title\',urlh);
	$.fn.yiiGridView.update(\'problem-grid\');
}
'
);

	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'problem-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>true,
	'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>0,),
	'template'=>'{pager}{items}{summary}',
	'columns'=>array(
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
	),
));
?>