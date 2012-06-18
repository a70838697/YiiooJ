<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
//	'language'=>'zh_cn',
//	'sourceLanguage'=>'en_us',		
	'name'=>"Open Online Judge of Jinan University",

	// preloading 'log' component
	'preload'=>array('log',),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'ext.giix-components.*', // giix components
		'application.modules.user.models.*',
        'application.modules.user.components.*',

	),
	'modules'=>array(
        'user',
		'message' => array(
			'userModel' => 'UUser',
			'getNameMethod' => 'getFullName',
			'getSuggestMethod' => 'getSuggest',
			'viewPath' => '//messagesModuleCustom',				
		),			
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths' => array(
				//'bootstrap.gii', // since 0.9.1
				'ext.giix-core', // giix generators
				'application.gii',  //nested set  Model and Crud templates
			),
		),
		'user'=>array(
			'hash' => 'md5',                                     # encrypting method (php hash function)
			'sendActivationMail' => true,                        # send activation email
			'loginNotActiv' => false,                            # allow access for non-activated users
			'activeAfterRegister' => false,                      # activate user on registration (only sendActivationMail = false)
			'autoLogin' => true,                                 # automatically login from registration
			'registrationUrl' => array('/user/registration'),    # registration path
			'recoveryUrl' => array('/user/recovery'),            # recovery password path
			'loginUrl' => array('/user/login'),                  # login form path
			'returnUrl' => array('/site/index'),                 # page after login
			'returnLogoutUrl' => array('/user/login'),           # page after logout
		),			
		// RBAM Configuration
		'rbam'=>array(
			'initialise'=>true,
			'rbacManagerRole'=>'RBAC Manager',
		 	'authItemsManagerRole'=>'Auth Items Manager',
			'authAssignmentsManagerRole'=>'Auth Assignments Manager',
			'authenticatedRole'=>'Authenticated',
			'guestRole'=>'Guest',
			'pageSize'=>10,
			'relationshipsPageSize'=>5,
			'userClass'=>'User',
			'userIdAttribute'=>'id',
			'userNameAttribute'=>'username',
			'userCriteria'=>array(),
			'layout'=>'rbam.views.layouts.main',
			'applicationLayout'=>'application.views.layouts.main',
			'baseUrl'=>null,
			'baseScriptUrl'=>null,
			'cssFile'=>null,
			'showConfirmation'=>3000,
			'juiShow'=>'fade',
			'juiHide'=>'puff',
			'juiScriptUrl'=>null,
			'juiThemeUrl'=>null,
			'juiTheme'=>'base',
			'juiScriptFile'=>'jquery-ui.min.js',
			'juiCssFile'=>'jquery-ui.css',
			'initialise'=>null,
			'exclude'=>'rbam',
			'development'=>true,		
		),
			'comments'=>array(
					//you may override default config for all connecting models
					'defaultModelConfig' => array(
							//only registered users can post comments
							'registeredOnly' => false,
							'useCaptcha' => false,
							//allow comment tree
							'allowSubcommenting' => true,
							//display comments after moderation
							'premoderate' => false,
							//action for postig comment
							'postCommentAction' => 'comments/comment/postComment',
							//super user condition(display comment list in admin view and automoderate comments)
							'isSuperuser'=>'Yii::app()->user->checkAccess("moderate")',
							//order direction for comments
							'orderComments'=>'DESC',
					),
					//the models for commenting
					'commentableModels'=>array(
							//model with individual settings
							'Problem'=>array(
									'registeredOnly'=>true,
									'useCaptcha'=>false,
									'allowSubcommenting'=>true,
									//config for create link to view model page(page with comments)
									'pageUrl'=>array(
											'route'=>'problem/view',
											'data'=>array('id'=>'id'),
									),
							),
							//model with individual settings
							'Experiment'=>array(
									'registeredOnly'=>true,
									'useCaptcha'=>false,
									'allowSubcommenting'=>true,
									//config for create link to view model page(page with comments)
									'pageUrl'=>array(
											'route'=>'Experiment/view',
											'data'=>array('id'=>'id'),
									),
							),
							//model with individual settings
							'ExperimentReport'=>array(
									'registeredOnly'=>true,
									'useCaptcha'=>false,
									'allowSubcommenting'=>false,
									//config for create link to view model page(page with comments)
									'pageUrl'=>array(
											'route'=>'ExperimentReport/view',
											'data'=>array('id'=>'id'),
									),
							),							
							//model with default settings
							'ImpressionSet',
					),
					//config for user models, which is used in application
					'userConfig'=>array(
							'class'=>'UUser',
							'nameProperty'=>'username',
							'emailProperty'=>'email',
					),
			),			
	),

	// application components
	'components'=>array(
		'syntaxhighlighter' => array(
			'class' => 'ext.JMSyntaxHighlighter.JMSyntaxHighlighter',
		),
		/*'bootstrap'=>array(
			'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
		),*/			
		'user'=>array(
			// enable cookie-based authentication
            'class' => 'WebUser',
            'allowAutoLogin'=>true,
            'loginUrl' => array('/user/login'),
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false, 
			'rules'=>array(
				'entry/index'=>'entry/index',
				'entry/view/<id:.+>'=>'entry/view',
				'entry/update/<id:.+>'=>'entry/update',
				'entry/<id:.+>'=>'entry/view',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>/<id:\d+>/*'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),

		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		// uncomment the following to use a MySQL database
		*/
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=csp_yiiooj',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),
		'db2'=>array(
			'class'=> 'CDbConnection' ,
			'connectionString' => 'mysql:host=localhost;dbname=csp_yiiooj_data',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),			
        'authManager'=>array(
            'class'=>'CDbAuthManager',
       		'itemTable' => 'authitem',//table for auth
       		'itemChildTable' => 'authitemchild',
       		'assignmentTable' => 'authassignment',        		
            'connectionID'=>'db',
			'defaultRoles'=>array('Authenticated', 'Guest', 'Student', 'Teacher', 'Admin'),
        ),		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning,trace',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'worthful@qq.com',
	),
);