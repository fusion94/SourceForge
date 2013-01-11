<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: user.php,v 1.57 2000/01/13 18:36:35 precision Exp $

// ############ User functions

// ############################### function user_isloggedin()

function user_isloggedin() {
	global $G_USER;
	return ($G_USER);
}

// ############################### function user_ismember

function user_ismember($group_id,$type=0) {

	unset($user_id); //paranoid
	$user_id=user_getid(); //optimization

	/*
		list of SF admins always return true
	*/
	if ($user_id==2 || $user_id==3 ||  $user_id==858 || $user_id==5) {
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
                case 'F2' : {
			//forum admin
                        $query .= ' AND forum_flags IN (2)';
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

// ############################## function user_getid()

function user_getid() {
	global $G_USER;
	return ($G_USER?$G_USER[user_id]:0);
}

// ############################# function user_getname()

function user_getname($user_id = 0) {
	global $G_USER;
	// use current user if one is not passed in
	if (!$user_id) {
		return ($G_USER?$G_USER[user_name]:"NA");
	}
	// else must lookup name
	else {
		$result = db_query("SELECT user_id,user_name FROM user WHERE user_id='$user_id'");
		if ($result && db_numrows($result) > 0) {
			return (db_result($result,0,"user_name"));
		} else {
			return "<B>Invalid User ID</B>";
		}
	}
}

function user_set_preference($preference_name,$value) {
	$preference_name=strtolower(trim($preference_name));
	$result=db_query("UPDATE user_preferences SET preference_value='$value' ".
		"WHERE user_id='".user_getid()."' AND preference_name='$preference_name'");
	if (db_affected_rows($result) < 1) {
		echo db_error();
		$result=db_query("INSERT INTO user_preferences (user_id,preference_name,preference_value) ".
			"VALUES ('".user_getid()."','$preference_name','$value')");
	}
}

function user_get_preference($preference_name) {
	GLOBAL $user_pref;
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
}
?>
