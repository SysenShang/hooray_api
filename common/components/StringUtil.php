<?php

namespace common\components;
/**
 * File for the class StringUtil.php
 * ==============================================
 * @copyright Copyright (c) 2013 – 2014 上海联沃通讯科技有限公司
 * ----------------------------------------------
 * 这不是一个自由软件，未经授权不许任何使用和传播。
 * ==============================================
 * @author zhuyongchao <zhuyongchao@linwotech.com>
 * @package
 * @subpackage
 * @version $Id:1.0.0$
 * @since 2013-11-4
 */
class StringUtil {
	
	/**
	 *
	 *
	 * 参数检查
	 *
	 * @access public
	 * @param unknown_type $str        	
	 * @param unknown_type $type
	 *        	请使用 SAFE_SQL，SAFE_POST,SAFE_NUMBER,SAFE_URL,SAFE_EMAIL,SAFE_IDENTITY,SAFE_KEYWORD,SAFE_NOHTML,SAFE_PHONE
	 * @return return_type
	 * @version 1.0.0 (2013-10-31)
	 * @author zhuyongchao
	 */
	public static function safeStr($str, $type) {
		if (strlen ( $str ) == 0)
			return '';
		if ($type == 'SAFE_SQL')
			return str_replace ( "'", "''", $str );
		if ($type == 'SAFE_TCP_PARAM') {
			$str = str_replace ( "'", "''", $str );
			return StringUtil::safestr ( $str, 'SAFE_NOHTML' );
		}
		if ($type == 'SAFE_POST') {
			$str = str_replace ( "'", "''", $str );
			return StringUtil::safestr ( $str, 'SAFE_NOHTML' );
		}
		if ($type == 'SAFE_NUMBER')
			return preg_replace ( '/[^\-+\.\d,;]/', '', $str );
		if ($type == 'SAFE_URL')
			return preg_replace ( '/[^\.\-\/_a-zA-Z\d#:\?&%\+=]/', '', $str );
		if ($type == 'SAFE_PATH')
			return preg_replace ( '/[^\.\-\/_a-zA-Z\d\(\)] /', '', trim ( $str ) );
		if ($type == 'SAFE_EMAIL')
			return preg_replace ( '/[^\.\-\/_a-zA-Z\d@]/', '', $str );
		if ($type == 'SAFE_IDENTITY')
			return preg_replace ( '/[^\-\._a-zA-Z\d]/', '', $str );
		if ($type == 'SAFE_DATETIME')
			return preg_replace ( '/[^\-:\.\/\d]/', '', $str );
		if ($type == 'SAFE_KEYWORD')
			return join ( ' ', preg_split ( '/[\s,;\'"\/\(\)]+/', trim ( $str ) ) );
		if ($type == 'SAFE_DIGIT')
			return preg_replace ( '/[^\d] /', '', trim ( $str ) );
		if ($type == 'SAFE_NOHTML') {
			$search = array (
					'@<script[^>]*?>.*?</script>@si',
					'@<style[^>]*?>.*?</style>@siU',
					'@<![\s\S]*?--[ \t\n\r]*>@' 
			);
			$str = preg_replace ( $search, '', $str );
			return htmlspecialchars ( strip_tags ( $str ), ENT_QUOTES );
		}
		if ($type == 'SAFE_PHONE') {
			$old = array (
					'（',
					'）',
					'－',
					'　' 
			);
			$new = array (
					'(',
					')',
					'-',
					' ' 
			);
			$str = str_replace ( $old, $new, $str );
			return preg_replace ( '/[^\-\d\(\) ]/', '', $str );
		}
		return '';
	}
	public static function getCurrentTime() {
		return date ( "Y-m-d H:i:s" );
	}
	public static function getCurrentTimestamp() {
		return time();		
	}
	public static function truncate($string, $length = 80, $etc = '...') {
		if ($length == 0)
			return '';
		
		if (function_exists ( "mb_strlen" )) {
			if (mb_strlen ( $string, "UTF-8" ) > $length) {
				$length -= min($length, mb_strlen($etc,"UTF-8"));
				return mb_substr($string, 0, $length, "UTF-8") . $etc;
				//return mb_substr ( $string, 0, $length / 2, "UTF-8" ) . $etc . mb_substr ( $string, - $length / 2, $length, "UTF-8" );
			}
			
			return $string;
		}
		
		if(isset($string[$length])){
			$length -= min($length,strlen($etc));
			return substr ( $string, 0, $length).$etc;
			//return substr ( $string, 0, $length / 2 ) . $etc . substr ( $string, - $length / 2 );
		}		
		return $string;
	}
	
	/**
	 * @author sunyong@linwotech.com
	 * @param unknown $arr_attr
	 * @param unknown $_allow_attr
	 * @return multitype:|$arr_attr
	 */
	public static function _filterAttr($arr_attr,$_allow_attr) {
		if(empty($arr_attr) || !is_array($arr_attr)) {
			return array();
		}
	
		$all_attr = $_allow_attr;
	
		//将非法字段过滤
		foreach($arr_attr as $key => $value) {
			if(!in_array($key,$all_attr)) {
				unset($arr_attr[$key]);
			}
		}
		return $arr_attr;
	}

    /**
     * 获取微秒的数据
     * @return int
     */
    public static function getMicroTime(){
        $time = explode (" ", microtime());
        return intval($time [0] * 1000000);
    }

    /**
     * 把秒数 格式化为 时：分 ：秒
     * @param $time
     */
    public static function formatDiffTime($time){
        $day = $hour = $min = $sec = 0;
        if($time > 86400){
            $day = floor($time / 86400);
        }
        $time = $time - $day * 86400;
        if($time > 3600){
            $hour = floor($time / 3600);
        }
        $time = $time - $hour * 3600;

        if($time > 60){
            $min = floor($time / 60);
        }
        $sec = $time - $min * 60;
        return sprintf("%02d:%02d:%02d", $hour, $min, $sec);
    }

    /**
     * 检查上传的文件是否存在
     * @param $file
     */
    public static function checkQNFile($file){
        $curl = curl_init($file) ;
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_ENCODING, "gzip" );
        curl_setopt ($curl, CURLOPT_HEADER, true);
        $file_contents = curl_exec($curl);
        $http_info = curl_getinfo($curl);
        curl_close($curl);
        if($http_info['http_code'] == '404'){
            return false;
        }
        return true;
    }

}

?>
