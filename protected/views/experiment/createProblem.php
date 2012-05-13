<?php
$this->breadcrumbs=array(
	'Experiment'=>array('experiment/view/'.$experiment->id),
	'Create Problem',
);

?>

<h1>Create Problem</h1>

<?php echo $this->renderPartial('/problem/_form', array('model'=>$model)); ?>