<?php 
/**
 * Zcontroller extendx CController and provide a new renderPartial method,
 * wich renders the clientScript immediatly after the content of the view
 */
class ZController extends Controller
{
	
	public function renderPartialWithHisOwnClientScript($view,$data=null, $return=false)
	{	
		
		$mainClientScript=Yii::app()->clientScript;
		Yii::app()->setComponent('clientScript', new ZClientScript);
		$output=$this->renderPartial($view, $data,  true);
		$output.=Yii::app()->clientScript->renderOnRequest();
		Yii::app()->setComponent('clientScript', $mainClientScript);

		if ($return)
			return $output;
		else
			echo $output;
	}

}


class ZClientScript extends CClientScript
{
	public function renderOnRequest()
	{
		$html='';
		foreach($this->scriptFiles as $scriptFiles)
		{
			foreach($scriptFiles as $scriptFile)
				$html.=CHtml::scriptFile($scriptFile)."\n";
		}
		foreach($this->scripts as $script)
			$html.=CHtml::script(implode("\n",$script))."\n";

		if($html!=='')
			return $html;
	}

}