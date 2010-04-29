<?php defined('SYSPATH') or die('No direct script access.');
/**
* Initiate Instance. Verify Install
* If we can't find application/config/database.php, we assume Ushahidi
* is not installed so redirect user to installer
*/
if (!file_exists(DOCROOT."application/config/database.php"))
{
	if ($_SERVER["SERVER_PORT"] != "80") {
		$url = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
		
	$installer =  "http://$url"."installer/";
		
	url::redirect($installer);
	
}

	//	echo " in application/hooks/init.php _SERVER['ORIG_PATH_INFO'] = ".$_SERVER['ORIG_PATH_INFO']."<br/>";
	//	echo "  in application/hooks/init.php _SERVER['PATH_INFO'] = ".$_SERVER['PATH_INFO']."<br/>";

//		$url = str_replace(Kohana::config('config.site_domain'), '/', $_SERVER["REQUEST_URI"]);

	/**
	 * The following lines we removed and replaced with a rewrite rule in the .htaccess file
	 */
	//	$url = str_replace(Kohana::config('config.site_domain'), '', $_SERVER["REQUEST_URI"]);
	//	$_SERVER['ORIG_PATH_INFO'] = $url;
	//	$_SERVER['PATH_INFO'] = $url;
	//	$_SERVER['PHP_SELF'] = $url;
