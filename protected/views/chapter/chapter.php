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
	function resetEquationNumbers() {
        var AMS = MathJax.Extension["TeX/AMSmath"];
        AMS.startNumber = 0;
        AMS.labels = {};
     } 
        MathJax.Hub.Queue(
          resetEquationNumbers,
          ["PreProcess",MathJax.Hub],
          ["Reprocess",MathJax.Hub]
        ); 			
		MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
	');
	?>