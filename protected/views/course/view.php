<?php
$this->homelink=CHtml::link(CHtml::encode($model->title),array('/course/view','id'=>$model->id,'class_room_id'=>$this->getClassRoomId()), array('class'=>'home'));
$this->breadcrumbs=array(
	Yii::t('t','Course introduction'),
);
/*
$this->breadcrumbs=array(
	Yii::t('main','Courses')=>array('index'),
	$model->title,
);
*/

$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Update Course', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Course', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
$this->toolbar=array(
        array(
            'label'=>Yii::t('course','View classrooms'),
        	'icon-position'=>'left',
        	'visible'=>true,
            'icon'=>'document',
        	'url'=>array('/course/classRooms/'.$model->id,'class_room_id'=>$this->getClassRoomId()),
        ),
    	array(
            'label'=>Yii::t('course','Course content'),
    		'icon-position'=>'left',
	        'visible'=>$model->chapter_id>0,
            'url'=>array('chapter/view', 'id'=>$model->chapter_id,'class_room_id'=>$this->getClassRoomId()),
        ), 
    	array(
            'label'=>Yii::t('course','Update course'),
    		'icon-position'=>'left',
	        'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),
            'url'=>array('update', 'id'=>$model->id,'class_room_id'=>$this->getClassRoomId()),
        ), 
    );
?>
<center><font size='6'><?php echo CHtml::encode($model->title);?></font></center>
<table>
	<tr>
	<td><b><?php echo CHtml::encode($model->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/user/user/view', 'id'=>$model->userinfo->user_id)); ?> |  <?php echo CHtml::link(Yii::t('main',"send a message"), array("message/compose/". $model->user_id));?></td>
	</tr>
</table>


<div>

<?php 
echo "<b>".Yii::t("course","Course introduction").":</b><br/>";
echo $model->description;
?>
</div>
