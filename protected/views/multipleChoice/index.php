<?php
$this->breadcrumbs=array(
	'Exercise items',
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
        	'url'=>array('/multipleChoice/list/'.$root->root,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()),
        ),
		array(
            'label'=>Yii::t('course','Multiple choices single answer'),
        	'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/multipleChoice/list/'.$root->root,"type"=>ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()),
        ),
		array(
            'label'=>Yii::t('course','Multiple choices many answers'),
        	'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/multipleChoice/list/'.$root->root,"type"=>ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_MULTIPLE,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()),
        ),
		array(
            'label'=>Yii::t('course','Questions'),
        	'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/multipleChoice/list/'.$root->root,"type"=>ULookup::EXAMINATION_PROBLEM_TYPE_QUESTION,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()),
        ),
);
}
$this->widget('application.components.widgets.MathJax',array());
?>
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
