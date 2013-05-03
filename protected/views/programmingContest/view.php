<?php
/* @var $this ProgrammingContestController */
/* @var $model ProgrammingContest */

$this->breadcrumbs=array(
	'Programming Contests'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List ProgrammingContest', 'url'=>array('index')),
	array('label'=>'Create ProgrammingContest', 'url'=>array('create')),
	array('label'=>'Update ProgrammingContest', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ProgrammingContest', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ProgrammingContest', 'url'=>array('admin')),
);
$canUpdate=!Yii::app()->user->isGuest && UUserIdentity::isTeacher();

?>

<h1>View ProgrammingContest #<?php echo $model->name; ?></h1>
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
						'label'=>'Update',
						'icon-position'=>'left',
						'visible'=>$canUpdate,
						'url'=>array('update', 'id'=>$model->id),
				),
				array(
						'label'=>'Students',
						'icon-position'=>'left',
						'visible'=>$canUpdate,
						'url'=>array('students', 'id'=>$model->id),
				),
			array(
					'label'=>Yii::t('t','Add a programming problem'),
					'icon-position'=>'left',
					'icon'=>'circle-plus', // This a CSS class starting with ".ui-icon-"
					'url'=>array('exerciseProblem/addProblemToProgrammingContest','id'=>$model->id),
					'visible'=>UUserIdentity::isTeacher()||UUserIdentity::isAdmin(),
					'linkOptions'=>array('class'=>'create')
			),
			/*			
				array(
						'label'=>'Rank',
						'icon-position'=>'left',
						'visible'=>!Yii::app()->user->isGuest,
						'icon'=>'circle-plus',
						'url'=>array('/'.$this->prefix.'submition/index/problem/'.$model->id.'/mine/1'),
				),
				*/
		),
		'htmlOptions' => array('style' => 'clear: both;'),
));
$APPPLICATION_MSG=ClassRoom::getApplicationOptionMessage();
 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        //'name',
		array(
			'name'=>'user_id',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->userinfo->lastname.$model->userinfo->firstname),
                                 array('user/user/view','id'=>$model->user_id)),
        ),
		array(
			'label'=>'Begin ~ End',
            'type'=>'raw',
            'value'=>$model->begin . ' ~ ' .$model->end,
        ),
		array(
			'name'=>'description',
            'type'=>'raw',
            'value'=>'<div>'.$model->description.'</div>',
        ),
	),
));
 
 if($model->exercise!==null && !$model->isTimeOut()){
// 	echo "<h3>".Yii::t('t',"Programming problems")."</h3>";
 	$criteria = new CDbCriteria;
 	//$criteria->select ("sequence","problem.title");
 	$criteria->compare('exercise_id',$model->exercise_id);
 	$criteria->order='sequence ASC';
 	$scopes=array('titled');
 	if((!Yii::app()->user->isGuest) && Yii::app()->request->getQuery('mine',null)!==null)
 		$scopes[]='mine';
 	else $scopes[]='public';
 	 	
 	$dataProvider=new EActiveDataProvider('ExerciseProblem',array(
 		'scopes'=>$scopes,
 			'criteria' => $criteria));
 	$arraycolums=array();
 	if(UUserIdentity::isTeacher()||UUserIdentity::isAdmin())
 	{
 		$arraycolums[]=array(
 				'class'=>'CButtonColumn',
 				'template'=> '{view}{update}{delete}',
 				'viewButtonUrl' => 'array("exerciseProblem/view",
 				"id"=>$data->id)',
 				'buttons'=>array(
 						'update' =>array('url'=>'Yii::app()->createUrl("exerciseProblem/update",array("id"=>$data->id))',
 								'options'=>array('class'=>'update'),
 						),
 				),
 				'deleteButtonUrl' => 'array("exerciseProblem/delete",
 				"id"=>$data->id)',
 		);
 
 	}
 	/*
 	$arraycolums[]=array(
 			'name'=>'solved',
 			'visible'=>!Yii::app()->user->isGuest,
 			'type'=>'raw',
 			'value'=>'$data->mySubmitedCount==0?"":(UCHtml::image($data->myAcceptedCount>0?"done.gif":"tried.gif").
 			($data->myAcceptedCount==0?"0":CHtml::link($data->myAcceptedCount,array("'.$this->prefix.'submition/index/mine/1/problem/".$data->id)))
 			."/".CHtml::link($data->mySubmitedCount,array("'.$this->prefix.'submition/index/mine/1/problem/".$data->id))
 	)',
 	);
 	*/
 	
 	$arraycolums[]=array(
 			'name' => 'sequence',
 			'header' => Yii::t('t','Sequence'),
 			'type' => 'raw',
 			'value' => 'CHtml::encode($data["sequence"])'
 	);
 	$arraycolums[]=array(
 			'name' => 'title',
 			'header' => Yii::t('t','Problem title'),
 			'type' => 'raw',
 			'value' => ' CHtml::link(nl2br(CHtml::encode($data->title)),$data->getUrl(null))',
 	);
 	$arraycolums[]=array(
 			'name' => 'memo',
 			'header' => Yii::t('t','Memo'),
 	);
 	$this->widget('zii.widgets.grid.CGridView', array(
 			//here might be a bug
 			'afterAjaxUpdate'=>'js:function(id,data){$("a.update").formDialog({"onSuccess":function(data, e){alert(data.message);window.location.reload();},"close":function(){if($.clearScripts)$.clearScripts();$(this).detach()},"title":"'.Yii::t("t","Update a programming problem").'","minWidth":800,"height":710,"modal":true,"id":"yw1"});}',
 			'dataProvider' => $dataProvider,
 			'columns' => $arraycolums,
 	));
 }
 
 /*$this->widget('comments.widgets.ECommentsListWidget', array(
 		'model' => $model,
 ));
 */
 
 $this->widget('application.extensions.formDialog.FormDialog', array('link'=>'a.create',
 		'options'=>array('onSuccess'=>'js:function(data, e){alert(data.message);window.location.reload();}',
 				'dialogClass'=>'rbam-dialog',
 				'close'=>'js:function(){if($.clearScripts)$.clearScripts();$(this).detach()}',
 				'title'=>Yii::t('t', 'Add a programming problem'),
 				'minWidth'=>800,
 				'height'=>710,
 				'modal'=>true,
 		)
 ));
 
 $this->widget('application.extensions.formDialog.FormDialog', array('link'=>'a.update',
 		'options'=>array('onSuccess'=>'js:function(data, e){alert(data.message);window.location.reload();}',
 				'dialogClass'=>'rbam-dialog',
 				'close'=>'js:function(){if($.clearScripts)$.clearScripts();$(this).detach()}',
 				'title'=>Yii::t('t', 'Update a programming problem'),
 				'minWidth'=>800,
 				'height'=>710,
 				'modal'=>true,
 		)
 ));
 $this->widget('ext.EAjaxUpload.EAjaxUploadBasic',
 		array(
 				'id'=>'uploadFile',
 				'config'=>array(
 						'button'=>'js:jQuery("#fileUploader")[0]',
 						'action'=>UCHtml::url('upload/create/type/chapter'.(isset($model->root)?('/book/'.(int)($model->root)):'')),
 						'allowedExtensions'=>array("jpg","jpeg","png","gif","txt","rar","zip","ppt","chm","pdf","doc","7z"),//array("jpg","jpeg","gif","exe","mov" and etc...
 						'sizeLimit'=>10*1024*1024,// maximum file size in bytes
 						'minSizeLimit'=>10,// minimum file size in bytes
 						'onComplete'=>'js:function(id, fileName, responseJSON){ if (typeof(responseJSON.success)!="undefined" && responseJSON.success){insertFile(fileName,responseJSON);}}',
 						//'messages'=>array(
 						//                  'typeError'=>"{file} has invalid extension. Only {extensions} are allowed.",
 						//                  'sizeError'=>"{file} is too large, maximum file size is {sizeLimit}.",
 						//                  'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
 						//                  'emptyError'=>"{file} is empty, please select files again without it.",
 						//                  'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
 						//                 ),
 						//'showMessage'=>"js:function(message){ alert(message); }"
 				)
 		));
// if($model->classRoom->hasMathFormula)$this->widget('application.components.widgets.MathJax',array());