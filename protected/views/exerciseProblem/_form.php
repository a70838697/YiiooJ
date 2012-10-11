<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'exercise-problem-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'problem_id'); ?>
		<?php echo $form->textField($model,'problem_id'); ?>
		<?php echo $form->error($model,'problem_id'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'sequence'); ?>
		<?php echo $form->textField($model,'sequence',array('size'=>60,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'sequence'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'memo'); ?>
		<?php echo $form->textField($model,'memo',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'memo'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<hr/>
<div id='selectproblem'></div>
<?php 
UCHtml::addYiiGridViewScriptsForAjax();
echo CHtml::script('
$(".select_id").live("click", 
function ()
{
	id=$(this).text();
	if(id!="")
	{
		$("#'.CHtml::activeId($model,'problem_id').'").val(id)
		$("#'.CHtml::activeId($model,'title').'").val($("#ap"+id).text());
	}
	return false;
}
);
function clearScripts()
{
	$("#problem-grid").remove();
	$("#selectproblem").remove();
	$("#'.CHtml::activeId($model,'problem_id').'").remove();
	 $("#'.CHtml::activeId($model,'title').'").remove();
}
	
$("#selectproblem").load("'.CHtml::normalizeUrl(array('courseProblem/select/public')) .'",{},function(){'.
"
jQuery('#problem-grid').yiiGridView({'ajaxUpdate':['1','problem-grid'],'ajaxVar':'ajax','pagerClass':'pager','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items','selectableRows':1,'enableHistory':false,'updateSelector':'{page}, {sort}','pageVar':'Problem_page'});
	".
'});
');
?>