<?php

?>
<h1>
	<?php echo $model->name; ?>
</h1>
<div id="chapter_content">
	<?php
	Yii::import('ext.ultraeditor.EditorSelector');
	echo EditorSelector::convert($model->content_type,$model->description);
	?>
</div>
<?php
if($model->book->course->hasMathFormula)
	echo CHtml::script('
	MathJax.Hub.Queue(
		["resetEquationNumbers",MathJax.InputJax.TeX],
		["PreProcess",MathJax.Hub],
		["Reprocess",MathJax.Hub]
	);
	');
?>