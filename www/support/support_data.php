<?php

function support_data_get_categories ($group_id) {
	/*
		List of possible support_categories set up for the project
	*/
	$sql="select support_category_id,category_name from support_category WHERE group_id='$group_id'";
	return db_query($sql);
}

function support_data_get_technicians ($group_id) {
	/*
		List of people that can be assigned this support request
	*/
	$sql="SELECT user.user_id,user.user_name ".
		"FROM user,user_group ".
		"WHERE user.user_id=user_group.user_id ".
		"AND user_group.support_flags IN (1,2) ".
		"AND user_group.group_id='$group_id'";
	return db_query($sql);
}


function support_data_get_canned_responses ($group_id) {
	/*
		show defined canned responses for this project
		and the site-wide canned responses
	*/
	$sql="SELECT support_canned_id,title,body ".
		"FROM support_canned_responses ".
		"WHERE (group_id='$group_id' OR group_id='0')";
	return db_query($sql);
}

function support_data_get_statuses() {
	$sql="select * from support_status";
	return db_query($sql);
}

function support_data_get_history ($support_id) {
	$sql="select support_history.field_name,support_history.old_value,support_history.date,user.user_name ".
                "FROM support_history,user ".
                "WHERE support_history.mod_by=user.user_id ".
                "AND support_id='$support_id' ORDER BY support_history.date DESC";
	return db_query($sql);
}

function support_data_get_status_name($string) {
        /*
                simply return status_name from support_status
        */
        $sql="select * from support_status WHERE support_status_id='$string'";
        $result=db_query($sql);
        if ($result && db_numrows($result) > 0) {
                return db_result($result,0,'status_name');
        } else {
                return 'Error - Not Found';
        }
}

function support_data_get_category_name($string) {
        /*
                simply return the category_name from support_category
        */
        $sql="select * from support_category WHERE support_category_id='$string'";
        $result=db_query($sql);
        if ($result && db_numrows($result) > 0) {
                return db_result($result,0,'category_name');
        } else {
                return 'Error - Not Found';
        }
}

function support_data_create_message ($body,$support_id,$by) {
        /*
                handle the insertion of history for these parameters
        */

        if (user_isloggedin()) {
                $body="Logged In: YES \nuser_id=". user_getid() ."\nBrowser: ". $GLOBALS['HTTP_USER_AGENT'] ."\n\n".$body;
        } else {
                $body="Logged In: NO \nBrowser: ". $GLOBALS['HTTP_USER_AGENT'] ."\n\n".$body;
        }

        $sql="insert into support_messages(support_id,body,from_email,date) ".
                "VALUES ('$support_id','". htmlspecialchars($body). "','$by','".time()."')";
        return db_query($sql);
}

function support_data_create_history ($field_name,$old_value,$support_id) {
        /*
                handle the insertion of history for these parameters
        */
        if (!user_isloggedin()) {
                $user=100;
        } else {
                $user=user_getid();
        }

        $sql="insert into support_history(support_id,field_name,old_value,mod_by,date) ".
                "VALUES ('$support_id','$field_name','$old_value','$user','".time()."')";
        return db_query($sql);
}

function support_data_get_messages ($support_id) {
        $sql="select * ".
                "FROM support_messages ".
                "WHERE support_id='$support_id' ORDER BY date DESC";
	return db_query($sql);
}

?>
