<?php

class CourseProblemController extends ProblemController
{
	
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/course';
	public $contentMenu=null;
	public $actual_controller='problem';
	public $prefix="course";

}
