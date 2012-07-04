<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'multiple-choice-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<?php if($chapters){?>
	<div class="row">
		<?php echo $form->labelEx($model,'chapter_id'); ?>
		<?php echo $form->dropDownList($model,'chapter_id',$chapters,array('encode'=>false)); ?>
		<?php echo $form->error($model,'chapter_id'); ?>
	</div>	
	<?php }?>
<table>
<tr>
<th colspan=3><?php  echo $form->labelEx($model,'question_type');?><?php echo $form->error($model,'answer'); ?>
<?php echo $form->dropDownList($model,'question_type',isset($type)&&$type=='Fill'?ULookup::$EXAMINATION_PROBLEM_TYPE_COMMON_MESSAGES1:ULookup::$EXAMINATION_PROBLEM_TYPE_COMMON_MESSAGES2,array('encode'=>false)); ?></th>
</tr>
<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
 
<?php $this->renderPartial('_formChoiceOption', array('id'=>$id,'multiple_model'=>$model, 'model'=>$choiceOption, 'form'=>$form));?>
 
<?php endforeach;?>
<tr>
    <td></td>
	<td></td>
    <td><?php echo CHtml::link('add', '#', array('submit'=>'', 'params'=>array('ChoiceOption[command]'=>'add', 'noValidate'=>true)));?></td>
</tr>
</table>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
$('body').on('change','#MultipleChoice_question_type',function(){jQuery.yii.submitForm(this,'',{'OldValue':<?php echo $model->question_type;?>,'noValidate':true});return false;});
});
/*]]>*/
</script>
