<?php
$this->breadcrumbs=array(
	'Practices'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Practice', 'url'=>array('index')),
	array('label'=>'Create Practice', 'url'=>array('create')),
	array('label'=>'Update Practice', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Practice', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Practice', 'url'=>array('admin')),
);
$this->toolbar= array(
		array(
				'label'=>Yii::t('main','Update'),
				'icon-position'=>'left',
				'icon'=>'plus', // This a CSS class starting with ".ui-icon-"
				'visible'=>true,
				'url'=>array('update', 'id'=>$model->id),
		),
);
?>
<?php if($model->chapter){?>
<div>
Chapter:<?php echo $model->chapter->name; ?>
</div>
<?php }?>
<div>
<?php echo '<b>Memo:</b><br/>'; ?>
<?php echo $model->memo; ?>
</div>
<div>
<?php 
		//create an array open_nodes with the ids of the nodes that we want to be initially open
		//when the tree is loaded.Modify this to suit your needs.Here,we open all nodes on load.
		$categories= Examination::model()->findAll(array('condition'=>'root=:root_id','order'=>'lft','params'=>array(':root_id'=>$model->examination->id)));
		$identifiers=array();
		foreach($categories as $n=>$category)
		{
			$identifiers[]="'".'node_'.$category->id."'";
		}
		$open_nodes=implode(',', $identifiers);
	
		$baseUrl=Yii::app()->baseUrl;
	
		$dataProvider=new CActiveDataProvider('Examination');
		$this->renderPartial('/examination/index',array(
				'dataProvider'=>$dataProvider,
				'baseUrl'=> $baseUrl,
				'open_nodes'=> $open_nodes,
				'model'=>$model->examination,
				'test'=>$test,
		));
?>
		</div>