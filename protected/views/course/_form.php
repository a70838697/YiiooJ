<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'course-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>60)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sequence'); ?>
		<?php echo $form->textField($model,'sequence',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'sequence'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'description','rows'=>15),true); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'memo'); ?>
		<?php echo $form->textField($model,'memo',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'memo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'visibility'); ?>
		<?php echo $form->dropDownList($model,'visibility',UCourseLookup::getCourseStatusMessages()); ?>		
		<?php echo $form->error($model,'visibility'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->