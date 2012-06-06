<?php
$this->breadcrumbs=array(
	'Multiple Choices',
);

$this->menu=array(
	array('label'=>'Create MultipleChoice', 'url'=>array('create')),
	array('label'=>'Manage MultipleChoice', 'url'=>array('admin')),
);
?>

<?php
if(isset($root))
{
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>Yii::t('course','View course problems'),
        	'icon-position'=>'left',
        	//'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/multipleChoice/list/'.$root->root),
        ),
        array(
            'label'=>Yii::t('course','Create course problem'),
        	'icon-position'=>'left',
        	'visible'=>(UUserIdentity::isTeacher()) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/multipleChoice/create/'.$root->id),
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
}
$this->widget('application.components.widgets.MathJax',array());
?>
<h1>Multiple Choices</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
