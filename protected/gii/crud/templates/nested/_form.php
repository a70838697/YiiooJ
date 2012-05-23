<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
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
<div id="<?php echo $this->class2id($this->modelClass); ?>_form_con"   class="client-val-form">
<?php echo '<?php '; ?>if ($_POST['create_root']=='true' && $model->isNewRecord) :<?php echo ' ?>'; ?>
              <h3 id="create_header">Create New Root <?php echo $this->modelClass; ?> </h3>
<?php echo '<?php '; ?>elseif ($model->isNewRecord) :<?php echo ' ?>'; ?>
     <h3 id="create_header">Create New <?php echo $this->modelClass; ?> </h3>
     <?php echo '<?php '; ?> elseif (!$model->isNewRecord): <?php echo ' ?>'; ?>
     <h3 id="update_header">Update <?php echo $this->modelClass; ?> <?php echo '<?php '; ?> echo $model->name; <?php echo ' ?>'; ?> (ID:<?php echo '<?php '; ?>  echo $model->id; <?php echo ' ?>'; ?>) </h3>
    <?php echo '<?php '; ?>  endif; <?php echo ' ?>'; ?>

    <div   id="success-<?php echo $this->class2id($this->modelClass); ?>" class="notification success png_bg" style="display:none;">
				<a href="#" class="close"><img src="<?php echo '<?php '; ?>echo Yii::app()->request->baseUrl.'/css/images/icons/cross_grey_small.png'; <?php echo ' ?>'; ?>"
                                                                title="Close this notification" alt="close" /></a>
			</div>

<div  id="error-<?php echo $this->class2id($this->modelClass); ?>" class="notification errorshow png_bg" style="display:none;">
				<a href="#" class="close"><img src="<?php echo '<?php '; ?>echo Yii::app()->request->baseUrl.'/css/images/icons/cross_grey_small.png'; <?php echo ' ?>'; ?>"
                                                                title="Close this notification" alt="close" /></a>
			</div>

<div class="form">

<?php echo '<?php  '; ?>
 $formId='<?php echo $this->class2id($this->modelClass); ?>-form';
 $ajaxUrl=($model->isNewRecord)?
              ( ($_POST['create_root']!='true')?CController::createUrl('<?php echo $this->class2id($this->modelClass); ?>/create'):CController::createUrl('<?php echo $this->class2id($this->modelClass); ?>/createRoot')):
               CController::createUrl('<?php echo $this->class2id($this->modelClass); ?>/update');
$val_error_msg='Error.<?php echo $this->modelClass; ?> was not saved.';
$val_success_message=($model->isNewRecord)?
( ($_POST['create_root']!='true')?'<?php echo $this->modelClass; ?> was created successfuly.':'Root <?php echo $this->modelClass; ?> was created successfuly.'):
                                                  '<?php echo $this->modelClass; ?> was updated successfuly.';


$success='function(data){
    var response= jQuery.parseJSON (data);

    if (response.success ==true)
    {
         jQuery("#'.<?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");
         $("#success-<?php echo $this->class2id($this->modelClass); ?>")
        .fadeOut(1000, "linear",function(){
                                                             $(this)
                                                            .append("<div> '.$val_success_message.'</div>")
                                                            .fadeIn(2000, "linear")
                                                            }
                       );
        $("#<?php echo $this->class2id($this->modelClass); ?>-form").slideToggle(1500);'.
        $updatesuccess.
    '}
         else {
                   $("#error-<?php echo $this->class2id($this->modelClass); ?>")
                   .hide()
                    .show()
                    .css({"opacity": 1 })
                   .append("<div>"+response.message+"</div>").fadeIn(2000);

              jQuery("#'.<?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");

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
     'id'=>'<?php echo $this->class2id($this->modelClass); ?>-form',
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
                                                                   $("label[for=\'<?php echo $this->modelClass; ?>_"+attribute.name+"\']").removeClass("error");
                                                                      }else {
                                                                                  $("label[for=\'<?php echo $this->modelClass; ?>_"+attribute.name+"\']").addClass("error");
                                                                                   $("#success-"+attribute.id).fadeOut(500);
                                                                                   }

                                                                                                                            }'
                                                                             ),
)); 

<?php echo ' ?>'; ?>

<?php echo '<?php '; ?>echo $form->errorSummary($model, '<div style="font-weight:bold">Please correct these errors:</div>', NULL, array('class' => 'errorsum notification errorshow png_bg'));<?php echo ' ?>'; ?>
<p class="note">Fields with <span class="required">*</span> are required.</p>

  

 <div class="row" >
  <?php echo '<?php '; ?>echo $form->labelEx($model,'name');<?php echo ' ?>'; ?>
    <?php echo '<?php '; ?> echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128,'value'=>$_POST['name'],'style'=>'width:75%;')); <?php echo ' ?>'; ?>
       <span  id="success-<?php echo $this->modelClass; ?>_name"  class="hid input-notification-success  success png_bg"></span>
    <div><small><?php //echo Yii::t('admin', 'Category Name'); ?></small> </div>
     <?php echo '<?php '; ?>  echo $form->error($model,'name'); <?php echo ' ?>'; ?>
    </div>

<?php
foreach($this->tableSchema->columns as $column)
{
	if($column->autoIncrement || in_array($column->name, array('lft','root','rgt','level','name')) )
		continue;
?>
	<div class="row">
		<?php echo "<?php echo ".$this->generateActiveLabel($this->modelClass,$column)."; ?>\n"; ?>
		<?php echo "<?php echo ".$this->generateActiveField($this->modelClass,$column)."; ?>\n"; ?>
             <span  id="success-<?php echo $this->modelClass; ?>_<?php echo $column->name; ?>"  class="hid input-notification-success  success png_bg"></span>
           <div><small><?php //echo Yii::t('admin', ''); ?></small> </div>
		<?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>\n"; ?>
	</div>

<?php
}
?>

<input type="hidden" name= "YII_CSRF_TOKEN" value="<?php echo '<?php '; ?>echo Yii::app()->request->csrfToken;<?php echo ' ?>'; ?>"  />
  <input type="hidden" name= "parent_id" value="<?php echo '<?php '; ?>echo $_POST['parent_id'];<?php echo ' ?>'; ?>"  />

  <?php echo '<?php '; ?> if (!$model->isNewRecord):<?php echo ' ?>'; ?>
    <input type="hidden" name= "update_id" value=" <?php echo '<?php '; ?>echo $_POST['update_id'];<?php echo ' ?>'; ?>"  />
     <?php echo '<?php '; ?>endif;<?php echo ' ?>'; ?>
      
    
   <div class="row buttons">
 <?php echo '<?php '; ?>  echo  CHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save',array('class' => 'button align-right'));<?php echo ' ?>'; ?>
	</div>
     
 <?php echo '<?php '; ?> $this->endWidget();<?php echo ' ?>'; ?>
</div><!-- form -->

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


