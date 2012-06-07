<?php
$this->breadcrumbs=array(
	((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?Yii::t('course','My courses'):Yii::t('course','All courses'),
);

$this->menu=array(
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        ((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?
    	array(
            'label'=>Yii::t('course','All classes'),
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('/classRoom/index'),
	        'visible'=>true,
        ):
    	array(
            'label'=>Yii::t('course','My classes'),
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('/classRoom/index/mine'),
	        'visible'=>true,
        ),
    	array(
            'label'=>Yii::t('course','Create class'),
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/classRoom/create'),
       		'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin()
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
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
