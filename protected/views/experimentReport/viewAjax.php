<?php

//skip id
for($loop=0;$loop<=30;$loop++)
{
	$w= new CWidget();
	$w->getId();
}

$cs=Yii::app()->clientScript;
echo UCHtml::cssFile('screen.css')."\r\n";
echo UCHtml::cssFile('main.css')."\r\n";
echo UCHtml::cssFile('form.css')."\r\n";

//echo CHtml::scriptFile($cs->getCoreScriptUrl().'/jquery.js')."\r\n";

$assets = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('comments') . '/assets');
echo CHtml::cssFile($assets . '/comments.css?'.time())."\r\n";
echo CHtml::scriptFile($assets . '/comments.js?'.time())."\r\n";


echo CHtml::cssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css')."\r\n";
echo CHtml::scriptFile($cs->getCoreScriptUrl().'/jui/js/jquery-ui.min.js')."\r\n";

$assets = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.JuiButtonSet') . '/assets');
echo CHtml::cssFile($assets .'/JuiButtonSet.css.php')."\r\n";
echo CHtml::scriptFile($assets .'/JuiButtonSet.js')."\r\n";

/*
*/
$canscore=UUserIdentity::isAdmin()||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->course->user_id);
$canedit=$canscore
	||(UUserIdentity::isStudent() && (
			($model->status==ExperimentReport::STATUS_ALLOW_EDIT ) 
			|| ( (!$model->experiment->isTimeOut()) &&  $model->status==ExperimentReport::STATUS_NORMAL)
		)
	);
if(UUserIdentity::isAdmin()||Yii::app()->user->id==$model->user_id||(UUserIdentity::isTeacher()&&Yii::app()->user->id==$model->experiment->course->user_id))
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
	'id'=>'xyb1',
    'items' => array(
        array(
            'label'=>Yii::t('course','Edit'),
            'icon-position'=>'left',
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
	        'linkOptions'=>array('target'=>'_blank;',),
        	'visible'=>$canedit,
        	'url'=>array('update', 'id'=>$model->id),
        ),
        array(
            'label'=>Yii::t('course','Score'),
            'icon-position'=>'left',
            'visible'=>$canscore && ( ($model->status==ExperimentReport::STATUS_SUBMITIED )|| ( ($model->status==ExperimentReport::STATUS_NORMAL) && ($model->experiment->isTimeOut()) )),
	        'linkOptions'=>array('onclick'=>'return showDialogue();',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('viewAjax', 'id'=>$model->id),
        ),
        array(
            'label'=>Yii::t('course','Submit'),
            'icon-position'=>'left',
            'visible'=>($canscore|| (Yii::app()->user->id==$model->user_id)) && ($model->status !=ExperimentReport::STATUS_SUBMITIED) ,
	        'linkOptions'=>array('onclick'=>'return submitr(this.href);',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('viewAjax', 'id'=>$model->id,'submited'=>'1'),
        ),
        array(
            'label'=>Yii::t('course','Extend deadline'),
            'icon-position'=>'left',
            'visible'=>($canscore) && ($model->status ==ExperimentReport::STATUS_SUBMITIED) ,
	        'linkOptions'=>array('onclick'=>'return extend(this.href);',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('viewAjax', 'id'=>$model->id,'extended'=>'1'),
        ),
    	array(
            'label'=>Yii::t('course','Print'),
            'icon-position'=>'left',
	        'linkOptions'=>array('target'=>'_blank;',),
            'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('report', 'id'=>$model->id),
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>
<?php /*$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'experiment_id',
		'user_id',
		'report',
		'conclusion',
		'created',
		'updated',
	),
));*/ ?>
	<?php if(Yii::app()->user->hasFlash('scoreSubmitted')){ ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('scoreSubmitted'); ?>
		</div>
	<?php } ?>

<?php $this->renderPartial('viewReport',array('model'=>$model));?>
<?php 
if($canscore){
echo CHtml::script('
$(document).ready(function() {
	$("#score-form").submit(function()
	{
		data={ "ExperimentReport[score]": $("#ExperimentReport_score").val(), "ExperimentReport[comment]": $("#ExperimentReport_comment").val() };
		$.post("'.CHtml::normalizeUrl(array('/experimentReport/viewAjax','id'=>$model->id)) .'", data,function(data) {
			$("#scoredialog").dialog("close");
			if(jQuery("#scoredialog"))jQuery("#scoredialog").dialog("destroy").remove();		
			if(jQuery("#comment1"))jQuery("#comment1").remove();
			if(jQuery("#ExperimentReport_comment"))jQuery("#ExperimentReport_comment").remove();
			if(jQuery("#tabReport"))jQuery("#tabReport").tabs("destroy").remove();
			$("#reportcontent").html(data);
		
			jQuery("#tabReport").tabs("select", 1);
			reloadGrid();
		});
		return false;
	});
}); 
        
function extend(url)
{
	if(confirm("Are you really want to let her/him resubmit?"))
		reloadReport(url);
	return false;
}		
function submitr(url)
{
	if(confirm("Are you really want to submit the report?\r\n You will not be allowed to modify it then."))
		reloadReport(url);
	return false;
}		
function showDialogue()
{
	$("#scoredialog").dialog("open");
	//this.blur();
	return false;	
}
');
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'scoredialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Give a score',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>360,
		'modal'=>true,
    ),
));
?>

<div id="submition">
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'score-form',
	'enableAjaxValidation'=>false,
));

 ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'score'); ?>
		<?php echo $form->textField ($model,'score'); ?>
		<?php echo $form->error($model,'score'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'comment'); ?>
<?php
$arr_attrs=array(
	    	'model'=>$model,
	    	'modelAttribute'=>'comment',
	    	'config'=>array(
	    		'tools'=>'full', // mini, simple, fill or from XHeditor::$_tools
	    		//see XHeditor::$_configurableAttributes for more
	    	),
			'htmlOptions'=>array(
				'rows'=>6,
	    		'style'=>isset($style)?$style:'',
		    	'cols'=>84,
			),    	
			
	   );
$config=array('upLinkUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upLinkExt'=>"zip,rar,txt,sql,ppt,pptx,doc,docx",'upImgUrl'=>UCHtml::url('upload/create/type/report/course/'.$model->experiment->course_id),'upImgExt'=>"jpg,jpeg,gif,png",);
if(isset($config))
{
	$arr_attrs['config']=array_merge($arr_attrs['config'],$config);
}
	   
	   if(isset($id))
{
	$arr_attrs['config']['id']=$id;
}	    
	    $this->widget('application.components.widgets.XHeditor',$arr_attrs);
?>		
		<?php echo $form->error($model,'comment'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

</div><!-- score -->
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
}


?>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
jQuery('#comment1').commentsList({'dialogTitle':'Add comment','deleteConfirmString':'Delete this comment?','approveConfirmString':'Approve this comment?','postButton':'Add comment','cancelButton':'Cancel'});
jQuery('#tabReport').tabs({'collapsible':true});
jQuery('#scoredialog').dialog({'dialogClass':'rbam-dialog','title':'Give a score','autoOpen':false,'minWidth':800,'height':360,'modal':true});
$("#ExperimentReport_comment").xheditor({'html5Upload':false,'tools':'full','upLinkUrl':'/YiiooJ1/upload/create/type/report/course/1','upLinkExt':'zip,rar,txt,sql,ppt,pptx,doc,docx','upImgUrl':'/YiiooJ1/upload/create/type/report/course/1','upImgExt':'jpg,jpeg,gif,png','id':'ExperimentReport_comment','name':'ExperimentReport[comment]'});
});
/*]]>*/
</script>
