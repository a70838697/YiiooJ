<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->title),array('/course/view','id'=>$data->id)); ?>

<?php if($data->sequence){?>
	<b><?php //echo CHtml::encode($data->getAttributeLabel('sequence')); ?>:</b>
	<?php echo "(".CHtml::encode($data->sequence).")"; ?>
<?php }?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->userinfo->lastname.$data->userinfo->firstname),array('/user/user/view', 'id'=>$data->userinfo->user_id));  ?>
	<br />

	<!-- 
	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />
	 -->
	<b><?php echo CHtml::encode($data->getAttributeLabel('memo')); ?>:</b>
	<?php echo CHtml::encode($data->memo); ?>
	<br />

<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('visibility')); ?>:</b>
	<?php echo CHtml::encode($data->visibility); ?>
	<br />*/
?>
	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('chapter_id')); ?>:</b>
	<?php echo CHtml::encode($data->chapter_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	*/ ?>
	<?php 
	foreach($data->classRooms as $classRoom)
	{
		echo $classRoom->begin."~".$classRoom->end."&nbsp;". CHtml::link(CHtml::encode($classRoom->title),array('/classRoom/view', 'id'=>$classRoom->id));
		echo "<br/>";
	}
		
	?>

</div>