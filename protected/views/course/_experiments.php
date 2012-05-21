<table>
<tr><th>Sequence</th><th>Name</th><th>Due time</th><th>Deadline</th>
<?php 
if(UUserIdentity::isStudent())
{
	echo "<th>Score</th>";
}
?>
</tr>
<?php
 foreach($experiments as $experiment): 
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
		echo CHtml::link( ($report->score>0)?$report->score:"View",array("experimentReport/view","id"=>$report->id) );
	}
	//echo "<td>".( ($experiment->myreport && $experiment->myreport->score>0)?$experiment->myreport->score:"")."</td>";
}
?>
<!-- experiment -->
</td>
</tr>
<?php
endforeach; ?>
</table>
