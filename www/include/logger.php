<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: logger.php,v 1.15 2000/01/13 18:36:35 precision Exp $


/*
	Determine browser and version
*/

if (ereg( 'MSIE ([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
	$log_browser_ver=$log_version[1];
	$log_browser='IE';
} elseif (ereg( 'Opera ([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
	$log_browser_ver=$log_version[1];
	$log_browser='OPERA';
} elseif (ereg( 'Mozilla/([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
        $log_browser_ver=$log_version[1];
	$log_browser='MOZILLA';
} else {
	$log_browser_ver=0;
	$log_browser='OTHER';
}

/*
	Determine platform
*/

if (strstr($HTTP_USER_AGENT,'Win')) {
	$log_platform='Win';
} else if (strstr($HTTP_USER_AGENT,'Mac')) {
	$log_platform='Mac';
} else if (strstr($HTTP_USER_AGENT,'Linux')) {
        $log_platform='Linux';
} else if (strstr($HTTP_USER_AGENT,'Unix')) {
        $log_platform='Unix';
} else {
	$log_platform='Other';
}

/*
	Determine group
*/

if ($group_id) {
	$log_group=$group_id;
} else if ($form_grp) {
	$log_group=$form_grp;
} else {
	$log_group=0;
}

$res_logger = db_query ("INSERT INTO activity_log (day,hour,group_id,browser,ver,platform,time,page,type) ".
	"VALUES (".date('Ymd', mktime()).",'".date('H', mktime())."','$log_group','$log_browser','$log_browser_ver','$log_platform','".time()."','$PHP_SELF','0');");
if (!$res_logger) {
	echo "An error occured in the logger.\n";
	echo db_error();
	exit;
}
/*
echo "<P>HTTP_USER_AGENT - $HTTP_USER_AGENT".
        "<BR>browser - $log_browser".
        "<BR>browser_ver - $log_browser_ver".
        "<BR>platform - $log_platform".
	"<BR>page - $PHP_SELF";
*/
?>
