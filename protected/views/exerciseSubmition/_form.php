<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'submition-form',
	'enableAjaxValidation'=>false,
));

 ?>

	<p>Current Authenticated Author :<b><?php echo Yii::app()->user->name?></b></p>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->label($problem,'title'); ?>
		<?php echo CHtml::link(CHtml::encode($problem->title),
                                 array('exerciseProblem/view/0/problem/'.$model->problem_id.'/exercise/'.$model->exercise_id)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'compiler_id'); ?>
		<?php echo $form->dropDownList($model,'compiler_id',UCompilerLookup::items($problem->compiler_set)); ?>
		<?php echo $form->error($model,'compiler_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'source'); ?>
		<?php echo $form->textArea($model,'source',array('rows'=>10, 'cols'=>68)); ?>
		<?php echo $form->error($model,'source'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->