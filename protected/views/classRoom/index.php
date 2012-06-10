<?php
$this->breadcrumbs=array(
		((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?
		(Yii::app()->request->getQuery('term',null)!==null?Yii::t('course','My current classes'):Yii::t('course','My classes'))
		:(Yii::app()->request->getQuery('term',null)!==null?Yii::t('course','All current classes'):Yii::t('course','All classes'))
);

$this->menu=array(
		array('label'=>'Create Course', 'url'=>array('create')),
		array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>
<?php
$this->toolbar=array(
		array(
				'label'=>Yii::t('course','My current classes'),
				'icon-position'=>'left',
				'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
				'url'=>array('/classRoom/index/mine/1/term/1'),
				'visible'=>(!Yii::app()->user->isGuest) && (Yii::app()->request->getQuery('mine',null)===null || Yii::app()->request->getQuery('term',null)===null),
		),
		array(
				'label'=>Yii::t('course','My classes'),
				'icon-position'=>'left',
				'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
				'url'=>array('/classRoom/index/mine'),
				'visible'=>(!Yii::app()->user->isGuest) && (Yii::app()->request->getQuery('mine',null)===null || Yii::app()->request->getQuery('term',null)!==null),
		),
		array(
				'label'=>Yii::t('course','All current classes'),
				'icon-position'=>'left',
				'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
				'url'=>array('/classRoom/index/term'),
				'visible'=>(!Yii::app()->user->isGuest) && (Yii::app()->request->getQuery('mine',null)!==null || Yii::app()->request->getQuery('term',null)===null),
		),
		array(
				'label'=>Yii::t('course','All classes'),
				'icon-position'=>'left',
				'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
				'url'=>array('/classRoom/index'),
				'visible'=>(!Yii::app()->user->isGuest) && (Yii::app()->request->getQuery('mine',null)!==null || Yii::app()->request->getQuery('term',null)!==null),
		),
		array(
				'label'=>Yii::t('course','Create a class'),
				'icon-position'=>'left',
				'icon'=>'document',
				'url'=>array('/classRoom/create','id'=>$this->getCourseId()),
				'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin()
		),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProvider,
		'itemView'=>'_view',
)); ?>
<?php
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
