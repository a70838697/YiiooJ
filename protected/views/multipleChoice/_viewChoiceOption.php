<tr>
<td width=10>
<?php echo $data->isAnswer?UCHtml::image("accept.png"):""?>
</td>
<td align="left">
	<?php echo CHtml::encode($data->description); ?>
</td>
</tr>

<?php /*echo CHtml::link(
        'delete', 
        '#', 
        array(
            'submit'=>'', 
            'params'=>array(
                'ChoiceOption[command]'=>'delete', 
                'ChoiceOption[id]'=>$data->id, 
                'noValidate'=>true)
            ));*/?>