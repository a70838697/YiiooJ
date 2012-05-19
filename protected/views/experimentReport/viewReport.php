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
<p style="text-align: center;" align="center"><b><span style="font-size: 22pt; font-family: 锟斤拷锟斤拷_GB2312;">锟斤拷锟较达拷学锟斤拷锟斤拷实锟介报锟斤拷专锟斤拷纸</span></b></p>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:80px; font-family: 锟斤拷锟斤拷_GB2312;">锟轿筹拷锟斤拷锟�/td>
	<td style="border-bottom: solid 2px black; text-align:center; width:500px;font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">锟斤拷'. $model->experiment->course->title.'锟斤拷</td>
	<td style="width:80px;font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">锟缴硷拷锟斤拷锟斤拷</td>
	<td style="font-size: 14pt;border-bottom: solid 2px black;">'. ($model->score==0?'&nbsp;':$model->score) .'</td></tr>
</table>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: 锟斤拷锟斤拷_GB2312;">实锟斤拷锟斤拷目锟斤拷锟�/td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">'. $model->experiment->title.'</td>
	<td style="width:80px;font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">指锟斤拷锟斤拷师</td>
	<td style="width:120px;font-size: 14pt;font-family: 锟斤拷锟斤拷_GB2312;border-bottom: solid 2px black;text-align: center;">'.$model->experiment->course->user->info->lastname.$model->experiment->course->user->info->firstname.'</td></tr>
</table>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: 锟斤拷锟斤拷_GB2312;">实锟斤拷锟斤拷目锟斤拷锟�/td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">'. $model->experiment->sequence.'</td>
	<td style="font-size: 14pt; width:120px; font-family: 锟斤拷锟斤拷_GB2312;">实锟斤拷锟斤拷目锟斤拷锟斤拷</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">'. UCourseLookup::$EXPERIMENT_TYPE_MESSAGES[$model->experiment->experiment_type_id].'</td>
	<td style="width:80px;font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">实锟斤拷氐锟�/td>
	<td style="font-size: 14pt;font-family: 锟斤拷锟斤拷_GB2312;border-bottom: solid 2px black;text-align: center;">'. $model->experiment->course->location.'</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: 锟斤拷锟斤拷_GB2312;">学锟斤拷锟斤拷锟斤拷</td>
	<td style="width:320px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">'.$model->user->info->lastname.$model->user->info->firstname.'</td>
	<td style="font-size: 14pt; width:40px; font-family: 锟斤拷锟斤拷_GB2312;">学锟斤拷</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">'. ($model->user->schoolInfo==null?"&nbsp;":$model->user->schoolInfo->identitynumber).'</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:40px; font-family: 锟斤拷锟斤拷_GB2312;">学院</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">
	'.$xueyuan.'
	</td>
	<td style="font-size: 14pt; width:20px; font-family: 锟斤拷锟斤拷_GB2312;">系</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">
	'.$xi.'
	
	</td>
	<td style="font-size: 14pt; width:40px; font-family: 锟斤拷锟斤拷_GB2312;">专业</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">
	'. $zhuanye .'
	</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: 锟斤拷锟斤拷_GB2312;">实锟斤拷时锟斤拷</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">'. date_format(date_create($model->experiment->due_time),'Y锟斤拷m锟斤拷d锟斤拷  H:i').'</td>
	<td style="font-size: 14pt; width:40px; font-family: 锟斤拷锟斤拷_GB2312;">锟铰讹拷</td>
	<td style="border-bottom: solid 2px black;width:40px; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">&nbsp;</td>
	<td style="font-size: 14pt; width:40px; font-family: 锟斤拷锟斤拷_GB2312;">锟斤拷</td>
	<td style="font-size: 14pt; width:40px; font-family: 锟斤拷锟斤拷_GB2312;">湿锟斤拷</td>
	<td style="width:60px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 锟斤拷锟斤拷_GB2312;">&nbsp;</td>
</tr>
</table>
<table style="height:31pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 锟斤拷锟斤拷;"><b>一锟斤拷实锟斤拷目锟斤拷</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;"><div>'.$model->experiment->aim.'</div></td>
</tr>
</table>
<table width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 锟斤拷锟斤拷;"><b>锟斤拷锟斤拷实锟介环锟斤拷</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;"><div>'. $model->experiment->course->environment.'</div></td>
</tr>
</table>
</div>';
$experiment='<div>
<table width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 锟斤拷锟斤拷;"><b>锟斤拷实锟斤拷锟斤拷锟斤拷</b></td>
</tr>';

if($model->experiment->exercise!=null)foreach($model->experiment->exercise->exercise_problems as $exerciseProblem){ 
	$experiment.='
<tr >
	<td style="font-size: 13pt;  font-family: 锟斤拷锟斤拷;"><b>'. $exerciseProblem->sequence.CHtml::encode($exerciseProblem->title).'</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;">'. $exerciseProblem->problem->description.'</td>
</tr>';
}
$experiment.='
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;">'.$model->experiment->description.'</td>
</tr>
</table>
</div>';
?>
<?php $writeReport='
<div style="width:750px">
<table   width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 锟斤拷锟斤拷;"><b>锟侥★拷实锟斤拷锟斤拷锟�/b></td><td><b>锟矫分ｏ拷</b>'. ($model->score==0?'未锟斤拷':$model->score).'</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;" colspan=2>
		'. $model->report.'
	</td>
</tr>
</table>
<table style="height:31pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 锟斤拷锟斤拷;"><b>锟藉、实锟斤拷小锟斤拷</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;">锟斤拷锟斤拷要锟斤拷锟斤拷通锟斤拷实锟斤拷锟角凤拷锏斤拷锟斤拷锟街讹拷锟窖э拷锟斤拷诳锟斤拷锟斤拷锟斤拷薹锟斤拷玫锟斤拷锟街讹拷锟斤拷锟斤拷目锟侥ｏ拷锟斤拷锟斤拷锟斤拷锟斤拷些锟斤拷锟斤拷锟叫达拷锟截碉拷锟斤拷叩模锟斤拷约锟斤拷锟绞碉拷锟斤拷锟斤拷锟饺ｏ拷</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 锟斤拷锟斤拷;">
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
	<td><b>锟矫分ｏ拷</b>'. ($model->score==0?'未锟斤拷':$model->score).'</td>
</tr>
';
$experiment_remarks.='
<tr >
	<td><b>锟斤拷师锟斤拷锟斤：</b></td>
</tr>
<tr >
	<td style="font-size: 12pt; color:red; font-family: 锟斤拷锟斤拷;">'. ( ($model->comment && strlen($model->comment)>0)?$model->comment:'No remarks!'). '</td>
</tr>';
$experiment_remarks.='
<tr >
	<td style="font-size: 8pt;  font-family: 锟斤拷锟斤拷;">
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


	