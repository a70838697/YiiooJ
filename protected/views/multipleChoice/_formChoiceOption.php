<tr>
	<td>
		<?php echo $multiple_model->more_than_one_answer?
			$form->checkBox($model, "[$id]isAnswer", array(
					'value'=>1,
					'uncheckValue'=>0
			))
			:$form->radioButton($multiple_model, 'answer', array(
				    'value'=>"$id",
				    'uncheckValue'=>null
				));
		?>
	</td>
    <td>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,"[$id]description",array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
    </td>
    <td><?php echo CHtml::link(
        'delete', 
        '#', 
        array(
            'submit'=>'', 
            'params'=>array(
                'ChoiceOption[command]'=>'delete', 
                "ChoiceOption[id]"=>$id, 
                'noValidate'=>true)
            ));?>
    </td>
</tr>