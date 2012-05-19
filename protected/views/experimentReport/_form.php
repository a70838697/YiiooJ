<div style="width:750px">
<p style="text-align: center;" align="center"><b><span style="font-size: 22pt; font-family: 楷体_GB2312;">暨南大学本科实验报告专用纸</span></b></p>
<table  style="height:21pt;margin:0px" width="100%" >
<tr>
	<td style="font-size: 14pt; width:80px; font-family: 楷体_GB2312;">课程名称</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:500px;font-size: 14pt; font-family: 楷体_GB2312;">《<?php echo $model->experiment->course->title?>》</td>
	<td style="width:80px;font-size: 14pt; font-family: 楷体_GB2312;">成绩评定</td>
	<td style="font-size: 14pt;border-bottom: solid 2px black;"><?php echo $model->score==0?'&nbsp;':$model->score?></td></tr>
</table>
<table  style="height:21pt;margin:0px"" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: 楷体_GB2312;">实验项目名称</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;"><?php echo $model->experiment->title?></td>
	<td style="width:80px;font-size: 14pt; font-family: 楷体_GB2312;">指导教师</td>
	<td style="width:120px;font-size: 14pt;font-family: 楷体_GB2312;border-bottom: solid 2px black;text-align: center;"><?php echo $model->experiment->course->user->info->lastname.$model->experiment->course->user->info->firstname?></td></tr>
</table>
<table  style="height:21pt;margin:0px"" width="100%" >
<tr>
	<td style="font-size: 14pt; width:120px; font-family: 楷体_GB2312;">实验项目编号</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: 楷体_GB2312;"><?php echo $model->experiment->sequence;?></td>
	<td style="font-size: 14pt; width:120px; font-family: 楷体_GB2312;">实验项目类型</td>
	<td style="border-bottom: solid 2px black; text-align:center; width:120px;font-size: 14pt; font-family: 楷体_GB2312;"><?php echo UCourseLookup::$EXPERIMENT_TYPE_MESSAGES[$model->experiment->experiment_type_id];?></td>
	<td style="width:80px;font-size: 14pt; font-family: 楷体_GB2312;">实验地点</td>
	<td style="font-size: 14pt;font-family: 楷体_GB2312;border-bottom: solid 2px black;text-align: center;"><?php echo $model->experiment->course->location;?></td>
</tr>
</table>
<table style="height:21pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: 楷体_GB2312;">学生姓名</td>
	<td style="width:320px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;"><?php echo $model->user->info->lastname.$model->user->info->firstname ?> </td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">学号</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;"><?php echo $model->user->schoolInfo==null?"&nbsp;":$model->user->schoolInfo->identitynumber;?></td>
</tr>
</table>
<table style="height:21pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">学院</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">
	<?php 
	$xueyuan="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_SCHOOLE)$node=$node->getParent();
		if($node!=null)$xueyuan=$node->title;
	}
	echo $xueyuan;
	?>
	</td>
	<td style="font-size: 14pt; width:20px; font-family: 楷体_GB2312;">系</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">
	<?php 
	$xi="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_DEPARTMENT)$node=$node->getParent();
		if($node!=null)$xi=$node->title;
	}
	echo $xi;
	?>
	
	</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">专业</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">
	<?php 
	$xi="&nbsp;";
	if($model->user->schoolInfo!=null)
	{
		$node=$model->user->schoolInfo->unit;
		while($node!=null && $node->type_id!=Organization::ORGANIZATION_TYPE_MAJOR)$node=$node->getParent();
		if($node!=null)$xi=$node->title;
	}
	echo $xi;
	?>	
	</td>
</tr>
</table>
<table style="height:21pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt; width:80px; font-family: 楷体_GB2312;">实验时间</td>
	<td style="border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;"><?php echo date_format(date_create($model->experiment->due_time),'Y年m月d日  H:i'); ?></td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">温度</td>
	<td style="border-bottom: solid 2px black;width:40px; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">&nbsp;</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">℃</td>
	<td style="font-size: 14pt; width:40px; font-family: 楷体_GB2312;">湿度</td>
	<td style="width:60px;border-bottom: solid 2px black; text-align:center; font-size: 14pt; font-family: 楷体_GB2312;">&nbsp;</td>
</tr>
</table>
<table style="height:31pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>一、实验目的</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;"><div><?php echo $model->experiment->aim;?></div></td>
</tr>
</table>
<table style="height:31pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>二、实验环境</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;"><div><?php echo $model->experiment->course->environment;?></div></td>
</tr>
</table>
<table style="height:31pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>三、实验内容</b></td>
</tr>
<?php if($model->experiment->exercise!=null)foreach($model->experiment->exercise->exercise_problems as $exerciseProblem){ ?>
<tr >
	<td style="font-size: 13pt;  font-family: 宋体;"><b><?php echo $exerciseProblem->sequence.CHtml::encode($exerciseProblem->title);?></b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;"><?php echo $exerciseProblem->problem->description;?></td>
</tr>
<?php } ?>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;"><?php echo $model->experiment->description;?></td>
</tr>
</table>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'experiment-report-form',
	'enableAjaxValidation'=>false,
)); ?>

<table style="height:31pt;margin:0px""  width="100%">
<tr ><td>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	</td>
</tr>
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>四、实验分析*</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'report','rows'=>20,
		'config'=>array('upLinkUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upLinkExt'=>"zip,rar,txt,sql,ppt,pptx,doc,docx",'upImgUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upImgExt'=>"jpg,jpeg,gif,png",)),true); ?>
		<?php echo $form->error($model,'report'); ?>
	</td>
</tr>
</table>
<table style="height:31pt;margin:0px""  width="100%">
<tr >
	<td style="font-size: 14pt;  font-family: 宋体;"><b>五、实验小结*</b></td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">（主要表述通过实验是否达到巩固知识、学到在课堂上无法得到的知识补充的目的，并且在哪些方面有待重点提高的，自己对实验的体会等）</td>
</tr>
<tr >
	<td style="font-size: 12pt;  font-family: 宋体;">
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'conclusion','rows'=>6,
			'config'=>array('upLinkUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upLinkExt'=>"zip,rar,txt,sql,ppt,pptx,doc,docx",'upImgUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upImgExt'=>"jpg,jpeg,gif,png",)),true); ?>
		<?php echo $form->error($model,'conclusion'); ?>
	</td>
</tr>
</table>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>
<?php $this->endWidget(); ?>

</div>
