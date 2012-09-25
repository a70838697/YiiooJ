	<?php 
	$xueyuan="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_SCHOOLE)$node=$node->getParent();
		if($node!=null)$xueyuan=$node->name;
	}
	$xi="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_DEPARTMENT)$node=$node->getParent();
		if($node!=null)$xi=$node->name;
	}
	$zhuanye="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_MAJOR)$node=$node->getParent();
		if($node!=null)$zhuanye=$node->name;
	}
	
echo  ($model->user->schoolInfo==null?"&nbsp;":$model->user->schoolInfo->identitynumber)."|". $model->user->info->lastname.$model->user->info->firstname.
	"|".date('Y-m-d H:i:s',$model->updated)."|Rank:" .$model->finishRank."/".$model->experiment->classRoom->studentGroup->userCount;
?>
<?php 
$gMessages=UClassRoomLookup::getEXPERIMENT_TYPE_MESSAGES();
$experimentInfomation='
<div style="width:750px">
<p style="text-align: center;" align="center"><b><span style="font-size: 22pt; font-family: 楷体_GB2312;">暨南大学本科实验报告专用纸</span></b></p>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:80px; font-family: 楷体_GB2312;">课程名称</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:500px;font-size: 14pt; font-family: 楷体_GB2312;">《'. $model->experiment->classRoom->title.'》</td>
	<td style="width:80px;font-size: 14pt; font-family: 楷体_GB2312;">成绩评定</td>
	<td style="font-size: 14pt;border-bottom: solid 2px black;">'. ($model->score==0?'&nbsp;':$model->score) .'</td></tr>
</table>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: 楷体_GB2312;">实验项目名称</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">'. $model->experiment->title.'</td>
	<td style="width:80px;font-size: 14pt; font-family: 楷体_GB2312;">指导教师</td>
	<td style="width:120px;font-size: 14pt;font-family: 楷体_GB2312;border-bottom: solid 2px black;text-align: center;">'.$model->experiment->classRoom->user->info->lastname.$model->experiment->classRoom->user->info->firstname.'</td></tr>
</table>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: 楷体_GB2312;">实验项目编号</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: 楷体_GB2312;">'. $model->experiment->sequence.'</td>
	<td style="font-size: 14pt; width:120px; font-family: 楷体_GB2312;">实验项目类型</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: 楷体_GB2312;">'. $gMessages[$model->experiment->experiment_type_id].'</td>
	<td style="width:80px;font-size: 14pt; font-family: 楷体_GB2312;">实验地点</td>
	<td style="font-size: 14pt;font-family: 楷体_GB2312;border-bottom: solid 2px black;text-align: center;">'. $model->experiment->classRoom->location.'</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: 楷体_GB2312;">学生姓名</td>
	<td style="width:320px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">'.$model->user->info->lastname.$model->user->info->firstname.'</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">学号</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">'. ($model->user->schoolInfo==null?"&nbsp;":$model->user->schoolInfo->identitynumber).'</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">学院</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">
	'.$xueyuan.'
	</td>
	<td style="font-size: 14pt; width:20px; font-family: 楷体_GB2312;">系</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">
	'.$xi.'
	
	</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">专业</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">
	'. $zhuanye .'
	</td>
</tr>
</table>
<table style="height:21pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: 楷体_GB2312;">实验时间</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">'. date_format(date_create($model->experiment->due_time),'Y年m月d日  H:i').'</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">温度</td>
	<td style="border-bottom: solid 2px black;width:40px; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">&nbsp;</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">℃</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">湿度</td>
	<td style="width:60px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">&nbsp;</td>
</tr>
</table>
<table style="height:31pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>一、实验目的</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;"><div>'.$model->experiment->aim.'</div></td>
</tr>
</table>
<table width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>二、实验环境</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;"><div>'. $model->experiment->classRoom->environment.'</div></td>
</tr>
</table>
</div>';
$experiment='<div>
<table width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>三、实验内容</b></td>
</tr>';

