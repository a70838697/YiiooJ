<?php
class UClassRoomLookup
{
	const CLASS_ROOM_TYPE_PUBLIC=0;
	const CLASS_ROOM_TYPE_PRIVATE=1;
	const CLASS_ROOM_TYPE_DELETED=2;
	const CLASS_ROOM_TYPE_ARCHIVED=3;
	
	public static $CLASS_ROOM_TYPE_MESSAGES=array(
		self::CLASS_ROOM_TYPE_PUBLIC=>'Public',
		self::CLASS_ROOM_TYPE_PRIVATE=>'Private',
	);
	public  static function getClassRoomStatusMessages()
	{
		if(!UUserIdentity::isAdmin()){
			return self::$CLASS_ROOM_TYPE_MESSAGES;
		}
		return array_merge(self::$CLASS_ROOM_TYPE_MESSAGES,
			array(
				self::CLASS_ROOM_TYPE_ARCHIVED=>'Archived',
				self::CLASS_ROOM_TYPE_DELETED=>'Deleted',
			)
		);
	}
	
	const EXPERIMENT_TYPE_DESIGN=0;
	const EXPERIMENT_TYPE_VERIFICATION=1;
	const EXPERIMENT_TYPE_DEMONSTRATION=2;
	const EXPERIMENT_TYPE_COMPOSITED=3;
	public static function getEXPERIMENT_TYPE_MESSAGES()
	{
		return array(
			self::EXPERIMENT_TYPE_DESIGN=>Yii::t('experiment','Design'),
			self::EXPERIMENT_TYPE_VERIFICATION=>Yii::t('experiment','Verification'),
			self::EXPERIMENT_TYPE_DEMONSTRATION=>Yii::t('experiment','Demonstration'),
			self::EXPERIMENT_TYPE_COMPOSITED=>Yii::t('experiment','Composited'),
		);
	}
	
}