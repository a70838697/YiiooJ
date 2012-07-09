<div class="view">

<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->name), array('practice/view', 'id'=>$data->practice_id, 'quiz'=>$data->id)); ?>
	<?php if(UUserIdentity::isTeacher()||UUserIdentity::isAdmin()) echo CHtml::link("results", array('quiz/students/'.($data->id))); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('memo')); ?>:</b>
	<?php echo CHtml::encode($data->memo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('begin')); ?>~</b>
	<b><?php echo CHtml::encode($data->getAttributeLabel('end')); ?>:</b>
	<?php echo CHtml::encode($data->begin); ?>~
	<?php echo CHtml::encode($data->end); ?>
	<br />


</div>