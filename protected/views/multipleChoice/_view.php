<div class="view">

<div>
<?php
$answer_ids=preg_split('/,/',$data->answer);
?>
<?php echo $data->description; ?>
</div>
<table>
<?php foreach($data->choiceOptions as $choiceOption):
	$choiceOption->isAnswer=in_array($choiceOption->id,$answer_ids)?1:0;
	$this->renderPartial('_viewChoiceOption', array('id'=>$choiceOption->id, 'data'=>$choiceOption));?>
 
<?php endforeach;?>
</table>
<?php if($data->chapter){?>
<div>
Chapter:<?php echo $data->chapter->name; ?>
<?php echo CHtml::link("View chapter problems",array('/multipleChoice/list/'.$data->chapter_id));?> 
</div>
<?php }?>


</div>