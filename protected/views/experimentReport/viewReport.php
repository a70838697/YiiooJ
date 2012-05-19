	<?php 
	$xueyuan="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_SCHOOLE)$node=$node->getParent();
		if($node!=null)$xueyuan=$node->title;
	}
	$xi="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_DEPARTMENT)$node=$node->getParent();
		if($node!=null)$xi=$node->title;
	}
	$zhuanye="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_MAJOR)$node=$node->getParent();
		if($node!=null)$zhuanye=$node->title;
	}
	
	?>

<?php 

$experimentInfomation='
<div style="width:750px">
<p style="text-align: center;" align="center"><b><span style="font-size: 22pt; font-family: ����_GB2312;">���ϴ�ѧ����ʵ�鱨��ר��ֽ</span></b></p>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:80px; font-family: ����_GB2312;">�γ����</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:500px;font-size: 14pt; font-family: ����_GB2312;">��'. $model->experiment->course->title.'��</td>
	<td style="width:80px;font-size: 14pt; font-family: ����_GB2312;">�ɼ�����</td>
	<td style="font-size: 14pt;border-bottom: solid 2px black;">'. ($model->score==0?'&nbsp;':$model->score) .'</td></tr>
</table>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: ����_GB2312;">ʵ����Ŀ���</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">'. $model->experiment->title.'</td>
	<td style="width:80px;font-size: 14pt; font-family: ����_GB2312;">ָ����ʦ</td>
	<td style="width:120px;font-size: 14pt;font-family: ����_GB2312;border-bottom: solid 2px black;text-align: center;">'.$model->experiment->course->user->info->lastname.$model->experiment->course->user->info->firstname.'</td></tr>
</table>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: ����_GB2312;">ʵ����Ŀ���</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: ����_GB2312;">'. $model->experiment->sequence.'</td>
	<td style="font-size: 14pt; width:120px; font-family: ����_GB2312;">ʵ����Ŀ����</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: ����_GB2312;">'. UCourseLookup::$EXPERIMENT_TYPE_MESSAGES[$model->experiment->experiment_type_id].'</td>
	<td style="width:80px;font-size: 14pt; font-family: ����_GB2312;">ʵ��ص�</td>
	<td style="font-size: 14pt;font-family: ����_GB2312;border-bottom: solid 2px black;text-align: center;">'. $model->experiment->course->location.'</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: ����_GB2312;">ѧ������</td>
	<td style="width:320px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">'.$model->user->info->lastname.$model->user->info->firstname.'</td>
	<td style="font-size: 14pt; width:40px; font-family: ����_GB2312;">ѧ��</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">'. ($model->user->schoolInfo==null?"&nbsp;":$model->user->schoolInfo->identitynumber).'</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:40px; font-family: ����_GB2312;">ѧԺ</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">
	'.$xueyuan.'
	</td>
	<td style="font-size: 14pt; width:20px; font-family: ����_GB2312;">ϵ</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">
	'.$xi.'
	
	</td>
	<td style="font-size: 14pt; width:40px; font-family: ����_GB2312;">רҵ</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">
	'. $zhuanye .'
	</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: ����_GB2312;">ʵ��ʱ��</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">'. date_format(date_create($model->experiment->due_time),'Y��m��d��  H:i').'</td>
	<td style="font-size: 14pt; width:40px; font-family: ����_GB2312;">�¶�</td>
	<td style="border-bottom: solid 2px black;width:40px; text-align:center; font-size: 14pt; font-family: ����_GB2312;">&nbsp;</td>
	<td style="font-size: 14pt; width:40px; font-family: ����_GB2312;">��</td>
	<td style="font-size: 14pt; width:40px; font-family: ����_GB2312;">ʪ��</td>
	<td style="width:60px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: ����_GB2312;">&nbsp;</td>
</tr>
</table>
<table style="height:31pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: ����;"><b>һ��ʵ��Ŀ��</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: ����;"><div>'.$model->experiment->aim.'</div></td>
</tr>
</table>
<table width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: ����;"><b>����ʵ�黷��</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: ����;"><div>'. $model->experiment->course->environment.'</div></td>
</tr>
</table>
</div>';
$experiment='<div>
<table width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: ����;"><b>��ʵ������</b></td>
</tr>';

if($model->experiment->exercise!=null)foreach($model->experiment->exercise->exercise_problems as $exerciseProblem){ 
	$experiment.='
<tr >
	<td style="font-size: 13pt;  font-family: ����;"><b>'. $exerciseProblem->sequence.CHtml::encode($exerciseProblem->title).'</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: ����;">'. $exerciseProblem->problem->description.'</td>
</tr>';
}
$experiment.='
<tr >
	<td style="font-size: 12pt;  font-family: ����;">'.$model->experiment->description.'</td>
</tr>
</table>
</div>';
?>
<?php $writeReport='
<div style="width:750px">
<table   width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: ����;"><b>�ġ�ʵ�����</b></td><td><b>�÷֣�</b>'. ($model->score==0?'δ��':$model->score).'</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: ����;" colspan=2>
		'. $model->report.'
	</td>
</tr>
</table>
<table style="height:31pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: ����;"><b>�塢ʵ��С��</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: ����;">����Ҫ����ͨ��ʵ���Ƿ�ﵽ����֪ʶ��ѧ���ڿ������޷��õ���֪ʶ�����Ŀ�ģ���������Щ�����д��ص���ߵģ��Լ���ʵ������ȣ�</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: ����;">
		'.$model->conclusion.' 
	</td>
</tr>
</table>
</div>';
?>
<?php
$this->beginClip('commentClip');
$this->widget('comments.widgets.ECommentsListWidget', array(
		'model' => $model,
));
$this->endClip();

$experiment_remarks='<div>
<table width="100%">
<tr >
	<td><b>�÷֣�</b>'. ($model->score==0?'δ��':$model->score).'</td>
</tr>
';
$experiment_remarks.='
<tr >
	<td><b>��ʦ���</b></td>
</tr>
<tr >
	<td style="font-size: 12pt; color:red; font-family: ����;">'. ( ($model->comment && strlen($model->comment)>0)?$model->comment:'No remarks!'). '</td>
</tr>';
$experiment_remarks.='
<tr >
	<td style="font-size: 8pt;  font-family: ����;">
	<hr/>
		 '.$this->clips['commentClip'].'
	</td>
</tr>
</table>
</div>';

$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array("Report of ".$model->user->info->lastname.$model->user->info->firstname=>$writeReport,
    		"Remarks"=>$experiment_remarks,
    		"Experiment Information"=>$experimentInfomation.$experiment,
    		),
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
    ),
));
?>


