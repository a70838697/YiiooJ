<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'course-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sequence'); ?>
		<?php echo $form->textField($model,'sequence',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'sequence'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'begin'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
		    'attribute'=>'begin',
		    'model'=>$model,
			'language'=>'cn',
			'value'=>date('Y-m-d'),
		    'options'=>array(
		        'showAnim'=>'fold',
				'dateFormat'=>('yy-mm-dd'),
			),
		    'htmlOptions'=>array(
		        'style'=>'height:20px;',
		    ),
		));
		?>		
		<?php echo $form->error($model,'begin'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
		    'attribute'=>'end',
		    'model'=>$model,
			'language'=>'cn',
			'value'=>date('Y-m-d'),
		    'options'=>array(
		        'showAnim'=>'fold',
				'dateFormat'=>('yy-mm-dd'),
			),
		    'htmlOptions'=>array(
		        'style'=>'height:20px;',
		    ),
		));
		?>			
		<?php echo $form->error($model,'end'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'location'); ?>
		<?php echo $form->textField($model,'location',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'location'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'due_time'); ?>
		<?php echo $form->textField($model,'due_time',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'due_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'visibility'); ?>
		<?php echo $form->dropDownList($model,'visibility',UCourseLookup::getCourseStatusMessages()); ?>		
		<?php echo $form->error($model,'visibility'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'environment'); ?>
		<?php echo $form->textField($model,'environment',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'environment'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'memo'); ?>
		<?php echo $form->textField($model,'memo',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'memo'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'description','rows'=>15),true); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->