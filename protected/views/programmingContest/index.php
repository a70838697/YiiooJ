<?php
$this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProvider,
		'itemView'=>'_view',
)); 

echo CHtml::script('
		$(".apply").live("click",
		function ()
		{
		return apply_classRoom($(this).attr("tag"),"/op/apply");
}
);
		function apply_classRoom(id,op)
		{
		if(id!="")
		{
		$.get("'.CHtml::normalizeUrl(array("/programmingContest/apply/")).'"+"/"+id+op, function(data) {
		$.fn.yiiListView.update(\'yw0\');
});
}
		return false;
}
		$(".capply").live("click",
		function ()
		{
		return apply_classRoom($(this).attr("tag"),"/op/cancel");
}
);

		');
?>
