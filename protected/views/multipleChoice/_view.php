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
$fill=($data->question_type==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_MULTIPLE||
$data->question_type==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE
)?"":"Fill";
echo CHtml::link(Yii::t('main',"View"),array('/multipleChoice/view/'.$data->id,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()));
echo "|".CHtml::link(Yii::t('main',"Update"),array('/multipleChoice/update'.$fill.'/'.$data->id,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()));
?>
<?php if($data->chapter){?>
 Chapter:<?php echo $data->chapter->name; ?>(
<?php echo CHtml::link("View exercise items of the chapter",array('/multipleChoice/list/'.$data->chapter_id,"class_room_id"=>$this->getClassroomId(),"course_id"=>$this->getCourseId()));?> )
<?php }?>

</div>

</div>