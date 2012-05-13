<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'problem-judger-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.<br/><b>If there is no special judger, don't submit one. A special judger runs like 'judge input output submition_output'. It will return 0, if the submition_ouput is correct. Otherwise, the submition is wrong.</b></p>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->label($problem,'title'); ?>
		<?php echo CHtml::link(CHtml::encode($problem->title),
                                 array(''.$this->prefix.'problem/view','id'=>$model->id)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'compiler_id'); ?>
		<?php echo $form->dropDownList($model,'compiler_id',UCompilerLookup::items(UCompilerLookup::values(-1))); ?>
		<?php echo $form->error($model,'compiler_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'source'); ?>
		<?php echo $form->textArea($model,'source',array('rows'=>9, 'cols'=>90)); ?>
		<?php echo $form->error($model,'source'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->