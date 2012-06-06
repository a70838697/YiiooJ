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
	<th><?php echo $form->checkBox($model,'more_than_one_answer', array('value'=>1, 'uncheckValue'=>0)); ?></th><th><?php  echo $form->labelEx($model,'more_than_one_answer');?><?php echo $form->error($model,'answer'); ?></th>
    <th></th>
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
$('body').on('click','#MultipleChoice_more_than_one_answer',function(){jQuery.yii.submitForm(this,'',{'OldValue':<?php echo $model->more_than_one_answer;?>,'noValidate':true});return false;});
});
/*]]>*/
</script>
