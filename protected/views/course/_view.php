<div class="view">
<table>
<tr>
<th width=20><?php echo CHtml::encode($data->getAttributeLabel('title')); ?></th>
<th>
	<?php echo CHtml::link(CHtml::encode($data->title),array('/course/view','id'=>$data->id)); ?>
</th>
<th width=200>
<?php if($data->sequence){?>
	<?php echo CHtml::encode($data->getAttributeLabel('sequence')).":"; echo CHtml::encode($data->sequence); ?>
<?php }?>
</th>
<th width=20>
	<?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>
</th>
<th width=100>
	<?php echo CHtml::link(CHtml::encode($data->userinfo->lastname.$data->userinfo->firstname),array('/user/user/view', 'id'=>$data->userinfo->user_id));  ?>
</th>
</tr>
<tr>
<th><?php echo CHtml::encode($data->getAttributeLabel('memo')); ?>:</th>
<td colspan=4>
	<?php echo CHtml::encode($data->memo); ?>
</td>
</tr>
<?php 
if($data->classRooms && count($data->classRooms)>0){
?>
<tr>
<td colspan=5><b><?php echo Yii::t('course','Classrooms')?>:</b><br/>
<table border=1>
<?php 
	foreach($data->classRooms as $classRoom)
	{
		echo "<tr><td>".$classRoom->begin."~".$classRoom->end."</td><td>". CHtml::link(CHtml::encode($classRoom->title),array('/classRoom/view', 'id'=>$classRoom->id));
		echo "</td></tr>";
	}
		
	?>
</table>
</td>
</tr>
<?php }?>
</table>
	
</div>