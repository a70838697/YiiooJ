<div class="view">
<table>
	<tr>
	<td style="width:40px;" align="right"><b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b></td>
	<td><?php echo CHtml::link(CHtml::encode($data->title),array('view', 'id'=>$data->id)); ?></td>
	<td style="width:108px"><b><?php echo CHtml::encode($data->getAttributeLabel('sequence')); ?>:</b></td>
	<td><?php echo CHtml::encode($data->sequence); ?></td>
	</tr>
	<tr>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b></td>
	<td><?php echo CHtml::link(CHtml::encode($data->userinfo->lastname.$data->userinfo->firstname),array('/user/user/view', 'id'=>$data->userinfo->user_id)); ?></td>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('location')); ?>:</b></td>
	<td><?php echo CHtml::encode($data->location); ?></td>
	</tr>
	<tr>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('begin')); ?>~<?php echo CHtml::encode($data->getAttributeLabel('end')); ?>:</b></td>
<td><?php echo ($data->begin); ?>~<?php echo ($data->end); ?></td>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('due_time')); ?>:</b></td>
	<td><?php echo CHtml::encode($data->due_time); ?></td>
	</tr>
	<tr>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('memo')); ?>:</b></td>
	<td><?php echo CHtml::encode($data->memo); ?></td>
	<td><b>Status:</b></td>
	<td><?php
	if(UUserIdentity::isStudent()){
		if($data->myMemberShip==null) echo '<input class="apply" tag="'.$data->id.'" type=button value="Apply"/>';
		else if($data->myMemberShip->status==GroupUser::USER_STATUS_APPLIED) echo '<input type=button class="capply" tag="'.$data->id.'" value="Cancel"/>';
		else if($data->myMemberShip->status==GroupUser::USER_STATUS_ACCEPTED) echo 'I am one of ';
		
		/*
		if($data->studentCount==1) echo '1 student';
		else echo $data->studentCount.' students';
				 */

		if($data->studentGroup==null) echo '0 students';
		else if($data->studentGroup->userCount==1) echo '1 student';
		else echo $data->studentGroup->userCount.' student';
		//var_dump($data->studentGroup->userCount);
	}
	 ?></td>
	</tr>
</table>
</div>
