<?php
/* @var $this ProgrammingContestController */
/* @var $model ProgrammingContest */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'programming-contest-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'begin'); ?>
		<?php
		$this->widget('application.extensions.timepicker.EJuiDateTimePicker',array(
		    'model'=>$model,
		    'attribute'=>'begin',
		    'options'=>array(
		        'hourGrid' => 6,
		        'hourMin' => 0,
		        'hourMax' => 23,
                'dateFormat'=>'yy-mm-dd',
                'timeFormat' => 'hh:mm:ss',
		        'changeMonth' => true,
		        'changeYear' => false,
		        ),
		    ));  
?>
		<?php echo $form->error($model,'begin'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end'); ?>
		<?php
		$this->widget('application.extensions.timepicker.EJuiDateTimePicker',array(
		    'model'=>$model,
		    'attribute'=>'end',
		    'options'=>array(
		        'hourGrid' => 6,
		        'hourMin' => 0,
		        'hourMax' => 23,
                'dateFormat'=>'yy-mm-dd',
                'timeFormat' => 'hh:mm:ss',
		        'changeMonth' => true,
		        'changeYear' => false,
		        ),
		    ));  
?>
	<?php echo $form->error($model,'end'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>1024)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'application_option'); ?>
		<?php echo $form->dropDownList($model,'application_option',ProgrammingContest::getApplicationOptionMessage()); ?>		
		<?php echo $form->error($model,'application_option'); ?>
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