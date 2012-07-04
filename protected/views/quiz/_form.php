<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'quiz-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'class_room_id'); ?>
		<?php echo $form->dropDownList($model,'class_room_id',CHtml::listData(ClassRoom::model()->findAll(array('condition'=>'course_id=:cid','order' => 'begin DESC','params'=>array(':cid'=>$this->getCourseId(),))),
                    'id', 
                    'titleAndYear') ); ?>
		<?php echo $form->error($model,'class_room_id'); ?>
	</div>

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

	<div class="row">
		<?php echo $form->labelEx($model,'practice_id'); ?>
		<?php echo $form->dropDownList($model,'practice_id',CHtml::listData(Practice::model()->with('chapter.book.course')->findAll('course.id=:cid',array(':cid'=>$this->getCourseId())),
                    'id', 
                    'name') ); ?>
		<?php echo $form->error($model,'practice_id'); ?>
	</div>

	<!-- 

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'quiz_type'); ?>
		<?php echo $form->textField($model,'quiz_type'); ?>
		<?php echo $form->error($model,'quiz_type'); ?>
	</div>
	 -->
	
	<div class="row">
	<?php echo $form->labelEx($model,'begin');
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
	<?php echo $form->labelEx($model,'begin');
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

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->