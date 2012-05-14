<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'OOJJ: Open Online Judge of JNU',

	// preloading 'log' component
	'preload'=>array('log'),

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
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths' => array(
				'ext.giix-core', // giix generators
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
			'returnUrl' => array('/user/profile'),               # page after login
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
	),

	// application components
	'components'=>array(
		'syntaxhighlighter' => array(
			'class' => 'ext.JMSyntaxHighlighter.JMSyntaxHighlighter',
		),
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