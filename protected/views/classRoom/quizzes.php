<?php
$this->homelink=CHtml::link(CHtml::encode($model->course->title),array('/course/view','id'=>$model->course_id,'class_room_id'=>$model->id), array('class'=>'home'));
$this->breadcrumbs=array(
	CHtml::encode($model->title)."(".$this->classRoom->begin.")"=>array('view','id'=>$model->id),
	Yii::t("t",'Quizzes')
);
$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Update Course', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Course', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
?>

<center><font size='6'><?php echo CHtml::encode($model->title);?></font></center>
<table>
	<tr>
	<td><b><?php echo CHtml::encode($model->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/schoolInfo/view', 'id'=>$model->userinfo->user_id)); ?> | <?php echo CHtml::link(Yii::t('main',"send a message"), array("message/compose/". $model->user_id));?>
	<td><center><b><?php echo CHtml::encode($model->getAttributeLabel('due_time')); ?>:</b>
	<?php echo CHtml::encode($model->due_time); ?></center></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('location')); ?>:</b>
	<?php echo CHtml::encode($model->location); ?></td>
	</tr>
</table>
<?php
$this->toolbar= array(
       array(
            'label'=>Yii::t('course','Add a quiz'),
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>UUserIdentity::isAdmin(),
        	'linkOptions'=>array('onclick'=>'return showDialogue();',)
        ),
		/*
    	array(
            'label'=>Yii::t('course','Class information'),
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/classRoom/view/'.$model->id.''),
        ),*/
    );

?>

<h1>Quizzes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_quiz',
)); ?>
