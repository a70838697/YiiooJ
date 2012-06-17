<?php
?>

<h1> <?php echo $model->name; ?></h1>
	<div id="chapter_content">
	<?php
//	Yii::import('application.extensions.SimpleWiki.ImWiki');
	
//	$wiki=new ImWiki($model->description);
//	 echo $wiki->get_html(); 
	$parser=new CMarkdownParser;
	$parsedText = $parser->safeTransform($model->description);
	echo $parsedText;
?>
	</div>
	<?php
	echo CHtml::script('
		MathJax.Hub.Queue(["Typeset",MathJax.Hub,"chapter_content"]);
	');
	?>