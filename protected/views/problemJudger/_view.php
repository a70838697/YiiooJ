<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('problem_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->problem->title),
                                 array('problem/view','id'=>$data->problem_id)); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user->username); ?>
	<br />

                                 
	<b><?php echo CHtml::encode($data->getAttributeLabel('compiler_id')); ?>:</b>
	<?php echo CHtml::encode(UCompilerLookup::item($data->compiler_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('source')); ?>:</b>
	<br />
	<?php echo '<pre class="brush :'.UCompilerLookup::ext($data->compiler_id).'">'.CHtml::encode($data->source).'</pre>'; ?>
	<br />
<?php Yii::app()->syntaxhighlighter->addHighlighter(); ?>
</div>