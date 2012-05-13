<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'test-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'input'); ?>
		<?php echo $form->textArea($model,'input',array('rows'=>6, 'cols'=>80)); ?>
		<?php echo $form->error($model,'input'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'output'); ?>
		<?php echo $form->textArea($model,'output',array('rows'=>6, 'cols'=>80)); ?>
		<?php echo $form->error($model,'output'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'description',))	; ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->