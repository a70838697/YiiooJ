<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'entry-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		The content uses an extended wiki format, please refer to <a href="http://www.simplewiki.org/" target="_blank">http://www.simplewiki.org/</a>
		<?php echo $form->textArea($model,'content',array('rows'=>12, 'cols'=>80)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php echo 
CHtml::script(
'
jQuery.fn.extend({
insertAtCaret: function(myValue){
  return this.each(function(i) {
    if (document.selection) {
      this.focus();
      sel = document.selection.createRange();
      sel.text = myValue;
      this.focus();
    }
    else if (this.selectionStart || this.selectionStart == \'0\') {
      var startPos = this.selectionStart;
      var endPos = this.selectionEnd;
      var scrollTop = this.scrollTop;
      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
      this.focus();
      this.selectionStart = startPos + myValue.length;
      this.selectionEnd = startPos + myValue.length;
      this.scrollTop = scrollTop;
    } else {
      this.value += myValue;
      this.focus();
    }
  })
}
});

function insertFile(fileName,responseJSON)
{
	if(responseJSON.ext=="jpg"||responseJSON.ext=="jpeg"||responseJSON.ext=="png"||responseJSON.ext=="gif")
		$("#Entry_content").insertAtCaret(\'\{\{Attachment:\'+responseJSON.fileid+\'|\'+fileName+\'}}\');
	else
		$("#Entry_content").insertAtCaret(\'[[Attachment:\'+responseJSON.fileid+\'|\'+fileName+\']]\');
}
'
);
?>
<?php $this->endWidget(); ?>
<? $this->widget('ext.EAjaxUpload.EAjaxUpload',
array(
        'id'=>'uploadFile',
        'config'=>array(
               'action'=>UCHtml::url('upload/create/type/wiki'),
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
)); ?>
</div><!-- form -->