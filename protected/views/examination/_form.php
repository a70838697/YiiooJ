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
<div id="examination_form_con"   class="client-val-form">
<?php if ((isset($_POST['create_root'])?$_POST['create_root']:false)=='true' && $model->isNewRecord) : ?>              <h3 id="create_header">Create New Root Examination </h3>
<?php elseif ($model->isNewRecord) : ?>     <h3 id="create_header">Create New Examination </h3>
     <?php  elseif (!$model->isNewRecord):  ?>     <h3 id="update_header">Update Examination <?php  echo $model->name;  ?> (ID:<?php   echo $model->id;  ?>) </h3>
    <?php   endif;  ?>
    <div   id="success-examination" class="notification success png_bg" style="display:none;">
				<a href="#" class="close"><img src="<?php echo Yii::app()->request->baseUrl.'/css/images/icons/cross_grey_small.png';  ?>"
                                                                title="Close this notification" alt="close" /></a>
			</div>

<div  id="error-examination" class="notification errorshow png_bg" style="display:none;">
				<a href="#" class="close"><img src="<?php echo Yii::app()->request->baseUrl.'/css/images/icons/cross_grey_small.png';  ?>"
                                                                title="Close this notification" alt="close" /></a>
			</div>

<div class="form">

<?php   $formId='examination-form';
 $ajaxUrl=($model->isNewRecord)?
              ( ((isset($_POST['create_root'])?$_POST['create_root']:false)!='true')?CController::createUrl('examination/create'):CController::createUrl('examination/createRoot')):
               CController::createUrl('examination/update');
$val_error_msg='Error.Examination was not saved.';
$val_success_message=($model->isNewRecord)?
( ((isset($_POST['create_root'])?$_POST['create_root']:false)!='true')?'Examination was created successfuly.':'Root Examination was created successfuly.'):
                                                  'Examination was updated successfuly.';


$success='function(data){
    var response= jQuery.parseJSON (data);

    if (response.success ==true)
    {
         jQuery("#'.Examination::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");
         $("#success-examination")
        .fadeOut(1000, "linear",function(){
                                                             $(this)
                                                            .append("<div> '.$val_success_message.'</div>")
                                                            .fadeIn(2000, "linear")
                                                            }
                       );
        $("#examination-form").slideToggle(1500);'.
        (isset($updatesuccess)?$updatesuccess:"").
    '}
         else {
                   $("#error-examination")
                   .hide()
                    .show()
                    .css({"opacity": 1 })
                   .append("<div>"+response.message+"</div>").fadeIn(2000);

              jQuery("#'.Examination::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");

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
     'id'=>'examination-form',
  // 'enableAjaxValidation'=>true,
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
                                                                   $("label[for=\'Examination_"+attribute.name+"\']").removeClass("error");
                                                                      }else {
                                                                                  $("label[for=\'Examination_"+attribute.name+"\']").addClass("error");
                                                                                   $("#success-"+attribute.id).fadeOut(500);
                                                                                   }

                                                                                                                            }'
                                                                             ),
)); 

 ?>
<?php echo $form->errorSummary($model, '<div style="font-weight:bold">Please correct these errors:</div>', NULL, array('class' => 'errorsum notification errorshow png_bg')); ?><p class="note">Fields with <span class="required">*</span> are required.</p>

  

 <div class="row" >
  <?php echo $form->labelEx($model,'name'); ?>    <?php  echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128,'value'=>(isset($_POST['name'])?$_POST['name']:$model->name),'style'=>'width:75%;'));  ?>       <span  id="success-Examination_name"  class="hid input-notification-success  success png_bg"></span>
    <div><small></small> </div>
     <?php   echo $form->error($model,'name');  ?>    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
             <span  id="success-Examination_description"  class="hid input-notification-success  success png_bg"></span>
           <div><small></small> </div>
		<?php echo $form->error($model,'description'); ?>
	</div>


<input type="hidden" name= "YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken; ?>"  />
  <input type="hidden" name= "parent_id" value="<?php echo (isset($_POST['parent_id'])?$_POST['parent_id']:""); ?>"  />

  <?php  if (!$model->isNewRecord): ?>    <input type="hidden" name= "update_id" value=" <?php echo (isset($_POST['update_id'])?$_POST['update_id']:""); ?>"  />
     <?php endif; ?>      
    
   <div class="row buttons">
 <?php   echo  CHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save',array('class' => 'button align-right')); ?>	</div>
     
 <?php  $this->endWidget(); ?></div><!-- form -->

</div>
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


