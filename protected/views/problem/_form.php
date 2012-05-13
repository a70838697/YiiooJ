<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'problem-form',
	'enableAjaxValidation'=>false,
)); ?>
<table>
<tr><td width='50%'>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
<table>
<tr><td>
	<div class="row">
		<?php echo $form->labelEx($model,'time_limit'); ?>
		<?php echo $form->textField($model,'time_limit'); ?>
		<?php echo $form->error($model,'time_limit'); ?>
	</div>
</td><td>

	<div class="row">
		<?php echo $form->labelEx($model,'memory_limit'); ?>
		<?php echo $form->dropDownList($model,'memory_limit',ULookup::$PROBLEM_MEMORY_LIMITS); ?>
		<?php echo $form->error($model,'memory_limit'); ?>
	</div>
</td><td>

	<div class="row">
		<?php echo $form->labelEx($model,'visibility'); ?>
		<?php echo $form->dropDownList($model,'visibility',ULookup::$PROBLEM_NORMAL_VISIBILITY_MESSAGES); ?>
		<?php echo $form->error($model,'visibility'); ?>
	</div>
</td></tr>
</table>
 	
	<div class="row">
		<?php echo $form->labelEx($model,'source'); ?>
		<?php echo $form->textField($model,'source',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'source'); ?>
	</div>	
</td>
<td width='50%'>

	<div class="row">
		<?php
		echo $form->labelEx($model,'compiler_set');
		echo '<table width="40px" style="overflow:hidden">';
		echo $form->checkboxList($model,'compiler_set',UCompilerLookup::items(UCompilerLookup::values(-1)),
			array('template'=>'<tr><td width="10px">{input}</td><td>{label}</td></tr>',
			'checkAll'=>'All languages',
			'separator'=>'')
		); 
		echo '</table>';
		?>
		<?php echo $form->error($model,'compiler_set'); ?>
	</div>

	<div class="row buttons">
		<?php
		$this->widget('zii.widgets.jui.CJuiButton', array(
				'name'=>'submit',
				'caption'=>$model->isNewRecord ? 'Create' : 'Save',
				'htmlOptions'=>array(
				'style'=>'width:300px'
		    ),
		));
		 //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

</td>
</tr>
<tr>
<td colspan=2>
<div class="row">
        <?php $this->widget('application.components.widgets.tag.TagWidget', array(
            'url'=> Yii::app()->request->baseUrl.'/tag/json/',
            'tags' => implode(',', $model->getTags())
        ));
        ?>
    </div>
</td>
</tr>
</table>

	<div class="row">
	<?php 
		$this->widget('zii.widgets.jui.CJuiTabs', array(
	    'tabs'=>array(
			$form->labelEx($model,'description')=>$this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'description',
				'config'=>array('upImgUrl'=>UCHtml::url('upload/create/type/problem'),'upImgExt'=>"jpg,jpeg,gif,png",)
			),true).$form->error($model,'description'),
			$form->labelEx($model,'input')=>$this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'input',
				'config'=>array('upImgUrl'=>UCHtml::url('upload/create/type/problem'),'upImgExt'=>"jpg,jpeg,gif,png",)
			),true).$form->error($model,'input'),
			$form->labelEx($model,'output')=>$this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'output',
				'config'=>array('upImgUrl'=>UCHtml::url('upload/create/type/problem'),'upImgExt'=>"jpg,jpeg,gif,png",)
			),true).$form->error($model,'output'),
			$form->labelEx($model,'input_sample')=>$form->textArea($model,'input_sample',array('rows'=>10, 'cols'=>100)).$form->error($model,'input_sample'),
			$form->labelEx($model,'output_sample')=>$form->textArea($model,'output_sample',array('rows'=>10, 'cols'=>100)).$form->error($model,'output_sample'),
			$form->labelEx($model,'hint')=>$this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'hint',
				'config'=>array('upImgUrl'=>UCHtml::url('upload/create/type/problem'),'upImgExt'=>"jpg,jpeg,gif,png",)
			),true).$form->error($model,'hint'),
			),
	    // additional javascript options for the tabs plugin
	    'options'=>array(
	        'collapsible'=>true,
	    ),
	));
	?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->