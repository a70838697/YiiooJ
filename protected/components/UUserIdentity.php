<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UUserIdentity extends UserIdentity
{
	const GROUP_REGISTERED=0;
	const GROUP_USER=10;
	const GROUP_STUDENT=20;
	const GROUP_TEACHER=80;
	const GROUP_ADMINISTRATOR=99;
	public static $GROUP_MESSAGES=array(
		self::GROUP_REGISTERED=>'Registered',
		self::GROUP_USER=>'User',
		self::GROUP_STUDENT=>'Student',
		self::GROUP_TEACHER=>'Teacher',
		self::GROUP_ADMINISTRATOR=>'Administrator',
	);
	public static function isGuest()
	{
		return Yii::app()->user->isGuest;
	}
	public static function canHaveCourses()
	{
		return (!Yii::app()->user->isGuest) && (Yii::app()->user->group>=20);
	}
	public static function isCommonUser()
	{
		return Yii::app()->user->group==self::GROUP_USER;
	}
	public static function isStudent()
	{
		return Yii::app()->user->group==self::GROUP_STUDENT;
	}
	public static function isTeacher()
	{
		return Yii::app()->user->group==self::GROUP_TEACHER;
	}
	public static function isAdmin()
	{
		return Yii::app()->user->group==self::GROUP_ADMINISTRATOR;
	}
}