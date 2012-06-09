<table>
<tr><th>Sequence</th><th>Name</th><th>Begin</th><th>End</th>
<?php 
if(UUserIdentity::isAdmin()||UUserIdentity::isTeacher())
{
	echo "<th>Operation</th>";
}
?>
</tr>
<?php
 foreach($classRooms as $classRoom): 
 ?>
<tr>
<td>
<?php echo CHtml::encode($classRoom->sequence);?>
</td>
<td>
	<div class="title">
		<?php echo CHtml::link(nl2br(CHtml::encode($classRoom->title)),$classRoom->getUrl(null)); ?>
	</div>
</td>
<td>
<?php echo CHtml::encode($classRoom->begin);?>
</td>
<td>
<?php echo CHtml::encode($classRoom->end);?>
</td>
<td>
<?php 
if(UUserIdentity::isAdmin()||($experiment->classRoom->user_id==Yii::app()->user->id))
{
	echo CHtml::link( "Delete",array("course/deleteClassRoom","id"=>$classRoom->id) ,array('confirm' =>Yii::t('course', 'Are you sure to delete the class?')));
}
?>
<!-- $classRoom -->
</td>
</tr>
<?php
endforeach; ?>
</table>
