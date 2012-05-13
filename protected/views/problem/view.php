<?php
$this->breadcrumbs=array(
	'Problems'=>array('index'),
	$model->title,
);

/*
$this->menu=array(
	array('label'=>'List Problem', 'url'=>array('index')),
	array('label'=>'Create Problem', 'url'=>array('create')),
	array('label'=>'Update Problem', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Problem', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),),
);
*/

$attrs=$model->attributeLabels();

$canUpdate=!Yii::app()->user->isGuest && $this->canAccess(array('model'=>$model),'update');
?>



<center><font size='6'><?php echo $model->id.'. '.CHtml::encode($model->title);?></font>
<?php echo ($model->submitedCount==0)?"0%(0/0)":"".round($model->acceptedCount*100.0/$model->submitedCount,1)."%(".$model->acceptedCount."/".$model->submitedCount.")";?>
<font color='red'><?php echo $model->time_limit.'ms,'.($model->memory_limit>>20).'M'?></font>
</center>
<?php if(count($model->tagLinks)>0){ ?>
		<b>Tags:</b>
		<?php echo implode(', ', $model->tagLinks); ?>
<?php } ?>
<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
    'items' => array(
/*
        array(
            'label'=>'Menu button 1',
            'icon-position'=>'left',
            'url'=>array('create') //urls like 'create', 'update' & 'delete' generates an icon beside the button
        ),
*/
        array(
            'label'=>'Submit',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
	        'visible'=>!Yii::app()->user->isGuest&& $buttons['submit'],
        	'linkOptions'=>array('onclick'=>'return showDialogue();',)
        ),
        array(
            'label'=>'Update',
            'icon-position'=>'left',
	        'visible'=>$canUpdate,
            'url'=>array('update', 'id'=>$model->id),
        ), 
        array(
            'label'=>'Test data',
            'icon-position'=>'left',
	        'visible'=>$canUpdate,
            'url'=>array('/test/problem', 'id'=>$model->id),
        ),         
        array(
            'label'=>'My submitions',
            'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'circle-plus',
        	'url'=>array('/'.$this->prefix.'submition/index/problem/'.$model->id.'/mine/1'),
        ),        
        array(
            'label'=>'Statistics',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/'.$this->prefix.'submition/index/problem/'.$model->id.''),
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
echo CHtml::script('
function showDialogue()
{
	$("#submitiondialog").dialog("open");
	//this.blur();
	return false;	
}
');
$tabs=array(
		$attrs['description']=>'<div>'.($model->description).'</div>',
);
if(strlen($model->input)>0) $tabs[$attrs['input']]='<div>'.($model->input).'</div>';
if(strlen($model->output)>0) $tabs[$attrs['output']]='<div>'.($model->output).'</div>';
if(strlen($model->input_sample)>0) $tabs[$attrs['input_sample']]='<pre style="border: 1px solid blue">'.($model->input_sample)."\r\n".'</pre>';
if(strlen($model->output_sample)>0) $tabs[$attrs['output_sample']]='<pre style="border: 1px solid blue">'.($model->output_sample)."\r\n".'</pre>';
if(strlen(trim($model->hint))>0) $tabs[$attrs['hint']]='<div>'.($model->hint).'</div>';

$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
    ),
));
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        array(
			'name'=>'user_id',
        	'label'=>'Recommend',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->user->username),
                                 array('user/user/view','id'=>$model->user_id)),
        ), 
        'source',
		'created',
	),
)); ?>

<?php 
if($buttons['submit']){
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'submitiondialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Submit a solution',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>500,
		'modal'=>true,
    ),
));
?>

<div id="submition">
	<?php if(Yii::app()->user->hasFlash('submitionSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('submitionSubmitted'); ?>
		</div>
	<?php else: ?>
		<?php $this->renderPartial('/submition/_form',array(
			'model'=>$submition,
			'problem'=>$model,
		)); ?>
	<?php endif; ?>

</div><!-- submition -->
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
}
?>

