<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: confirmation.php,v 1.42 2000/01/13 18:36:36 precision Exp $

require "pre.php";    // Initial db and session library, opens session
session_require(array(isloggedin=>1));
require "vars.php";
require('../forum/forum_utils.php');

if ($group_id && $form_category && $rand_hash) {
	/*

		Finalize the db entries

	*/

	$result=db_query("UPDATE groups SET status='P' WHERE group_id='$group_id' AND rand_hash='__$rand_hash'");
	if (db_affected_rows($result) < 1) {
		exit_error('Error','UDPATING TO ACTIVE FAILED. <B>PLEASE</B> report to admin@sourceforge.net'.db_error());
	}

	/*
		set up environments and languages for this group
	*/
	if (count($form_env)<1) {
		$form_env[]=1;
	}

	if (count($form_lang)<1) {
		$form_lang[]=1;
	}

	for ($i=0; $i<count($form_env); $i++) {
		db_query("INSERT INTO group_env (group_id,env_id) VALUES ('$group_id','$form_env[$i]')");
	}

	for ($i=0; $i<count($form_lang); $i++) {
		db_query("INSERT INTO group_language (group_id,language_id) VALUES ('$group_id','$form_lang[$i]')");
	}

	// put it in a category
	$result=db_query("INSERT INTO group_category (group_id,category_id,primary_category) VALUES ('$group_id','$form_category','1')"); 
	if (!$result) {
		exit_error('Error','INSERTING CATEGORY FAILED. <B>PLEASE</B> report to admin@sourceforge.net'.db_error());
	}

	// define a module
	$result=db_query("INSERT INTO filemodule (group_id,module_name) VALUES ('$group_id','".group_getunixname($group_id)."')");
	if (!$result) {
		exit_error('Error','INSERTING FILEMODULE FAILED. <B>PLEASE</B> report to admin@sourceforge.net'.db_error());
	}

	// make the current user an admin
	$result=db_query("INSERT INTO user_group (user_id,group_id,admin_flags,bug_flags,forum_flags) VALUES ("
		. user_getid() . ","
		. $group_id . ","
		. "'A'," // admin flags
		. "2," // bug flags
		. "2)"); // forum_flags	
	if (!$result) {
		exit_error('Error','SETTING YOU AS OWNER FAILED. <B>PLEASE</B> report to admin@sourceforge.net'.db_error());
	}

	//Add a couple of forums for this group
	forum_create_forum($group_id,'Open Discussion',1);
	forum_create_forum($group_id,'Help',1);
	forum_create_forum($group_id,'Developers',0);

	//Set up some mailing lists
	//will be done at some point. needs to communicate with geocrawler

	//
	site_header(array('title'=>'Registration Complete'));
	
	?>

	<H1>Registration Complete!</H1>
	<P>Your project has been submitted to the SourceForge admininstrators. 
	Within 24 hours, you will receive decision notification and further 
	instructions.
	<P>
	Thank you for choosing SourceForge.
	<P>

	<?php
	site_footer(array());
	site_cleanup(array());

} else {
	exit_error('Error','This is an invalid state. Some form variables were missing.
		If you are certain you entered everything, <B>PLEASE</B> report to admin@sourceforge.net and
		include info on your browser and platform configuration');

}

?>

