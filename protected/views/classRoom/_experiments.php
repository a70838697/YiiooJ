<table>
<tr><th>Sequence</th><th>Name</th><th>Due time</th><th>Deadline</th>
<?php 
if(UUserIdentity::isStudent())
{
	echo "<th>Score</th>";
}
if(UUserIdentity::isAdmin()||UUserIdentity::isTeacher())
{
	echo "<th>Closed</th><th>Operation</th>";
}
?>
</tr>
<?php
 foreach($experiments as $experiment): 
 if((!$experiment->isClosed)|| UUserIdentity::isTeacher() ||UUserIdentity::isAdmin()){
 ?>
<tr>
<td>
<?php echo CHtml::encode($experiment->sequence);?>
</td>
<td>
	<div class="title">
		<?php echo CHtml::link(nl2br(CHtml::encode($experiment->title)),$experiment->getUrl(null)); ?>
	</div>
</td>
<td>
	<div class="due_time">
		<?php echo date_format(date_create($experiment->due_time),'Y年m月d日  H:i'); ?>
	</div>
</td>
<td>
	<div class="deadline">
		<?php echo $experiment->begin."~".$experiment->end; ?>
	</div>
</td>
<td>
<?php 
if(UUserIdentity::isStudent())
{
	if(! ($experiment->myreport) )
	{
		if(!$experiment->isTimeOut())echo  CHtml::link( "Write",array("experimentReport/write","id"=>$experiment->id) );
	}
	else 
	{
		$report=$experiment->myreport;
		echo CHtml::link( ($report->score>0)?$report->score:($report->canEdit()?Yii::t('course',"Update"):Yii::t('course',"View")),array("experimentReport/view","id"=>$report->id) );
	}
	//echo "<td>".( ($experiment->myreport && $experiment->myreport->score>0)?$experiment->myreport->score:"")."</td>";
}
?>
<?php 
if(UUserIdentity::isAdmin()||($experiment->classRoom->user_id==Yii::app()->user->id))
{
?>
	<div>
	<font color="<?php echo $experiment->isClosed?"red":"" ?>">
		<?php echo Yii::t('t',$experiment->isClosed?"Yes":"No"); ?>
	</font>
	</div>
</td>
<td>
<?php
	echo CHtml::link( Yii::t('t',"Delete"),array("classRoom/deleteExperiment","id"=>$experiment->id) ,array('confirm' =>Yii::t('course', 'Are you sure to delete the experiment?')));
	echo "|".CHtml::link( Yii::t('t',"Update"),array("experiment/update","id"=>$experiment->id));
	echo "|".CHtml::link( Yii::t('t',$experiment->isClosed?"Open":"Close"),array("classRoom/deleteExperiment/type/close","id"=>$experiment->id) ,$experiment->isClosed?array():array('confirm' =>Yii::t('course', 'Are you sure to close the experiment?')));
}
?>
<!-- experiment -->
</td>
</tr>
<?php
}
endforeach; ?>
</table>
