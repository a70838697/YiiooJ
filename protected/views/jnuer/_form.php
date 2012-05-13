<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'jnuer-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<p class="note"><font color=red><b>Please enter your real information, or you will be rejected.</b></font></p>

	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->errorSummary($model->profile); ?>
	<div class="row">
		<?php echo $form->labelEx($model->profile,'lastname'); ?>
		<?php echo $form->textField($model->profile,'lastname',array('size'=>40,'maxlength'=>50)); ?>
		<?php echo $form->error($model->profile,'lastname'); ?>
	</div>	
	<div class="row">
		<?php echo $form->labelEx($model->profile,'firstname'); ?>
		<?php echo $form->textField($model->profile,'firstname',array('size'=>40,'maxlength'=>50)); ?>
		<?php echo $form->error($model->profile,'firstname'); ?>
	</div>		
	<div class="row">
		<?php echo $form->labelEx($model,'identitynumber'); ?>
		<?php echo $form->textField($model,'identitynumber',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'identitynumber'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'first_year'); ?>
		<?php echo $form->textField($model,'first_year'); ?>
		<?php echo $form->error($model,'first_year'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'unit_id'); ?>
		<?php echo $form->dropDownList($model,'unit_id',$units,array('encode'=>false)); ?>
		<?php echo $form->error($model,'unit_id'); ?>
	</div>

<?php if(UUserIdentity::isAdmin()):?>
	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',Jnuer::$USER_STATUS_MESSAGES); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->profile,'group'); ?>
		<?php echo $form->dropDownList($model->profile,'group',UUserIdentity::$GROUP_MESSAGES); ?>
		<?php echo $form->error($model->profile,'group'); ?>
	</div>	
<?php endif?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->