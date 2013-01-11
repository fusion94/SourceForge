<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.8 2000/01/13 18:36:34 precision Exp $
//
// adduser.php - All the forms and functions to manage unix users

require("pre.php");
session_require(array('group'=>'1'));

parse_str ($argv[0]);  // parse out $argv and get $action

// The following is a random salt generator
function gensalt(){
	function rannum(){             
		mt_srand((double)microtime()*1000000);                  
		$num = mt_rand(46,122);                  
		return $num;                  
	}             
	function genchr(){
		do {          
			$num = rannum();                  
		} while ( ( $num > 57 && $num < 65 ) || ( $num > 90 && $num < 97 ) );          
		$char = chr($num);          
		return $char;          
	}           

	$a = genchr(); 
	$b = genchr();
	$salt = "$1$" . "$a$b";
	return $salt;        
}
$salt = gensalt();

if (!$action) {

	site_header(array(title=>"Unix User Account Management"));

	include("view_user.php");

} elseif ($action == "add") {

	if (!$username) {
		site_header(array(title=>"Add a New User"));
		include("add_user.php");
	} else {

		$result = db_query("SELECT * FROM unix_user WHERE username = '$username'");

		// Check to see if the username is in use
		if (db_result($result, 0, 2)) {
			site_header(array(title=>"UserName in Use"));
			echo "<h1>Your Username is already in use. Please pick another.</h1>\n";
			$username = "";
			include("add_user.php");

		// make sure that the passwords are identical
		} elseif ($form_password != $password_retype) {
			site_header(array(title=>"Invalid Password"));
			echo "<h1>You passwords don't match please try again</h1>\n";
			include("add_user.php");

		// passed everything else now create the record
		} else {
	
			$hashed_password = crypt($form_password, $salt);
	
			$datetime = time();

			$md5_password = md5($form_password);

			$added_by = user_getname(user_getid());

			$datetime = time();
	
			db_query("INSERT INTO unix_user VALUES ('', '1', '$username', '$user_id', '$shell', '$hashed_password', '$md5_password', '$added_by', '$datetime')");
			
			site_header(array(title=>"User Added"));
	
			echo "<h1>The user has been added to the database</h1>\n";
		}
	}

} elseif ($action == "delete") {
	if (!$uid) {
		site_header(array(title=>"You must supply a UID"));
		echo "<h1>You supply a User ID</h1>\n";
	} else {
		$sql = "UPDATE unix_user SET status = 1 WHERE id = $uid";
		$result = db_query($sql);
		
		site_header(array(title=>"User Account Deleted"));
		echo "<h1>This Users Account has been marked for deletion</h1>\n";
	}
} elseif ($action == "details") {
	if (!$uid) {
		site_header(array(title=>"You must supply a UID"));
		echo "<h1>You supply a User ID</h1>\n";
	} else {
		site_header(array(title=>"User Details"));
		include("details_user.php");
	}

} elseif ($action == "update") {

	if (!$uid) {
		site_header(array(title=>"You must supply a UID"));
		echo "<h1>You supply a User ID</h1>\n";
	} else {

		if ($form_password != $password_retype) {
			site_header(array(title=>"The passwords you supplied don't match"));
			echo "<h1>The passwords you supplied don't match</h1>\n";
		} elseif (!$form_password) {

			$sql = "UPDATE unix_user SET status=\"$status\", user_id=\"$user_id\", shell=\"$shell\" WHERE id = \"$uid\"";
			$result = db_query($sql);

			site_header(array(title=>"User Account Updated"));
			echo "<h1>This Users Account has been Updated</h1>\n";
		} else {
			$new_md5_password = md5($form_password);
			$new_password_hash = crypt($form_password, $salt);
			
			$sql = "UPDATE unix_user SET status=\"$status\", user_id=\"$user_id\", shell=\"$shell\", password=\"$new_password_hash\", md5_password=\"$new_md5_password\" WHERE id = \"$uid\"";
			
			$result = db_query($sql);

			site_header(array(title=>"User Account Updated"));
			echo "<h1>This Users Account has been Updated</h1>\n";
		}
	}
}
	

site_footer(array());
site_cleanup(array());
