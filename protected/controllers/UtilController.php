<?php

class UtilController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow',
						'users'=>array('admin'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	private function processImg($field,$record)
	{
		$change=false;
		//$img=GrabImage("http://tech.ddvip.com/_1978837_detector_ap100.jpg","");
		if(preg_match_all("/src[\s]*=[\s]*\"?([^\">\s]*)/",$field,$out, PREG_PATTERN_ORDER)){
			foreach($out[1] as $m){
				$pattern = '/^\/joj/';
				if(preg_match($pattern,$m))
				{
					//echo  $record->id.",".$type.",".$m.'<br/>';
					continue;
				}
				$url=$m;
				if(preg_match('/^Image\//',$m))
				{
					$url='http://acm.zjgsu.edu.cn/JudgeOnline/'.$m;
				}
				if(preg_match('/^(http:|https:)/i',$url))
				{
					$filename = ltrim(strrchr($url,"/"),"/");
					while (file_exists( 'upload/'.$filename)) {
						$filename = rand(10, 99).$filename;
					}
					if(!$this->GrabImage($url,'upload/'.$filename))
					{
						echo $record->id.",".$url.' failed<br/>';
					}
					else 
					{
						$change=str_replace($m,'/joj/images/problem/static/'.$filename,$field);
					}
				}
				else
				{
					echo  $record->id.",".$m.'<br/>';
				}
			}
		}
		return $change;		
	}
	public function actionDownloadImg()
	{
		set_time_limit(0);

		$records = Problem::model()->findAll();
		foreach ($records as $record) {
			if($change=$this->processImg($record->description,$record))
			{
				$record->description=$change;
				$record->save();
			}
			if($change=$this->processImg($record->hint,$record))
			{
				$record->hint=$change;
				$record->save();
			}
			if($change=$this->processImg($record->input,$record))
			{
				$record->input=$change;
				$record->save();
			}
			if($change=$this->processImg($record->output,$record))
			{
				$record->output=$change;
				$record->save();
			}
			if($change=$this->processImg($record->input_sample,$record))
			{
				$record->input_sample=$change;
				$record->save();
			}
			if($change=$this->processImg($record->output_sample,$record))
			{
				$record->output_sample=$change;
				$record->save();
			}
				
		}
		Yii::app()->end();
	}
	private function GrabImage($url,$filename="") {
		if($url==""):return false;endif;
		if($filename=="") {
			$ext=strrchr($url,".");
			//if($ext!=".gif" && $ext!=".jpg"):return false;endif;
			$filename=date("dMYHis").$ext;
			while (file_exists( $filename)) {
				$filename = rand(10, 99).$filename;
			}			
		}
		ob_start();
		$read_result=@readfile($url);
		$img = ob_get_contents();
		ob_end_clean();
		if($read_result)
		{
			$size = strlen($img);
			$fp2=@fopen($filename, "a");
			@fwrite($fp2,$img);
			@fclose($fp2);
			return $filename;
		}
		return false;
	}
	public function actionCheckUploadFile()
	{
		set_time_limit(0);

		$records = Upload::model()->findAll();
		foreach ($records as $record) {
			$filepath=$record->location;
			if(!(file_exists($filepath) && is_file($filepath)))
			{
				$record->delete();
				echo $record->id . ' does not exist!';
				continue;
			}
				
			// do the processing here
			$pathinfo = pathinfo($filepath);
			$filename = md5(uniqid());
			if(!isset($pathinfo['extension']))
			{
				echo($record->id.'<br/>');
				continue;
			}
			$ext = $pathinfo['extension'];
			/// don't overwrite previous files that were uploaded
			while (file_exists( $pathinfo['dirname'].'/' . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
				
			/*
			 $record->location= $pathinfo['dirname'].'/' . $filename . '.' . $ext;
			if($record->save())
			{
			@rename($filepath, $pathinfo['dirname'].'/' . $filename . '.' . $ext);
			}
			//echo $record->location."=".	$filename;
			//echo '<br/>';
			*/
		}
		Yii::app()->end();
	}


}
