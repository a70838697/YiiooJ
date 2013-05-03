<div class="view">
<table>
	<tr>
	<td style="width:40px;" align="right"><b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b></td>
	<td><?php echo CHtml::link(CHtml::encode($data->name),array('view', 'id'=>$data->id)); ?></td>
	<td style="width:108px"><b></b></td>
	<td></td>
	</tr>
	<tr>
	<td><b><?php $data->denyStudent(); echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b></td>
	<td><?php echo CHtml::link(CHtml::encode($data->userinfo->lastname.$data->userinfo->firstname),array('/user/user/view', 'id'=>$data->userinfo->user_id)); ?></td>
	<td></td>
	<td></td>
	</tr>
	<tr>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('begin')); ?>~<?php echo CHtml::encode($data->getAttributeLabel('end')); ?>:</b></td>
<td><?php echo ($data->begin); ?>~<?php echo ($data->end); ?></td>
	<td></td>
	<td></td>
	</tr>
	<tr>
	<td><b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b></td>
	<td><?php echo CHtml::encode($data->description); ?></td>
	<td><b>Status:</b></td>
	<td><?php
	if(UUserIdentity::isStudent()){
		if($data->myMemberShip==null && $data->application_option!=ClassRoom::STUDENT_APPLICATION_OPTION_DENY) echo '<input class="apply" tag="'.$data->id.'" type=button value="Apply"/>';
		else if($data->myMemberShip->status==GroupUser::USER_STATUS_APPLIED) echo '<input type=button class="capply" tag="'.$data->id.'" value="Cancel"/>';
		else if($data->myMemberShip->status==GroupUser::USER_STATUS_ACCEPTED) echo Yii::t('t','Selected classroom').".";
		
		/*
		if($data->studentCount==1) echo '1 student';
		else echo $data->studentCount.' students';
				 */
		//var_dump($data->studentGroup->userCount);
	}
		echo "Total:";
		if($data->studentGroup==null) echo '0 students';
		else if($data->studentGroup->userCount==1) echo '1 student';
		else echo $data->studentGroup->userCount.' students';
	?></td>
	</tr>
</table>
</div>