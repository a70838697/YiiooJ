<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'practice-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',ULookup::$PRACTICE_STATUS_MESSAGES); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<?php if($chapters){?>
	<div class="row">
		<?php echo $form->labelEx($model,'chapter_id'); ?>
		<?php echo $form->dropDownList($model,'chapter_id',$chapters,array('encode'=>false)); ?>
		<?php echo $form->error($model,'chapter_id'); ?>
	</div>	
	<?php }?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'memo'); ?>
		<?php echo $form->textField($model,'memo',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'memo'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->