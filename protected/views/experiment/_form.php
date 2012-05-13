<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'experiment-form',
	'enableAjaxValidation'=>false,
)); ?>

<table>
<tr><td colspan=4>
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>
</td></tr>
<tr>
<td colspan=4>
		<table><tr>
		<td>
		<?php echo $form->labelEx($model,'title'); ?>
		</td>
		<td width='150px'>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'title'); ?>
		</td><td><?php echo $form->labelEx($model,'experiment_type_id'); ?></td>
		<td><?php echo $form->dropDownList($model,'experiment_type_id',UCourseLookup::$EXPERIMENT_TYPE_MESSAGES); ?>		
		<?php echo $form->error($model,'experiment_type_id'); ?></td>
		</tr>
		</table>
</td></tr>
<tr><td>
		<?php echo $form->labelEx($model,'due_time'); ?>
		</td><td>
		<?php
		$this->widget('application.extensions.timepicker.EJuiDateTimePicker',array(
		    'model'=>$model,
		    'attribute'=>'due_time',
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
		<?php echo $form->error($model,'due_time'); ?>
</td><td>
		<?php echo $form->labelEx($model,'sequence'); ?>
		</td><td>
		<?php echo $form->textField($model,'sequence',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'sequence'); ?>
</td></tr>

<tr><td>
		<?php echo $form->labelEx($model,'begin'); ?>
		</td><td>
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
</td><td>

		<?php echo $form->labelEx($model,'end'); ?>
		</td><td>
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
</td></tr>
<tr><td>
		<?php echo $form->labelEx($model,'memo'); ?>
		</td><td colspan=3>
		<?php echo $form->textField($model,'memo',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'memo'); ?>
</td></tr>
<tr><td colspan=4>
	<div class="row">
		<?php echo $form->labelEx($model,'aim'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'aim','style'=>'width:740px;height:100px'),true); ?>
		<?php echo $form->error($model,'aim'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'description','style'=>'width:740px;height:120px','config'=>array('upImgUrl'=>UCHtml::url('upload/create/type/report/course/0'),'upImgExt'=>"jpg,jpeg,gif,png",)),true); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
</td></tr>
</table>
<?php $this->endWidget(); ?>
</div><!-- form -->