<?php
class UCourseLookup
{
	const COURSE_TYPE_PUBLIC=0;
	const COURSE_TYPE_PRIVATE=1;
	const COURSE_TYPE_DELETED=2;
	const COURSE_TYPE_ARCHIVED=3;
	
	public static $COURSE_TYPE_MESSAGES=array(
		self::COURSE_TYPE_PUBLIC=>'Public',
		self::COURSE_TYPE_PRIVATE=>'Private',
	);
	public  static function getCourseStatusMessages()
	{
		if(!UUserIdentity::isAdmin()){
			return self::$COURSE_TYPE_MESSAGES;
		}
		return array_merge(self::$COURSE_TYPE_MESSAGES,
		array(
		self::COURSE_TYPE_ARCHIVED=>'Archived',
		self::COURSE_TYPE_DELETED=>'Deleted',
		)
		);
	}
	
	const EXPERIMENT_TYPE_DESIGN=0;
	const EXPERIMENT_TYPE_VERIFICATION=1;
	const EXPERIMENT_TYPE_DEMONSTRATION=2;
	const EXPERIMENT_TYPE_COMPOSITED=3;
	public static $EXPERIMENT_TYPE_MESSAGES=array(
		self::EXPERIMENT_TYPE_DESIGN=>'设计型',
		self::EXPERIMENT_TYPE_VERIFICATION=>'验证型',
		self::EXPERIMENT_TYPE_DEMONSTRATION=>'演示型',
		self::EXPERIMENT_TYPE_COMPOSITED=>'综合型',
	);
	public static $EXPERIMENT_TYPE_MESSAGES_en=array(
		self::EXPERIMENT_TYPE_DESIGN=>'Design',
		self::EXPERIMENT_TYPE_VERIFICATION=>'Verification',
		self::EXPERIMENT_TYPE_DEMONSTRATION=>'Demonstration',
		self::EXPERIMENT_TYPE_COMPOSITED=>'Composited',
	);
	
}