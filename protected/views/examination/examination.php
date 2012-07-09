<?php
	$formId='examination-form';
	$ajaxUrl=CController::createUrl('examination/returnExamination/'. $model->id.(isset($quiz)&&$quiz!==null?("/quiz/".$quiz):"").(isset(Yii::app()->params['hisId'])&&Yii::app()->params['hisId']!==null?("/user_id/".Yii::app()->params['hisId']):""));
	$val_error_msg='Examination answers were not saved.';
	$val_success_message='Examination answers were saved successfuly.';


	$success='function(data){
			var response= jQuery.parseJSON (data);
			if (response.success ==true)
			{
				$("#error-examination").hide();
				$("#success-examination").fadeOut(1000, "linear",function(){
					$(this).append("<div> '.$val_success_message.'</div>").fadeIn(2000, "linear")
				});
			}
			else {
				$("#success-examination").hide();
				$("#error-examination").hide().show().css({"opacity": 1 }).append("<div>"+response.message+"</div>").fadeIn(2000);
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
	?>

	<?php
	if($quiz!==null){
	?>
<div class="form">
	<?php
		$form=$this->beginWidget('CActiveForm', array(
     		'id'=>'examination-form',
			//'enableAjaxValidation'=>false,
			'enableClientValidation'=>true,
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
		echo $form->error($model,'name'); 
		}
	?>
	<?php
	$node=$trees[0];
//	foreach ($trees as $node)
	{
		?>

	<?php echo "<h$node->level>". $node->sequence."($node->score points)&nbsp;".$node->name."</h$node->level>"; ?>
	<div id="chapter_content">
		<?php
		if($node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_FOLDER){
			$parser=new CMarkdownParser;
			$parsedText = $parser->safeTransform($node->description);
			echo $parsedText;
		}
		else if($node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_MULTIPLE
			||$node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE){
			$choiceOptionManager=new ChoiceOptionManager();
			$choiceOptionManager->load($node->multiple_choice_problem);
			$parser=new CMarkdownParser;
			$parsedText = $parser->safeTransform($node->description);
			echo $parsedText;
			if($quiz===null)
			{
				?>
		<table>
			<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
			<tr>
				<td width=10><?php echo $choiceOption->isAnswer?UCHtml::image("accept.png"):"";?>
				</td>
				<td align="left"><?php echo CHtml::encode($choiceOption->description); ?>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
		<?php
			}
			else
			{
				$answer_nodes=$quiz_answer_manager->getItems();
				?>
		<table>
			<?php foreach($choiceOptionManager->items as $id=>$choiceOption):?>
			<tr>
				<td width=10>
				<?php
				if(ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_SINGLE==$node->type_id)
					echo $form->radioButton($answer_nodes[$node->id], '[$node->id]answer', array(
						'value'=>"$id",
						'uncheckValue'=>null
					));
				elseif(ULookup::EXAMINATION_PROBLEM_TYPE_MULTIPLE_CHOICE_MULTIPLE==$node->type_id)
				echo $form->checkBox($model, "[$id]isAnswer", array(
					'value'=>1,
					'uncheckValue'=>0
				));
				?>
				</td>
				<td align="left"><?php echo CHtml::encode($choiceOption->description); ?>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
		<?php
			}
		}
		else if($node->type_id==ULookup::EXAMINATION_PROBLEM_TYPE_QUESTION)
		{
			$parser=new CMarkdownParser;
			$parsedText = $parser->safeTransform($node->description);
			echo $parsedText;
			if($savetype==2)
			{
				$answer_nodes=$quiz_answer_manager->getItems();	
				echo $form->textField($answer_nodes[$node->id], "[$node->id]score",array('maxSize'=>4));
			}
			$parsedText = $parser->safeTransform($node->multiple_choice_problem->description);
			echo $parsedText;
			if($quiz!==null)
			{
				$answer_nodes=$quiz_answer_manager->getItems();	
				echo $form->textArea($answer_nodes[$node->id], "[$node->id]answer",array('rows'=>20, 'cols'=>60));
			}
		}

	}
	?>
	</div>
	<?php 
	if($quiz!==null){
		?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class' => 'button align-right')); ?>
	</div>
	<div id="success-examination" class="notification success png_bg"
		style="display: none;"></div>

	<div id="error-examination" class="notification errorshow png_bg"
		style="display: none;"></div>

	<input type="hidden" name= "submit_id" value="1"  />	
	<?php 
	$this->endWidget();
	?>
	</div>
	<?php
	}
	?>
	<?php
	echo CHtml::script('
		MathJax.Hub.Queue(
		["resetEquationNumbers",MathJax.InputJax.TeX],
		["PreProcess",MathJax.Hub],
		["Reprocess",MathJax.Hub]
	);
		');
	?>