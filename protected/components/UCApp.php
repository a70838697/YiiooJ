<?php
class UCApp
{
	/**
	 * get remote address
	 */
	public static function getClientAddress() {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if (isset($_SERVER["HTTP_CLIENT_IP"]))
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		else if (isset($_SERVER["REMOTE_ADDR"]))
			$ip = $_SERVER["REMOTE_ADDR"];
		else if (getenv("HTTP_X_FORWARDED_FOR"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("HTTP_CLIENT_IP"))
			$ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("REMOTE_ADDR"))
			$ip = getenv("REMOTE_ADDR");
		else
			$ip = "Unknown";
	  
		if (strpos($ip, ', ')>0) {
			$ips = explode(', ', $ip);
			$ip = $ips[0];
		}
		if($ip=='::1') $ip='127.0.0.1';
		return $ip;
	}
	public static function getIpAsInt() {
		$ip=ip2long(UCApp::getClientAddress());
		return is_int($ip)?$ip:0;
	}
}