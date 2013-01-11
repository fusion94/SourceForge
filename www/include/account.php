<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: account.php,v 1.22 2000/01/29 09:11:03 dtype Exp $
//
// adduser.php - All the forms and functions to manage unix users
//

// ***** function account_pwvalid()
// ***** check for valid password

function account_pwvalid($pw) {
        if (strlen($pw) < 6) {
                $GLOBALS[register_error] = "Password must be at least 6 characters.";
                return 0;
        }
        return 1;
}


// ***** function account_namevalid()
// ***** check for good unix username

function account_namevalid($name) {
	// no spaces
	if (strrpos($name,' ') > 0) {
		$GLOBALS[register_error] = "There cannot be any spaces in the login name.";	
		return 0;
	}

	// must have at least one character
	if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") == 0) {
		$GLOBALS[register_error] = "There must be at least one character.";
		return 0;
	}

	// must contain all legal characters
	//if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#\$%^&*()-_\\/{}[]<>+=|;:?.,`~")
	if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_")
		!= strlen($name)) {
		$GLOBALS[register_error] = "Illegal character in name.";
		return 0;
	}

	// min and max length
	if (strlen($name) < 3) {
		$GLOBALS[register_error] = "Name is too short. It must be at least 3 characters.";
		return 0;
	}
	if (strlen($name) > 15) {
		$GLOBALS[register_error] = "Name is too long. It must be less than 15 characters.";
		return 0;
	}

	// illegal names
	if (eregi("^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)"
		. "|(uucp)|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)"
		. "|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download))$",$name)) {
		$GLOBALS[register_error] = "Name is reserved.";
		return 0;
	}
	if (eregi("^(anoncvs_)",$name)) {
		$GLOBALS[register_error] = "Name is reserved for CVS.";
		return 0;
	}
		
        return 1;
}

function account_groupnamevalid($name) {
	if (!account_namevalid($name)) return 0;
	
	// illegal names
	if (eregi("^((www[0-9]?)|(cvs[0-9]?)|(shell[0-9]?)|(ftp[0-9]?)|(irc[0-9]?)|(news[0-9]?)"
		. "|(mail[0-9]?)|(ns[0-9]?)|(download[0-9]?)|(pub)|(users)|(mirrors?))$",$name)) {
		$GLOBALS[register_error] = "Name is reserved for DNS purposes.";
		return 0;
	}

	if (eregi("_",$name)) {
		$GLOBALS[register_error] = "Group name cannot contain underscore for DNS reasons.";
		return 0;
	}

	return 1;
}

// The following is a random salt generator
function account_gensalt(){
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

// generate unix pw
function account_genunixpw($plainpw) {
	return crypt($plainpw,account_gensalt());
}

// returns next userid
function account_nextuid() {
	db_query("SELECT max(unix_uid) AS maxid FROM user");
	$row = db_fetch_array();
	return ($row[maxid] + 1);
}

// print out shell selects
function account_shellselects($current) {
	$shells = file("/etc/shells");

	for ($i = 0; $i < count($shells); $i++) {
		$this_shell = chop($shells[$i]);

		if ($current == $this_shell) {
			echo "<option selected value=$this_shell>$this_shell</option>\n";
		} else {
			echo "<option value=$this_shell>$this_shell</option>\n";
		}
	}
}

