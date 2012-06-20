<?php
$trees=$model->descendants()->findAll();
array_unshift($trees,$model);
foreach ($trees as $node)
{
?>

<?php echo "<h$node->level>". $node->sequence."&nbsp;".$node->name."</h$node->level>"; ?>
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
	
}
	?>
</div>
<?php
echo CHtml::script('
MathJax.Hub.Queue(
	["resetEquationNumbers",MathJax.InputJax.TeX],
	["PreProcess",MathJax.Hub],
	["Reprocess",MathJax.Hub]
);
	');
?>