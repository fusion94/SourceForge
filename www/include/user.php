<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: user.php,v 1.76 2000/08/30 21:15:40 tperdue Exp $

unset($USER_IS_SUPER_USER);
$USER_RES=array();

function user_isloggedin() {
	global $G_USER;
	if ($G_USER['user_id']) {
		return true;
	} else {
		return false;
	}
}

function user_is_super_user() {
	global $USER_IS_SUPER_USER;
	/*
		members of group_id 1 who are admins have super-user privs site-wide
	*/

	if (isset($USER_IS_SUPER_USER)) {
		return $USER_IS_SUPER_USER;
	} else {
		if (user_isloggedin()) {
			$sql="SELECT * FROM user_group WHERE user_id='". user_getid() ."' AND group_id='1' AND admin_flags='A'";
			$result=db_query($sql);
			if (!$result || db_numrows($result) < 1) {
				$USER_IS_SUPER_USER=false;
				return $USER_IS_SUPER_USER;
			} else {
				//matching row was found - set and save this knowledge for later
				$USER_IS_SUPER_USER=true;
				return $USER_IS_SUPER_USER;
			}
		} else {
			$USER_IS_SUPER_USER=false;
			return $USER_IS_SUPER_USER;
		}
	}
}

function user_ismember($group_id,$type=0) {
	if (!user_isloggedin()) {
		return false;
	}
	$user_id=user_getid(); //optimization

	/*
		SF admins always return true
	*/
	if (user_is_super_user()) {
		return true;
	}

	/*
		for everyone else, do a query
	*/
	$query = "SELECT user_id FROM user_group "
		. "WHERE user_id='$user_id' AND group_id='$group_id'";

	$type=strtoupper($type);

	switch ($type) {
		/*
			list the supported permission types
		*/
		case 'B1' : {
			//bug tech
			$query .= ' AND bug_flags IN (1,2)';
			break;
		}
		case 'B2' : {
			//bug admin
			$query .= ' AND bug_flags IN (2,3)';
			break;
		}
		case 'P1' : {
			//pm tech
			$query .= ' AND project_flags IN (1,2)';
			break;
		}
		case 'P2' : {
			//pm admin
			$query .= ' AND project_flags IN (2,3)';
			break;
		}
		case 'C1' : {
			//patch tech
			$query .= ' AND patch_flags IN (1,2)';
			break;
		}
		case 'C2' : {
			//patch admin
			$query .= ' AND patch_flags IN (2,3)';
			break;
		}
		case 'F2' : {
			//forum admin
			$query .= ' AND forum_flags IN (2)';
			break;
		}
		case 'S1' : {
			//support tech
			$query .= ' AND support_flags IN (1,2)';
			break;
		}
		case 'S2' : {
			//support admin
			$query .= ' AND support_flags IN (2,3)';
			break;
		}
		case '0' : {
			//just in this group
			break;
		}
		case 'A' : {
			//admin for this group
			$query .= " AND admin_flags = 'A'";
			break;
		}
		case 'D1' : {
			//document editor
			$query .= " AND doc_flags = '1'";
			break;
		}
		default : {
			//fubar request
			return false;
		}
	}

	$res = db_query($query);
	if (!$res || db_numrows($res) < 1) {
		//matching row wasn't found
		return false;
	} else {
		//matching row was found
		return true;
	}
}

function user_getid() {
	global $G_USER;
	return ($G_USER?$G_USER['user_id']:0);
}

function user_getname($user_id = 0) {
	global $G_USER,$USER_NAMES;
	// use current user if one is not passed in
	if (!$user_id) {
		return ($G_USER?$G_USER['user_name']:"NA");
	}
	// else must lookup name
	else {
		if ($USER_NAMES["user_$user_id"]) {
			//user name was fetched previously
			return $USER_NAMES["user_$user_id"];
		} else {
			//fetch the user name and store it for future reference
			$result = db_query("SELECT user_id,user_name FROM user WHERE user_id='$user_id'");
			if ($result && db_numrows($result) > 0) {
				//valid user - store and return
				$USER_NAMES["user_$user_id"]=db_result($result,0,"user_name");
				return $USER_NAMES["user_$user_id"];
			} else {
				//invalid user - store and return
				$USER_NAMES["user_$user_id"]="<B>Invalid User ID</B>";
				return $USER_NAMES["user_$user_id"];
			}
		}
	}
}

//quick hack - this entire library needs a rewrite similar to groups library
function user_getrealname($user_id) {
	$result = user_get_result_set($user_id); 
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,"realname");
	} else {
		return 'Error - Not Found';
	}
}

function user_get_result_set($user_id) {
	//create a common set of user result sets,
	//so it doesn't have to be fetched each time

	global $USER_RES;
	if (!$USER_RES["_".$user_id."_"]) {
		$USER_RES["_".$user_id."_"]=db_query("SELECT * FROM user WHERE user_id='$user_id'");
		return $USER_RES["_".$user_id."_"];
	} else {
		return $USER_RES["_".$user_id."_"];
	}
}

function user_get_result_set_from_unix($user_name) {
	//create a common set of user result sets,
	//so it doesn't have to be fetched each time
		
	global $USER_RES;
	if (!$USER_RES["_".$user_id."_"]) {
		$USER_RES["_".$user_id."_"]=db_query("SELECT * FROM user WHERE user_name='$user_name'");
		return $USER_RES["_".$user_id."_"];
	} else {
		return $USER_RES["_".$user_id."_"];
	}
}       

function user_get_timezone() {
	if (user_isloggedin()) {
		$result=user_get_result_set(user_getid());
		return db_result($result,0,'timezone');
	} else {
		return '';
	}
}

function user_set_preference($preference_name,$value) {
	if (user_isloggedin()) {
		$preference_name=strtolower(trim($preference_name));
		$result=db_query("UPDATE user_preferences SET preference_value='$value' ".
			"WHERE user_id='".user_getid()."' AND preference_name='$preference_name'");
		if (db_affected_rows($result) < 1) {
			echo db_error();
			$result=db_query("INSERT INTO user_preferences (user_id,preference_name,preference_value) ".
				"VALUES ('".user_getid()."','$preference_name','$value')");
		}
	} else {
		return false;
	}
}

function user_get_preference($preference_name) {
	GLOBAL $user_pref;
	if (user_isloggedin()) {
		$preference_name=strtolower(trim($preference_name));
		/*
			First check to see if we have already fetched the preferences
		*/
		if ($user_pref) {
			if ($user_pref["$preference_name"]) {
				//we have fetched prefs - return part of array
				return $user_pref["$preference_name"];
			} else {
				//we have fetched prefs, but this pref hasn't been set
				return false;
			}
		} else {
			//we haven't returned prefs - go to the db
			$result=db_query("SELECT preference_name,preference_value FROM user_preferences ".
				"WHERE user_id='".user_getid()."'");
			if (db_numrows($result) < 1) {
				return false;
			} else {
				//iterate and put the results into an array
				for ($i=0; $i<db_numrows($result); $i++) {
					$user_pref[db_result($result,$i,'preference_name')]=db_result($result,$i,'preference_value');
				}
				if ($user_pref["$preference_name"]) {
					//we have fetched prefs - return part of array
					return $user_pref["$preference_name"];
				} else {
					//we have fetched prefs, but this pref hasn't been set
					return false;
				}
			}
		}
	} else {
		return false;
	}
}

?>