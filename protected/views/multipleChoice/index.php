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
$this->toolbar= array(
        array(
            'label'=>Yii::t('course','View all questions'),
        	'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/multipleChoice/list/'.$root->root),
        ),
        array(
            'label'=>Yii::t('course','New multiple choice question'),
        	'icon-position'=>'left',
        	'visible'=>(UUserIdentity::isTeacher()) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/multipleChoice/create/'.$root->id),
        ),
    );
}
$this->widget('application.components.widgets.MathJax',array());
?>
<h1>Multiple Choices</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
