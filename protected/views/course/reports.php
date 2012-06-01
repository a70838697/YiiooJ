<?php
$this->breadcrumbs=array(
	'My Courses'=>array('/course/index/mine/1'),
	$model->title=>array('view','id'=>$model->id),
	'Reports'
);

$assets = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.components.widgets').'/xheditor');
echo CHtml::scriptFile($assets .'/xheditor-en.min.js')."\r\n";


/*
$this->menu=array(
	array('label'=>'List Course', 'url'=>array('index')),
	array('label'=>'Create Course', 'url'=>array('create')),
	array('label'=>'Update Course', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Course', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Course', 'url'=>array('admin')),
);
*/
?>
<h1>Experiment Reports for <?php echo CHtml::encode($model->title); ?></h1>

<?php
$this->widget('ext.JuiButtonSet.JuiButtonSet', array(
	'id'=>'xyz1',
    'items' => array(
        array(
            'label'=>Yii::t('course','View experiments'),
            'icon-position'=>'left',
        	'visible'=>!Yii::app()->user->isGuest,
            'icon'=>'document',
        	'url'=>array('/course/experiments/'.$model->id),
        ),
        array(
            'label'=>Yii::t('course','View students'),
            'icon-position'=>'left',
        	'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'icon'=>'document',
        	'url'=>array('/course/students/'.$model->id),
        ),
    	array(
            'label'=>Yii::t('course','Course information'),
            'icon-position'=>'left',
            'icon'=>'document',
        	'url'=>array('/course/view/'.$model->id.''),
        ),
        array(
            'label'=>Yii::t('course','Update course'),
            'icon-position'=>'left',
	        'visible'=>(UUserIdentity::isTeacher()&& $model->user_id==Yii::app()->user->id) ||UUserIdentity::isAdmin(),
            'url'=>array('update', 'id'=>$model->id),
        ),               
    ),
    'htmlOptions' => array('style' => 'clear: both;'),
));
?>
<?php
$columns=array(
				array(
						'header'=>Yii::t('course','Student number'),
						'name'=>'schoolInfo.identitynumber',
						'type'=>'raw',
						'value'=>'CHtml::encode($data->schoolInfo->identitynumber)',
				),
				array(
						'header'=>Yii::t('course','Name'),
						'name'=>'name',
						'type'=>'raw',
						'value'=>'CHtml::encode($data->info->lastname.$data->info->firstname)',
				),
		/*
				array(
						'header'=>'Login name',
						'name'=>'username',
						'type'=>'raw',
						'value'=>'CHtml::link(CHtml::encode($data->username),array("user/user/view","id"=>$data->id),  array("target"=>"_blank"))',
				),
				*/
		/*
				array(
						'header'=>'Score',
						'name'=>'experimentReport.score',
						'type'=>'raw',
						'value'=>'$data->experimentReport!=null && $data->experimentReport->score>0?$data->experimentReport->score:""',
				),
				array(
						'class'=>'CButtonColumn',
						'template' => '{view} ',
						'viewButtonUrl'=>'array("/experimentReport/view/".( ($data->experimentReport!=null)?$data->experimentReport->id:""))',
						'buttons' => array(
								'view'=>array(
										'visible'=>'($data->experimentReport!=null)',
										'options'=>array('target'=>'_blank'),
								)
						)
						 
				)
				*/
		);

foreach($model->experiments as $experiment) 
{

	$isTimeOut=($experiment->afterDeadline())?'true':'false';
	$columns[]=	array(
						'header'=>$experiment->sequence,
						'name'=>'score',
						'type'=>'raw',
						'value'=>'$data->getCourseExperimentColumn('.$model->id.','.$experiment->id.','.$isTimeOut.')',
				);
	
}
if(count($model->experiments)>0)
{
	$columns[]=	array(
			'header'=>Yii::t('course',"Average/Times"),
			'type'=>'raw',
			'value'=>'$data->getAverageScore('.$model->id.')',
	);
	
}

echo UCHtml::cssFile('pager.css');
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'groupUser-grid',
		'dataProvider'=>$dataProvider,
		'ajaxUpdate'=>true,
		'pager'=>array('class'=>'CLinkPager','maxButtonCount'=>4,),
		'template'=>'{summary}{pager}{items}{pager}',
		'columns'=>$columns,
));

echo CHtml::script('
function reloadGrid()
{
		$.fn.yiiGridView.update(\'groupUser-grid\');
}
function showReport(id)
{
	reloadReport("'.UCHtml::theUrl(array("experimentReport/viewAjax/")).'"+"/"+id,"open");
	return false;	
}
function reloadReport(url,dialog_status)
{
	if(jQuery("#scoredialog"))jQuery("#scoredialog").dialog("destroy").remove();
	if(jQuery("#comment1"))jQuery("#comment1").remove();
	if(jQuery("#ExperimentReport_comment"))jQuery("#ExperimentReport_comment").remove();
	if(jQuery("#tabReport"))jQuery("#tabReport").tabs("destroy").remove();
		
	$("#reportcontent").load(url,function(){
		if(dialog_status=="open")
			$("#viewreport").dialog("open");
	});
	return false;
}
function resubmitReport(link){
		if(confirm("'.Yii::t('course','Do you allow her/him to resubmit a report?').'") ) {
			jQuery.ajax({"success":function(data){ reloadGrid(); },"url":link,"cache":false});
			return false;
		} else return false;
}		
');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'viewreport',
    'options'=>array(
		'dialogClass'=>'rbam-dialog',
        'title'=>'View Report',
        'autoOpen'=>false,
		'minWidth'=>800,
		'height'=>800,
		'modal'=>true,
    ),
));
echo '<div id="reportcontent"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

