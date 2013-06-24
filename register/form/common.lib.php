<?php

define("MYSQL_DATABASE_PREFIX","festember_");
/**
 * @package pragyan
 * @file common.lib.php
 * @brief Contains functions which are common to many tasks and very frequently used.
 * @author Abhishek <i.abhi27[at]gmail.com>.
 * @copyright (c) 2010 Pragyan Team.
 * @license http://www.gnu.org/licenses/ GNU Public License.
 * For more details, see README
 */

/** Security Functions Begin, by Abhishek (For Usage, read Security Guidelines)**/

/** To escape the database queries for avoiding SQL injection attacks */
require_once("smarttable.class.php");
function escape($query)
{
	if (!get_magic_quotes_gpc()) {
	    $xquery = mysql_real_escape_string($query);
	    /// If there's no mysql connection, then the xquery will be false
	    if($xquery===false)
	    {
	     connect();
	     return escape($query);
	    }
	    else return $xquery;
	}
	return $query;
}

/** Another escape, which is not so secure as previous one, but it should only be used in install script.
    Reason being this doesn't require to establish a mysql connection unlike previous one. Can be hacked if 
    multibyte character set is used (e.g. GBK), highly unlikely though coz I trust the admin! -Abhishek
*/
function nomysql_escape($query)
{
	if (!get_magic_quotes_gpc()) {
		$query=addslashes($query);
	}
	return $query;
}

/** To protect against writing dangerous URLs, Returns true if it detects a risk, More improvement to be done */

function URLSecurityCheck($getvars)
{
	foreach($getvars as $var=>$val)
	{
		if(preg_match("/[<>]/",$var) || preg_match("/[<>]/",$val)) 
			return true;
	}
	return false;
}

/** To prevent XSS attacks  */

function safe_html($html)
{
	return htmlspecialchars(strip_tags($html));
}

/** Disabling magic quotes gpc on runtime incase .htaccess is disabled and its ON in php.ini */

function disable_magic_quotes()
{
	if (get_magic_quotes_gpc()) {
	    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	    while (list($key, $val) = each($process)) {
		foreach ($val as $k => $v) {
		    unset($process[$key][$k]);
		    if (is_array($v)) {
		        $process[$key][stripslashes($k)] = $v;
		        $process[] = &$process[$key][stripslashes($k)];
		    } else {
		        $process[$key][stripslashes($k)] = stripslashes($v);
		    }
		}
	    }
	    unset($process);
	}
}

