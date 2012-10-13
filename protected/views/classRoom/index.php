<?php
$this->breadcrumbs=array();
if(!Yii::app()->user->isGuest)
{
	if(Yii::app()->request->getQuery('mine',null)!==null||Yii::app()->request->getQuery('term',null)!==null)
	{
		$this->breadcrumbs[Yii::t('t','All classrooms')]=array('/classRoom/index');
	}
	$this->breadcrumbs[((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?
		(Yii::app()->request->getQuery('term',null)!==null?Yii::t('t','My present classrooms'):Yii::t('t','My classrooms'))
		:(Yii::app()->request->getQuery('term',null)!==null?Yii::t('t','Available classrooms'):Yii::t('t','All classrooms'))]=null;
}

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
		$.get("'.CHtml::normalizeUrl(array("/classRoom/apply/")).'"+"/"+id+op, function(data) {
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
