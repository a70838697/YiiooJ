<?php
$this->breadcrumbs=array(
	'Problems'=>array('index'),
	$problem->title=>array('/problem/'.$problem->id),
	'Test data'=>array('/test/problem/'.$problem->id),
	'Create by file'
);
?>
<center><font size='6'><?php echo $problem->id.'. '.CHtml::encode($problem->title);?></font>
<?php echo ($problem->submitedCount==0)?"0%(0/0)":"".round($problem->acceptedCount*100.0/$problem->submitedCount,1)."%(".$problem->acceptedCount."/".$problem->submitedCount.")";?>
<font color='red'><?php echo $problem->time_limit.'ms,'.($problem->memory_limit>>20).'M'?></font>
</center>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
        array(
            'label'=>'view tests',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/test/problem/'.$problem->id),
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>

<h1>Create Test</h1>

<div class="form">

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'test-form',
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'enableAjaxValidation'=>false,
));
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'input_file'); ?>
		<?php echo $form->fileField($model,'input_file',array('size'=>100)); ?>
		<?php echo $form->error($model,'input_file'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'output_file'); ?>
		<?php echo $form->fileField($model,'output_file',array('size'=>100)); ?>
		<?php echo $form->error($model,'output_file'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $this->renderPartial('/inc/_xheditor',array('model'=>$model,'field'=>'description',))	; ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->