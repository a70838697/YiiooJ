<?php
?>

<h1> <?php echo $model->name; ?></h1>
	<div>
	<?php
	Yii::import('application.extensions.SimpleWiki.ImWiki');
	
	$wiki=new ImWiki($model->description);
	 echo $wiki->get_html(); ?>
	</div>