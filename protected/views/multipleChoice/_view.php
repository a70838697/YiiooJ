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
<div>
<?php
echo CHtml::link(Yii::t('main',"View"),array('/multipleChoice/view/'.$data->chapter_id));
echo "|".CHtml::link(Yii::t('main',"Update"),array('/multipleChoice/view/'.$data->chapter_id));
?>
<?php if($data->chapter){?>
 Chapter:<?php echo $data->chapter->name; ?>(
<?php echo CHtml::link("View multiple choice problems of the chapter",array('/multipleChoice/list/'.$data->chapter_id));?> )
<?php }?>

</div>

</div>