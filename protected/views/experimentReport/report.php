<html><head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="<?php echo Yii::app()->baseUrl. '/js_plugins/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML';?>">
</script>
</head><body>
<?php if($model->experiment->classRoom->hasMathFormula)$this->widget('application.components.widgets.MathJax',array());
?><?php $this->renderPartial('_report',array('model'=>$model));?>
</body></html>