<?php
$this->breadcrumbs=array(
	'Problems'=>array('index'),
	$problem->title=>array('/problem/'.$problem->id),
	'Test data'
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
            'label'=>'Add test',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('/test/create/'.$problem->id),
        ),
         array(
            'label'=>'Add test from files',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>array('/test/createByFile/'.$problem->id),
        ),       
        array(
            'label'=>'view problem',
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/'.$this->prefix.'problem/view/'.$problem->id.''),
        ),
        array(
            'label'=>($problemJudger->isNewRecord?'Add':'Update').' Special Judger',
            'icon-position'=>'left',
            'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
            'url'=>'#',
        	'linkOptions'=>array('onclick'=>'return showProblemJudgerDialogue();',)
        ),
        array(
            'label'=>'Delete Special Judger',
            'icon-position'=>'left',
            'icon'=>'circle-minus', // This a CSS class starting with ".ui-icon-"
            'url'=>$problem->id.'/deleteProblemJudger/1',
        	'visible'=>!($problemJudger->isNewRecord),
        	'linkOptions'=>array('onclick'=>'return showDeleteProblemJudger();',)
        ),
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
echo CHtml::script('

function showProblemJudgerDialogue()
{
	$("#specialJudgedialog").dialog("open");
	return false;	
}

');
$tabs=array(
	'Test data'=>$this->renderPartial('/test/_gridview',array(
				'dataProvider'=>$dataProvider,
			),true),
);

if(!($problemJudger->isNewRecord))
	$tabs['Special Judger']=$this->renderPartial('/problemJudger/_view',array(
				'data'=>$problemJudger,
			),true);
if(Yii::app()->user->hasFlash('problemJudgerSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('problemJudgerSubmitted'); ?>
		</div>
<?php
endif;
$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
    ),
));
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$problem,
	'attributes'=>array(
        array(
			'name'=>'user_id',
        	'label'=>'Recommend',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($problem->user->username),
                                 array('user/user/view','id'=>$problem->user_id)),
        ), 
        'source',
		'created',
	),
)); ?>


<?php 

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'specialJudgedialog',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'Special Judger',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>500,
		'modal'=>true,
    ),
));
?>

<div id="specialJudger">
	<?php $this->renderPartial('/problemJudger/_form',array(
			'model'=>$problemJudger,
			'problem'=>$problem,
		));?>
</div><!-- specialJudger -->
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
