<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'organization-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'root'); ?>
		<?php echo $form->textField($model,'root',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'root'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'lft'); ?>
		<?php echo $form->textField($model,'lft',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'lft'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rgt'); ?>
		<?php echo $form->textField($model,'rgt',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'rgt'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'level'); ?>
		<?php echo $form->textField($model,'level'); ?>
		<?php echo $form->error($model,'level'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->