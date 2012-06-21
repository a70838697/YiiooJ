<?php
$trees=$model->descendants()->findAll();
array_unshift($trees,$model);
?>
<?php
if($test!==null)
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'multiple-choice-form',
		'enableAjaxValidation'=>false,
)); ?>
<?php
foreach ($trees as $node)
{
?>

<?php echo "<h$node->level>". $node->sequence."($node->score points)&nbsp;".$node->name."</h$node->level>"; ?>
<div id="chapter_content">
	<?php
	if($node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_FOLDER){
		$parser=new CMarkdownParser;
		$parsedText = $parser->safeTransform($node->description);
		echo $parsedText;
	}
	else if($node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_MULTIPLE
		||$node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE){
		$choiceOptionManager=new ChoiceOptionManager();
		$choiceOptionManager->load($node->multiple_choice_problem);
		$parser=new CMarkdownParser;
		$parsedText = $parser->safeTransform($node->description);
		echo $parsedText;
if($test===null)
{
		?>
<table>
<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
		<tr>
		<td width=10>
		<?php echo $choiceOption->isAnswer?UCHtml::image("accept.png"):"";?>
		</td>
		<td align="left">
			<?php echo CHtml::encode($choiceOption->description); ?>
		</td>
		</tr>
<?php endforeach;?>
</table>
	<?php
}
else
{
		?>
<table>
<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
		<tr>
		<td width=10>
	<?php echo $node->multiple_choice_problem->more_than_one_answer?
	$form->checkBox($node->multiple_choice_problem, "[$node->id][$id]answer", array(
			'value'=>1,
			'uncheckValue'=>0
	))
	:$form->radioButton($node->multiple_choice_problem, "[$node->id]answer", array(
			'value'=>"$id",
			'uncheckValue'=>null
	));
	?>
				</td>
		<td align="left">
			<?php echo CHtml::encode($choiceOption->description); ?>
		</td>
		</tr>
<?php endforeach;?>
</table>
	<?php
}
	}
	
}
	?>
</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php if($test!==null) $this->endWidget(); ?>
<?php
echo CHtml::script('
MathJax.Hub.Queue(
	["resetEquationNumbers",MathJax.InputJax.TeX],
	["PreProcess",MathJax.Hub],
	["Reprocess",MathJax.Hub]
);
	');
?>