/** Unregistering globals in case .htaccess is disabled and its ON in php.ini */
function unregister_globals() {
    if (ini_get('register_globals')) {
        $array = array('_POST', '_GET', '_COOKIE', '_REQUEST', '_SESSION', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if (isset($GLOBALS[$key]) && $var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

/** Security Functions Ends **/

/** Load Templates into the database */

/** To Check if the email provider is not blacklisted */
function check_email($mail)
{
	$domain = substr(strstr($mail,'@'),1);
	$ip = gethostbyname($domain);
	$query = "SELECT * FROM `".MYSQL_DATABASE_PREFIX."blacklist` WHERE `domain` = '$domain' OR `ip`= '$ip'";
	$result = mysql_query($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows)
		return 0;
	return 1;
}

function displayerror($error_desc) {
	global $ERRORSTRING;
	$ERRORSTRING .= "<div class=\"cms-error\">$error_desc</div>";
}
function displayinfo($info) {
	global $INFOSTRING;
	$INFOSTRING .= "<div class=\"cms-error\">$info</div>";
}

/**Used for giving warning*/
function displaywarning($error_desc) {
	global $WARNINGSTRING;
	$WARNINGSTRING .= "<div class=\"cms-warning\">$error_desc</div>";
}

/**
 * Convert an array to a string recursively
 * @param $array Array to convert
 * @return string containing the array information
 */
 function arraytostring($array) {
	$text = "array(";
	$count=count($array);
	$x=0;
	foreach ($array as $key=>$value) {
		$x++;
		if (is_array($value)) {
			if(substr($text,-1,1)==')')
				$text .= ',';
			$text.='"'.$key.'"'."=>".arraytostring($value);
			continue;
		}

		$text.="\"$key\"=>\"$value\"";
		if ($count!=$x)
			$text.=",";
	}

	$text.=")";

	if(substr($text, -4, 4)=='),),')$text.='))';
		return $text;
}

/**
 * Determines the User Name of a user, given his/her User Id
 * @param $userId User Id of the user, whose User Name is to be determined
 * @return string containing the User Name of the user, null representing failure
 */
function getUserName($userId) {
	if($userId <= 0) return "Anonymous";
	$query = "SELECT `user_name` FROM `".MYSQL_DATABASE_PREFIX."users` WHERE `user_id` = '".$userId."'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

/**
 * Determines the Full Name of a user, given his/her User Id
 * @param $userId User Id of the user, whose Full Name is to be determined
 * @return string containing the Full Name of the user, null representing failure
 */
function getUserFullName($userId) {
	if($userId <= 0) return "Anonymous";
	$query = "SELECT `user_fullname` FROM `".MYSQL_DATABASE_PREFIX."users` WHERE `user_id` = '".$userId."'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

/**
 * Determines the Full Name of a user, given his/her Email ID
 * @param $email Email Id of the user, whose Full Name is to be determined
 * @return string containing the Full Name of the user, null representing failure
 */
function getUserFullNameFromEmail($email) {
	$query = "SELECT `user_fullname` FROM `".MYSQL_DATABASE_PREFIX."users` WHERE `user_email` = '".$email."'";
	$result = mysql_query($query);
	
	$row = mysql_fetch_row($result);
	return $row[0];
}

/**
 * Determines the Email-Id of a user, given his/her User Id
 * @param $userid User Id of the user, whose E-mail address is to be determined
 * @return string containing the e-mail address of the user, null representing failure
 */
function getUserEmail($userId) {
	if($userId <= 0) return 'Anonymous';
	$query="SELECT `user_email` FROM `".MYSQL_DATABASE_PREFIX."users` WHERE `user_id` = '".$userId."'";
	$result = mysql_query($query);
	$row= mysql_fetch_row($result);
	return $row[0];
}

/**
 * Determines the User Id of a user, given his/her E-mail Id
 * @param $email E-mail address of the user, whose User Id is to be determined
 * @return Integer representing the User Id of the user, null representing failure
 */
function getUserIdFromEmail($email) {
	if(strtolower($email) == 'anonymous') return 0;
	$query = 'SELECT `user_id` FROM `'.MYSQL_DATABASE_PREFIX."users` WHERE `user_email` = '".$email."'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}


/**
 * Determines the module type of a given page
 * @param $pageid Page id of the page, whose module name is to be determined
 * @return String containing the module name of the given page
 */
function getEffectivePageModule($pageId) {
	$pagemodule_query = "SELECT `page_module`, `page_modulecomponentid` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`='".$pageId."'";
	$pagemodule_result = mysql_query($pagemodule_query);
	$pagemodule_row = mysql_fetch_assoc($pagemodule_result);
	if($pagemodule_row['page_module']=="link")	return (getEffectivePageModule($pagemodule_row['page_modulecomponentid']));
	return $pagemodule_row['page_module'];
}

/**
 * Gets the next module component id of a given module, which can be used for creating new instances of the same module.
 * @param $modulename Name of the module
 * @return Integer representing the new module component id
 */
function getNextModuleComponentId($modulename) {
		$moduleComponentIdQuery = "SELECT MAX(page_modulecomponentid) FROM `".MYSQL_DATABASE_PREFIX."_pages` WHERE `page_module`='$modulename'";
		$moduleComponentIdResult = mysql_query($moduleComponentIdQuery);
		if(!$moduleComponentIdResult)
			return 0;
		$moduleComponentIdRow = mysql_fetch_row($moduleComponentIdResult);
		if(!is_null($moduleComponentIdRow[0]))
			return $moduleComponentIdRow[0] + 1;
		return 1;
}


/**
 * Determines the dereferenced Page Id of a given page
 * @param $pageid Page id of the page (link) to be dereferenced
 * @return Integer indicating the dereferenced page id
 */
function getDereferencedPageId($pageId) {
	$pagemodule_query = "SELECT `page_module`, `page_modulecomponentid` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`='".$pageId."'";
	$pagemodule_result = mysql_query($pagemodule_query);
	$pagemodule_row = mysql_fetch_assoc($pagemodule_result);
	if($pagemodule_row['page_module']=="link") {
		return getDereferencedPageId($pagemodule_row['page_modulecomponentid']);
	}
	if($pagemodule_row['page_module']=="external") {
		return $pagemodule_row['page_modulecomponentid'];
	}
	return $pageId;
}



function getPagePath($pageid) {
	$pagepath = '';

	while($pageid != 0) {
		$pathQuery = "SELECT `page_parentid`, `page_name` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id` = '".$pageid."'";
		$pathResult = mysql_query($pathQuery);
		$pathResultRow = mysql_fetch_row($pathResult);

		$pageid = $pathResultRow[0];
		$pagepath = $pathResultRow[1]."/$pagepath";
	}

	return "/$pagepath";
}

function getPageModule($pageId) {
	$pagemodule_query = "SELECT `page_module` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`=".$pageId;
	$pagemodule_result = mysql_query($pagemodule_query);
	$pagemodule_row = mysql_fetch_assoc($pagemodule_result);
	return $pagemodule_row['page_module'];
}
function getPageTitle($pageId) {
	$pagemodule_query = "SELECT `page_title` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`='".$pageId."'";
	$pagemodule_result = mysql_query($pagemodule_query);
	$pagemodule_row = mysql_fetch_assoc($pagemodule_result);
	return $pagemodule_row['page_title'];
}



/**
 * Determines the page id of the parent of a given page
 * @param $pageid Page id of the page, whose parent is to be determined
 * @return Integer indicating the page id of the parent page
 */
function getParentPage($pageid) {
	$pageparent_query = "SELECT `page_parentid` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`='".$pageid."'";
	$pageparent_result = mysql_query($pageparent_query);
	$pageparent_row = mysql_fetch_assoc($pageparent_result);
	return $pageparent_row['page_parentid'];
}
function getPageInfo($pageid) {
	$pageparent_query = "SELECT `page_id`, `page_name`, `page_parentid`, `page_title`, `page_module`, `page_modulecomponentid`, `page_menurank`, `page_inheritedinfoid`, `page_displayinmenu`, `page_displaymenu`, `page_displaysiblingmenu`, `page_menutype`, `page_menudepth`, `page_image`, `page_displayicon` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`='".$pageid."'";
	$pageparent_result = mysql_query($pageparent_query);
	$pageparent_row = mysql_fetch_assoc($pageparent_result);
	return $pageparent_row;
}
function getPageModuleComponentId($pageid) {
	$pageparent_query = "SELECT `page_modulecomponentid` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_id`='".$pageid."'";
	$pageparent_result = mysql_query($pageparent_query);
	$pageparent_row = mysql_fetch_assoc($pageparent_result);
	return $pageparent_row['page_modulecomponentid'];
}
function getPageIdFromModuleComponentId($moduleName,$moduleComponentId) {
	$moduleid_query = "SELECT `page_id` FROM `".MYSQL_DATABASE_PREFIX."pages` WHERE `page_module` = '".$moduleName."' AND `page_modulecomponentid` = '".$moduleComponentId."'";
	$moduleid_result = mysql_query($moduleid_query);
	$moduleid_row = mysql_fetch_assoc($moduleid_result);
	return $moduleid_row['page_id'];
}

function getModuleComponentIdFromPageId($pageId, $moduleName) {
	$moduleIdQuery = 'SELECT `page_modulecomponentid` FROM `' . MYSQL_DATABASE_PREFIX . "pages` WHERE `page_module` = '".$moduleName."' AND `page_id` = '".$pageId."'";
	$moduleIdResult = mysql_query($moduleIdQuery);
	$moduleIdRow = mysql_fetch_row($moduleIdResult);
	return $moduleIdRow[0];
}
/**
 *@author boopathi
 *@description returns the depth of the page - 0 if the page is a child of /home
 *@param pageId
 *@return pageDepth
 **/
function getPageDepth($pageId) {
	$depth = 1;
	if(getParentPage($pageId) == 0)
		return 0;
	else
		return $depth + getPageDepth(getParentPage($pageId));
}

function logInfo ($userEmail, $userId, $pageId, $pagePath, $permModule, $permAction, $accessIpAddress) {
	if(isRequiredMaintenance()) {
		require_once("maintenance.lib.php");
		runMaintenance();
	}
	if($pageId === false) $pageId = -1;
	if(isset($_GET['fileget']))	return false;

	$updateQuery = "SELECT `log_no` FROM `".MYSQL_DATABASE_PREFIX."log` WHERE `log_no` = 1";
	$result = mysql_query($updateQuery);
	
	if(!$result || mysql_num_rows($result) == 0)
		$updateQuery = "INSERT INTO `".MYSQL_DATABASE_PREFIX."log` (`log_no`, `user_email`, `user_id`, `page_id`, `page_path`, `perm_module`, `perm_action`, `user_accessipaddress`)
    	VALUES ( 1  , '".$userEmail."', ".$userId.", ".$pageId.", '".$pagePath."', '".$permModule."', '".$permAction."', '".$accessIpAddress."' );";
    else
    	$updateQuery = "INSERT INTO `".MYSQL_DATABASE_PREFIX."log` (`log_no`, `user_email`, `user_id`, `page_id`, `page_path`, `perm_module`, `perm_action`, `user_accessipaddress`)
    	( SELECT (MAX(log_no)+1)  , '".$userEmail."', ".$userId.", ".$pageId.", '".$pagePath."', '".$permModule."', '".$permAction."', '".$accessIpAddress."' FROM  `".MYSQL_DATABASE_PREFIX."log`);";
    
    if(!mysql_query($updateQuery))
    	displayerror ("Error in logging info.");
    return true;
}

#returns true for first access of every 10 day slab
#select date > sub(now, diff(now,first)%10)

/**
 * Replaces the protocol in a url with https://
 * @param $url Url to be converted
 * @return Converted Url
 */
function convertToHttps($url){
	if(!strncasecmp("https://",$url,8))
		return $url;
	else
		return str_replace("http://","https://",$url);
}

/**
 * Replaces the protocol in a url with http://
 * @param $url Url to be converted
 * @return Converted Url
 */
function convertToHttp($url){
	if(!strncasecmp("http://",$url,7))
		return $url;
	else {
		$pos = strpos($url, '://');
		if($pos >= 0) {
			return 'http://' . substr($url, $pos + 3);
		}
		else return $url;
	}
}

function verifyHttps($url){
	if(!strncasecmp("https://",$url,7))
		return true;
	else 
		return false;
}

function selfURI() {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

function hostURL() {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    
    $scriptname = isset($_SERVER['ORIG_SCRIPT_NAME'])?$_SERVER['ORIG_SCRIPT_NAME']:$_SERVER['SCRIPT_NAME'];
    $location = substr($scriptname,0,strpos($scriptname,"/index.php"));
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$location;
}

/**
 * Replaces the action in the url to a new action
 *
 * @param $url Initial URL
 * @param $old Old Action
 * @param $new New Action
 *
 * @return the URL with the new action
 * @TODO check for rewrite enabled and handle +action as well as &action=action kind of URLs
 * @warning Whats the guarantee it won't convert some word in the URL which matches the Old Action ?
 */
function replaceAction($url,$old,$new) {
   $offset = strpos($url,"action=$old");
   $url = substr_replace($url,$new,$offset+7,strlen($old));
   return $url;
}

function strleft($s1, $s2) {
    return substr($s1, 0, strpos($s1, $s2));
}

function updateUserPassword($user_email,$user_passwd) {
	$query = "UPDATE `" . MYSQL_DATABASE_PREFIX . "users` SET `user_password`= '".md5($user_passwd)."' WHERE `" . MYSQL_DATABASE_PREFIX . "users`.`user_email` = '" . $user_email . "'";
							mysql_query($query) or die(mysql_error() . " in function updateUserPassword");
}

function getUserInfo($user_email) {
	$query = "SELECT `user_id`,`user_password`,`user_name`,`user_activated`,`user_lastlogin`,`user_loginmethod` FROM `" . MYSQL_DATABASE_PREFIX . "users` WHERE `user_email` = '" . $user_email . "'";
	$result = mysql_query($query) or die(mysql_error() . " in function getUserInfo : common.lib.php");
	return mysql_fetch_assoc($result);
}
function getTableFieldsName($tablename,$exclude="user_profilepic")
{
	$query="SELECT * FROM ".MYSQL_DATABASE_PREFIX.$tablename;
	$result=mysql_query($query);
	$numfields=mysql_num_fields($result);
	$fields=array();
	$i=0;
	$exclist=explode(",",$exclude);
	while($i<$numfields)
	{
		$meta=mysql_fetch_field($result,$i);
		if($meta && array_search($meta->name,$exclist)===FALSE)
		{
			$fields[$i]=$meta->name;
		}
		$i++;
	}
	return $fields;
}

function getNextUserId()
{
	$query="SELECT max(user_id) FROM ".MYSQL_DATABASE_PREFIX."users";
	$result=mysql_query($query);
	$row=mysql_fetch_row($result);
	return $row[0]+1;
}

function showBreadcrumbSubmenu()
{
	$query="SELECT `value` FROM `".MYSQL_DATABASE_PREFIX."global` WHERE `attribute`='breadcrumb_submenu'";
	$result = mysql_fetch_row(mysql_query($query));
	return $result[0];
}

function getFileActualPath($moduleType,$moduleComponentId,$fileName)
{
	$query = "SELECT * FROM `" . MYSQL_DATABASE_PREFIX . "uploads` WHERE  `upload_filename`= '". escape($fileName). "' AND `page_module` = '".escape($moduleType)."' AND `page_modulecomponentid` = '".escape($moduleComponentId)."'";
	$result = mysql_query($query) or die(mysql_error() . "upload L:85");
	$row = mysql_fetch_assoc($result);
	/**
	 * Not checking if filetype adheres to uploadable filetype list beacuse this check can be
	 * performed in $moduleInstance->getFileAccessPermission.
	 */

	global $sourceFolder,$uploadFolder;
	$upload_fileid = $row['upload_fileid'];
	
	$filename = str_repeat("0", (10 - strlen((string) $upload_fileid))) . $upload_fileid . "_" . $fileName;
	
	$file = $sourceFolder . "/" . $uploadFolder . "/" . $moduleType . "/" . $filename;
	return $file;
}
/**
 *  Checks for presence of the cURL extension for OpenID.
*/
function iscurlinstalled() {
  if  (in_array  ('curl', get_loaded_extensions())) {
    return true;
  }
  else{
    return false;
  }
}
$curl_message="cURL extention is not enabled/installed on your system. OpenID requires this extention to be loaded. Please enable cURL extention. (This can be done by uncommenting the line \"extension=curl.so\" in your php.ini file). OpenID can't be enabled until you enable cURL.";
function censor_words($text)
{
	$query = "SELECT `value` FROM `".MYSQL_DATABASE_PREFIX."global` WHERE `attribute` = 'censor_words'";
	$words = mysql_query($query);
	$words = mysql_fetch_row($words);
	$replace = "<b>CENSORED</b>";
	if($words[0]=='')
		return $text;
	else
		$res = preg_replace("/$words[0]/i",$replace,$text);
	return $res;
}

