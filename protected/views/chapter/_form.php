<!--
 Nested Set Admin GUI
 Form  View File  _form.php

This Form uses client validation.Check Yii Class Reference for rules supported by  client validation.
If your validation rule is not supported you may need to modify this file,possibly enabling
ajax validation -in this case you'll have to write the validation code in the controller.

 @author Spiros Kabasakalis <kabasakalis@gmail.com>,myspace.com/spiroskabasakalis
 @copyright Copyright &copy; 2011 Spiros Kabasakalis
 @since 1.0
 @license The MIT License-->
<div id="chapter_form_con"   class="client-val-form">
<?php if (isset($_POST['create_root']) && ($_POST['create_root']=='true') && $model->isNewRecord) : ?>              <h3 id="create_header">Create New Root Chapter </h3>
<?php elseif ($model->isNewRecord) : ?>     <h3 id="create_header">Create New Chapter </h3>
     <?php  elseif (!$model->isNewRecord):  ?>     <h3 id="update_header">Update Chapter <?php  echo $model->name;  ?> (ID:<?php   echo $model->id;  ?>) </h3>
    <?php   endif;  ?>
    <div   id="success-chapter" class="notification success png_bg" style="display:none;">
				<a href="#" class="close"><img src="<?php echo Yii::app()->request->baseUrl.'/css/images/icons/cross_grey_small.png';  ?>"
                                                                title="Close this notification" alt="close" /></a>
			</div>

<div  id="error-chapter" class="notification errorshow png_bg" style="display:none;">
				<a href="#" class="close"><img src="<?php echo Yii::app()->request->baseUrl.'/css/images/icons/cross_grey_small.png';  ?>"
                                                                title="Close this notification" alt="close" /></a>
			</div>

<div class="form">
<?php   $formId='chapter-form';
 $ajaxUrl=($model->isNewRecord)?
              ( ( !(isset($_POST['create_root']) && ($_POST['create_root']=='true'))  )?CController::createUrl('chapter/create'):CController::createUrl('chapter/createRoot')):
               CController::createUrl('chapter/update');
$val_error_msg='Error.Chapter was not saved.';
$val_success_message=($model->isNewRecord)?
( (!(isset($_POST['create_root']) && ($_POST['create_root']=='true')))?'Chapter was created successfuly.':'Root Chapter was created successfuly.'):
                                                  'Chapter was updated successfuly.';


$success='function(data){
    var response= jQuery.parseJSON (data);

    if (response.success ==true)
    {
         jQuery("#'.Chapter::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");
         $("#success-chapter")
        .fadeOut(1000, "linear",function(){
                                                             $(this)
                                                            .append("<div> '.$val_success_message.'</div>")
                                                            .fadeIn(2000, "linear")
                                                            }
                       );
        $("#chapter-form").slideToggle(1500);'.
        (isset($updatesuccess)?$updatesuccess:'').
    '}
         else {
                   $("#error-chapter")
                   .hide()
                    .show()
                    .css({"opacity": 1 })
                   .append("<div>"+response.message+"</div>").fadeIn(2000);

              jQuery("#'.Chapter::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");

                  }
                     }//function';

$js_afterValidate="js:function(form,data,hasError) {


        if (!hasError) {                         //if there is no error submit with  ajax
        jQuery.ajax({'type':'POST',
                              'url':'$ajaxUrl',
                         'cache':false,
                         'data':$(\"#$formId\").serialize(),
                         'success':$success
                           });
                         return false; //cancel submission with regular post request,ajax submission performed above.
    } //if has not error submit via ajax

else{
return false;       //if there is validation error don't send anything
    }                    //cancel submission with regular post request,validation has errors.

}";


$form=$this->beginWidget('CActiveForm', array(
     'id'=>'chapter-form',
   //'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
     'focus'=>array($model,'name'),
     'errorMessageCssClass' => 'input-notification-error  error-simple png_bg',
     'clientOptions'=>array('validateOnSubmit'=>true,
                                        'validateOnType'=>false,
                                        'afterValidate'=>$js_afterValidate,
                                        'errorCssClass' => 'err',
                                        'successCssClass' => 'suc',
                                        'afterValidateAttribute' => 'js:function(form, attribute, data, hasError){
                                                   if(!hasError){
                                                                    $("#success-"+attribute.id).fadeIn(500);
                                                                   $("label[for=\'Chapter_"+attribute.name+"\']").removeClass("error");
                                                                      }else {
                                                                                  $("label[for=\'Chapter_"+attribute.name+"\']").addClass("error");
                                                                                   $("#success-"+attribute.id).fadeOut(500);
                                                                                   }

                                                                                                                            }'
                                                                             ),
)); 

 ?>
<?php echo $form->errorSummary($model, '<div style="font-weight:bold">Please correct these errors:</div>', NULL, array('class' => 'errorsum notification errorshow png_bg')); ?><p class="note">Fields with <span class="required">*</span> are required.</p>

  

 <div class="row" >
  <?php echo $form->labelEx($model,'name'); ?>    <?php  echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128,'value'=>(isset($_POST['name'])?$_POST['name']:$model->name),'style'=>'width:88%;'));  ?>       <span  id="success-Chapter_name"  class="hid input-notification-success  success png_bg"></span>
    <div><small></small> </div>
     <?php   echo $form->error($model,'name');  ?>    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		The chapter uses an extended wiki format, please refer to <a href="http://www.simplewiki.org/" target="_blank">http://www.simplewiki.org/</a>
		<?php echo $form->textArea($model,'description',array('rows'=>10, 'cols'=>50)); ?>
             <span  id="success-Chapter_description"  class="hid input-notification-success  success png_bg"></span>
           <div><small></small> </div>
		<?php echo $form->error($model,'description'); ?>
	</div>

<? $this->widget('ext.EAjaxUpload.EAjaxUpload',
array(
        'id'=>'uploadFile',
        'config'=>array(
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
)); ?>

<input type="hidden" name= "YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken; ?>"  />
  <input type="hidden" name= "parent_id" value="<?php echo isset($_POST['parent_id'])?$_POST['parent_id']:''; ?>"  />

  <?php  if (!$model->isNewRecord): ?>    <input type="hidden" name= "update_id" value=" <?php echo $model->id; ?>"  />
     <?php endif; ?>      
    
   <div class="row buttons">
 <?php   echo  CHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save',array('class' => 'button align-right')); ?>	</div>
     
 <?php  $this->endWidget(); ?></div><!-- form -->

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
		$("#Chapter_description").insertAtCaret(\'\{\{Attachment:\'+responseJSON.fileid+\'|\'+fileName+\'}}\');
	else
		$("#Chapter_description").insertAtCaret(\'[[Attachment:\'+responseJSON.fileid+\'|\'+fileName+\']]\');
}
'
);
?>

<script  type="text/javascript">
    
 //Close button:

		$(".close").click(
			function () {
				$(this).parent().fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(600);
				});
				return false;
			}
		);


</script>


