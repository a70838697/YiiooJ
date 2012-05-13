<?php

class UCompilerLookup
{
    const GNU_C=1;
    const GNU_C_PLUS_PLUS=2;
    const PASCAL=4;
    const JAVA=8;
    
	private static $_items=array(
		self::GNU_C=>array('text'=>'GNU C 4.5','ext'=>'c','display'=>'GCC'),
		self::GNU_C_PLUS_PLUS=>array('text'=>'GNU C++ 4.5','ext'=>'cpp','display'=>'G++'),
		self::PASCAL=>array('text'=>'Free Pascal 2.4','ext'=>'pas','display'=>'FPC'),
		self::JAVA=>array('text'=>'Java 1.6','ext'=>'java','display'=>'JAVA'),
	);


	/**
	 * Returns a validate compiler id bitset
	 * @return int
	 * @param int $compiler_id_bitset
	 */
	public static function validateCompilerSet($compiler_id_bitset)
	{
		$wholeset=0;
		foreach(self::$_items as $value=>$key)
		{
			$wholeset=($wholeset|$value);
		}
		return $wholeset&$compiler_id_bitset;
	}
	/**
	 * Returns the values for the specified type.
	 * @return array item values.
	 * An empty array is returned if the item type does not exist.
	 */
	public static function values($compiler_id_bitset=-1)
	{
		$result=array();
		foreach(self::$_items as $value=>$key)
		{
			if(($value & $compiler_id_bitset) ==$value)
			{
				$result[]=$value;
			}
		}
		return $result;
	}	
	/**
	 * Returns the items for the specified type.
	 * @return array item names indexed by item code. The items are order by their position values.
	 * An empty array is returned if the item type does not exist.
	 */
	public static function items($compiler_id_array)
	{
		$result=array();
		foreach($compiler_id_array as $value)
		{
			if(isset(self::$_items[$value]))
			{
				$result[$value]=self::$_items[$value]['text'];
			}
		}
		return $result;
	}

	/**
	 * Returns the item name for the specified type and code.
	 * @param integer the item code (corresponding to the 'code' column value)
	 * @return string the item name for the specified the code. False is returned if the item type or code does not exist.
	 */
	public static function item($code)
	{
		return isset(self::$_items[$code]) ? self::$_items[$code]['text'] : 'Unknown';
	}

	public static function ext($code)
	{
		return isset(self::$_items[$code]) ? self::$_items[$code]['ext'] : '';
	}
	public static function display($code)
	{
		return isset(self::$_items[$code]) ? self::$_items[$code]['display'] : '';
	}	
}