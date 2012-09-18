<?php
$this->breadcrumbs=array(
	Yii::t('course','My classes')=>array('/classRoom/index/mine/1'),
	Yii::t('course','Quizzes')=>array('/classRoom/quizzes','id'=>$model->classRoom->id),
	$model->name=>array('view','id'=>$model->id),
	Yii::t('course','Students')
);

$assets = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.components.widgets').'/xheditor');
echo CHtml::scriptFile($assets .'/xheditor-en.min.js')."\r\n";

$this->widget('application.components.widgets.MathJax',array());
?>
<?php
$columns=array(
				array(
						'header'=>Yii::t('classRoom','Student number'),
						'name'=>'schoolInfo.identitynumber',
						'type'=>'raw',
						'value'=>'$data->schoolInfo?CHtml::encode($data->schoolInfo->identitynumber):""',
				),
				array(
						'header'=>Yii::t('course','Name'),
						'name'=>'name',
						'type'=>'raw',
						'value'=>'$data->schoolInfo?CHtml::link(CHtml::encode($data->info->lastname.$data->info->firstname),array("schoolInfo/view","id"=>$data->schoolInfo->user_id)):""',
				),
				array(
						'header'=>Yii::t('course','Operation'),
						'type'=>'raw',
						'value'=>'$data->schoolInfo?CHtml::link(Yii::t("main","send a message"),array("message/compose","id"=>$data->schoolInfo->user_id)):""',
				),
		/*
				array(
						'header'=>'Login name',
						'name'=>'username',
						'type'=>'raw',
						'value'=>'CHtml::link(CHtml::encode($data->username),array("user/user/view","id"=>$data->id),  array("target"=>"_blank"))',
				),
				*/
		/*
				array(
						'header'=>'Score',
						'name'=>'experimentReport.score',
						'type'=>'raw',
						'value'=>'$data->experimentReport!=null && $data->experimentReport->score>0?$data->experimentReport->score:""',
				),
				array(
						'class'=>'CButtonColumn',
						'template' => '{view} ',
						'viewButtonUrl'=>'array("/experimentReport/view/".( ($data->experimentReport!=null)?$data->experimentReport->id:""))',
						'buttons' => array(
								'view'=>array(
										'visible'=>'($data->experimentReport!=null)',
										'options'=>array('target'=>'_blank'),
								)
						)
						 
				)
				*/
		);

foreach($model->practice->examination->examinations as $examination) 
{

	$isTimeOut=($model->afterDeadline())?'true':'false';
	$columns[]=	array(
						'header'=>$examination->sequence,
						'name'=>'id',
						'type'=>'raw',
						'value'=>'isset($this->grid->params[$data->id]['.$examination->id.'])?
		CHtml::link( 
			($this->grid->params[$data->id]['.$examination->id.']->review_time>0?$this->grid->params[$data->id]['.$examination->id.']->score:Yii::t("course","S")),
			array("examination/returnExamination","id"=>'.$examination->id.',"hisId"=>$data->id),  array("target"=>"_blank","onclick"=>"return showQuestion('.$examination->id.',$data->id);"))
		:""
		',
				);
	
}

$answer_array=array();
$quiz_answers=QuizAnswer::model()->findAll(array('condition'=>'quiz_id=:quiz_id','order'=>'user_id','params'=>array(':quiz_id'=>$model->id)));
foreach($quiz_answers as $n=>$quiz_answer)
{
	if(!isset($answer_array[$quiz_answer->user_id]))
		$answer_array[$quiz_answer->user_id]=array();
	$answer_array[$quiz_answer->user_id][$quiz_answer->examination_id]=$quiz_answer;
}
echo UCHtml::cssFile('pager.css');
$this->widget('SpecialGridView', array(
		'id'=>'groupUser-grid',
		'dataProvider'=>$dataProvider,
		'params' => $answer_array,
		'ajaxUpdate'=>true,
		'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>4,),
		'template'=>'{summary}{pager}{items}{pager}',
		'columns'=>$columns,
));

echo CHtml::script('
function reloadGrid()
{
		$.fn.yiiGridView.update(\'groupUser-grid\');
}
function showQuestion(examination_id,user_id)
{
	reloadReport("'.UCHtml::theUrl(array("examination/returnExamination/")).'"+"/id/"+examination_id+"/quiz/'.$model->id .'/hisId/"+user_id,"open");
	return false;	
}
function refreshcontent(){
	$("#viewreport").dialog("close");
	reloadGrid();
}
function reloadReport(url,dialog_status)
{
	if(jQuery("#scoredialog"))jQuery("#scoredialog").dialog("destroy").remove();
	if(jQuery("#comment1"))jQuery("#comment1").remove();
	//$("#ExperimentReport_comment").xheditor(false);
	if(jQuery("#ExperimentReport_comment"))jQuery("#ExperimentReport_comment").remove();
	if(jQuery("#tabReport"))jQuery("#tabReport").tabs("destroy").remove();
		
	$("#reportcontent").load(url,function(){
		if(dialog_status=="open")
			$("#viewreport").dialog("open");
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"tabReport"]);
	});
		
	return false;
}
function resubmitReport(link){
		if(confirm("'.Yii::t('course','Do you allow her/him to resubmit a report?').'") ) {
			jQuery.ajax({"success":function(data){ reloadGrid(); },"url":link,"cache":false});
			return false;
		} else return false;
}		
');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'viewreport',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'View Report',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>800,
		'modal'=>true,
    ),
));
echo '<div id="reportcontent"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

