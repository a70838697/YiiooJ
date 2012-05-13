<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo  CHtml::encode($data->title); echo "  ".CHtml::link("Edit",array("update",'id'=>$data->title)); ?>
	<br />

	<div>
	<?php
	Yii::import('application.extensions.SimpleWiki.ImWiki');
	
	$wiki=new ImWiki($data->content);
	 echo $wiki->get_html(); ?>
	</div>

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('create_time')); ?>:</b>
	<?php echo CHtml::encode($data->create_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('update_time')); ?>:</b>
	<?php echo CHtml::encode($data->update_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('accessed_time')); ?>:</b>
	<?php echo CHtml::encode($data->accessed_time); ?>
	<br />

	*/ ?>

</div>