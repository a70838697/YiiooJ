<?php
$this->breadcrumbs=array(
	'Courses'=>array('index'),
	$model->title,
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
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/user/user/view', 'id'=>$model->userinfo->user_id)); ?> |  <?php echo CHtml::link(Yii::t('main',"send a message"), array("message/compose/". $model->user_id));?></td>
	</tr>
</table>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>Yii::t('course','View classes'),
        	'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/course/classRooms/'.$model->id),
        ),
        array(
            'label'=>Yii::t('course','View teachers'),
        	'icon-position'=>'left',
        	'visible'=>true,//(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/course/users/'.$model->id),
        ),
    	array(
            'label'=>Yii::t('course','Course content'),
    		'icon-position'=>'left',
	        'visible'=>!Yii::app()->user->isGuest && $model->chapter_id>0,
            'url'=>array('chapter/view', 'id'=>$model->chapter_id),
        ), 
    	array(
            'label'=>Yii::t('course','Update course'),
    		'icon-position'=>'left',
	        'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),
            'url'=>array('update', 'id'=>$model->id),
        ), 
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        //'name',
		array(
			'name'=>'user_id',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),
                                 array('user/user/view','id'=>$model->user_id)),
        ),
        array(
			'name'=>'visibility',
            'value'=>UCourseLookup::$COURSE_TYPE_MESSAGES[$model->visibility],
        ),
        array(
			'name'=>'created',
            'value'=>date('Y-m-d',$model->created),
        ),      
        'memo',
		array(
			'name'=>'description',
            'type'=>'raw',
            'value'=>'<div>'.$model->description.'</div>',
        ),
	),
)); ?>

