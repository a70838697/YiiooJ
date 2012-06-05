<?php
$this->breadcrumbs=array(
	((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?'My course':'All courses',
);

$this->menu=array(
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>

<h1><?php echo ((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?'My ':'All';?> Courses</h1>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        ((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)?
    	array(
            'label'=>Yii::t('course','All courses'),
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('/course/index'),
	        'visible'=>true,
        ):
    	array(
            'label'=>Yii::t('course','My courses'),
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('/course/index/mine'),
	        'visible'=>true,
        ),
    	array(
            'label'=>Yii::t('course','Create course'),
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/course/create'),
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
	return apply_course($(this).attr("tag"),"/op/apply");
}
);
function apply_course(id,op)
{
	if(id!="")
	{
		$.get("'.CHtml::normalizeUrl(array("/course/apply/")).'"+"/"+id+op, function(data) {
				$.fn.yiiListView.update(\'yw0\');
			});
	}
	return false;
}
$(".capply").live("click", 
function ()
{
	return apply_course($(this).attr("tag"),"/op/cancel");
}
);

');
?>