$submition_content="";
if($model->experiment->exercise!=null)foreach($model->experiment->exercise->exercise_problems as $exerciseProblem){ 
	$experiment.='
<tr >
	<td style="font-size: 13pt;  font-family: 宋体;"><b>'. $exerciseProblem->sequence.CHtml::encode($exerciseProblem->title).'</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">'. $exerciseProblem->problem->description.'</td>
</tr>';
	$submitions=$exerciseProblem->submitions(array('condition'=>'user_id='.$model->user_id));
	$submition_content.='
	<tr >
	<td style="font-size: 10pt;  font-family: 宋体;"><b>'. $exerciseProblem->sequence.".".CHtml::encode($exerciseProblem->title).'提交'. count($submitions) .'次</b></td>
	</tr>';
	if(count($submitions)>0){
	$submition_content.='<tr >'.
		'<table style="font-size: 10pt;  font-family: 宋体;"><tr><th>ID</th><th>状态</th><th>提交时间</th><th>修改次数</th><th>名次</th></tr>';
		$isfirstAccept=UUserIdentity::isTeacher() && UUserIdentity::Admin();
		foreach($submitions as $submition){
			$submition_content.="<tr><td>".CHtml::link($submition->id,array("exerciseSubmition/view","id"=>$submition->id), array('target'=>'_blank'))."</td>
			<td>".ULookup::$JUDGE_RESULT_MESSAGES[$submition->status]."</td>"
			."<td>".$submition->modified."</td>"
			."<td>".$submition->modification_times."</td>"
			."<td>".( ($isfirstAccept&&$submition->status== ULookup::JUDGE_RESULT_ACCEPTED)?($submition->acceptedRank+1):"") ."</td>"
			."</tr>";
			if(($isfirstAccept&& $submition->status== ULookup::JUDGE_RESULT_ACCEPTED))$isfirstAccept=false;
		}
		$submition_content.='</table></tr>';
	}
}
$experiment.='
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">'.$model->experiment->description.'</td>
</tr>
</table>
</div>';
?>
<?php $writeReport='
<div style="width:750px">
<table   width="100%">
'.$submition_content.'
</table>
<table   width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>四、实验分析*</b></td><td><b>得分：</b>'. ($model->score==0?'未评':$model->score).'</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;" colspan=2>
		'. $model->report.'
	</td>
</tr>
</table>
<table style="height:31pt;margin:0px"  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>五、实验小结*</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">（主要表述通过实验是否达到巩固知识、学到在课堂上无法得到的知识补充的目的，并且在哪些方面有待重点提高的，自己对实验的体会等）</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">
		'.$model->conclusion.' 
	</td>
</tr>
</table>
</div>';
?>
<?php
$this->beginClip('commentClip');
$this->widget('comments.widgets.ECommentsListWidget', array(
		'id'=>'comment1',
		'model' => $model,
));
$this->endClip();

$experiment_remarks='<div>
<table width="100%">
<tr >
	<td><b>得分：</b>'. ($model->score==0?'未评':$model->score).'</td>
</tr>
';
$experiment_remarks.='
<tr >
	<td><b>教师评语：</b></td>
</tr>
<tr >
	<td style="font-size: 12pt; color:red; font-family: 宋体;">'. ( ($model->comment && strlen($model->comment)>0)?$model->comment:'No remarks!'). '</td>
</tr>';
$experiment_remarks.='
<tr >
	<td style="font-size: 8pt;  font-family: 宋体;">
	<hr/>
		 '.$this->clips['commentClip'].'
	</td>
</tr>
</table>
</div>';

$this->widget('zii.widgets.jui.CJuiTabs', array(
	'id'=>'tabReport',
    'tabs'=>array("Report"=>$writeReport,
    		"Remarks"=>$experiment_remarks,
    		"Experiment Information"=>$experimentInfomation.$experiment,
    		),
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
    ),
));
?>


	