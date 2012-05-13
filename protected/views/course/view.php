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
	<?php echo CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),array('/user/user/view', 'id'=>$model->userinfo->user_id)); ?></td>
	<td><center><b><?php echo CHtml::encode($model->getAttributeLabel('due_time')); ?>:</b>
	<?php echo CHtml::encode($model->due_time); ?></center></td>
	<td align="right"><b><?php echo CHtml::encode($model->getAttributeLabel('location')); ?>:</b>
	<?php echo CHtml::encode($model->location); ?></td>
	</tr>
</table>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'View experiments',
            'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/course/experiments/'.$model->id),
        ),
        array(
            'label'=>'View students',
            'icon-position'=>'left',
        	'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/course/students/'.$model->id),
        ),            
        array(
            'label'=>'Update this course',
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
			'label'=>'Begin ~ End',
            'type'=>'raw',
            'value'=>$model->begin . ' ~ ' .$model->end,
        ),
		'due_time',
        'location',
        'environment',
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
