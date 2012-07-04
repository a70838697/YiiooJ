<div class="view">

	<!-- 
	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />
	 -->
	<b><?php echo CHtml::encode("Course"); ?>:</b>
	<?php echo CHtml::encode($data->chapter->book->name); ?>
	
	<?php 
	if($data->chapter->id!=$data->chapter->root){
	?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('chapter_id')); ?>:</b>
	<?php echo CHtml::encode($data->chapter->name); ?>
	<?php 
	}
	?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->id)); 
	echo ' | '.CHtml::link("Assign task",array("quiz/create",'class_room_id'=>$this->getClassRoomId(),'practice_id'=>$data->id)) ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('memo')); ?>:</b>
	<?php echo CHtml::encode($data->memo); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('examination_id')); ?>:</b>
	<?php echo CHtml::encode($data->examination_id); ?>
	<br />

	*/ ?>

</div>