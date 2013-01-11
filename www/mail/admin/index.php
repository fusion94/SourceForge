<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.21 2000/01/13 18:36:35 precision Exp $

require('pre.php');
require('../mail_utils.php');

if ($group_id && user_ismember($group_id,'A')) {

	if ($post_changes) {
		/*
			Update the DB to reflect the changes
		*/

		if ($add_list) {
			$list_password = substr(md5($GLOBALS[session_hash] . time() . rand(0,40000)),0,16);

			$new_list_name=strtolower(group_getunixname($group_id).'-'.$list_name);

			$result=db_query("SELECT * FROM mail_group_list WHERE lower(list_name)='$new_list_name'");

			if (db_numrows($result) > 0) {

				$feedback .= " ERROR - List Already Exists ";

			} else {
				$sql = "INSERT INTO mail_group_list "
					. "(group_id,list_name,is_public,password,list_admin,status) VALUES ("
					. "$group_id,"
					. "'$new_list_name',"
					. "'$is_public',"
					. "'$list_password',"
					. "'".user_getid()."',"
					. "1)";

				$result=db_query($sql);
				if (!$result) {
					$feedback .= " Error Adding List ";
				} else {
					$feedback .= " List Added ";
				}
			
				// get email addr
				$res_email = db_query("SELECT email FROM user WHERE user_id='".user_getid()."'");
				if (db_numrows($res_email) < 1) exit_error("Invalid userid","Does not compute.");
				$row_email = db_fetch_array($res_email);

				// mail password to admin
				$message = "A mailing list will be created on SourceForge in 6-24 hours \n"
					. "and you are the list administrator.\n\n"
					. "This list is: $new_list_name@lists.sourceforge.net\n\n"
					. "Your mailing list info is at:\n"
					. "http://mail1.sourceforge.net/mailman/listinfo/$new_list_name\n\n"
					. "List administration can be found at:\n"
					. "http://mail1.sourceforge.net/mailman/admin/$new_list_name\n\n"
					. "Your list password is: $list_password\n"
					. "You are encouraged to change this password as soon as possible.\n\n"
					. "Thank you for registering your project with SourceForge.\n\n"
					. " -- the SourceForge staff\n";

				mail ($row_email[email],"SourceForge New Mailing List",$message,"From: admin@sourceforge.net");
 
				$feedback .= " Email sent with details to: $row_email[email] ";
			}

		} else if ($change_status) {
			/*
				Change a forum to public/private
			*/
			$sql="UPDATE mail_group_list SET is_public='$is_public' ".
				"WHERE group_list_id='$group_list_id' AND group_id='$group_id'";
			$result=db_query($sql);
			if (!$result || db_affected_rows($result) < 1) {
				$feedback .= " Error Updating Status ";
			} else {
				$feedback .= " Status Updated Successfully ";
			}
		}

	} 

	if ($add_list) {
		/*
			Show the form for adding forums
		*/
		mail_header(array('title'=>'Add a Mailing List'));

		echo '<H2>Add a Mailing List</H2>
			<P>Lists are named in this manner: 
			<BR><B>projectname-listname@lists.sourceforge.net</B>
			<P>It will take <B><FONT COLOR="RED">6-24 Hours</FONT></B> for your list 
			to be created.
			<P>';
		$result=db_query("SELECT list_name FROM mail_group_list WHERE group_id='$group_id'");
		ShowResultSet($result,'Existing Mailing Lists');

		echo 	'<P>
			<FORM METHOD="POST" ACTION="'.$PHP_SELF.'">
			<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
			<INPUT TYPE="HIDDEN" NAME="add_list" VALUE="y">
			<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
			<B>Mailing List Name:</B><BR>
			<B>'.group_getunixname($group_id).'-<INPUT TYPE="TEXT" NAME="list_name" VALUE="" SIZE="10" MAXLENGTH="12">@lists.sourceforge.net</B><BR>
			<P>
			<B>Is Public?</B><BR>
			<INPUT TYPE="RADIO" NAME="is_public" VALUE="1" CHECKED> Yes<BR>
			<INPUT TYPE="RADIO" NAME="is_public" VALUE="0"> No<P>
			<P>
			<B><FONT COLOR="RED">Once created, this list will ALWAYS be attached to your project 
			and cannot be deleted!</FONT></B>
			<P>
			<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Add This List">
			</FORM>';

		mail_footer(array());

	} else if ($change_status) {
		/*
			Change a forum to public/private
		*/
		mail_header(array('title'=>'Change Mailing List Status'));

		$sql="SELECT list_name,group_list_id,is_public ".
			"FROM mail_group_list ".
			"WHERE group_id='$group_id'";
		$result=db_query($sql);
		$rows=db_numrows($result);

		if (!$result || $rows < 1) {
			echo '
				<H2>No Lists Found</H2>
				<P>
				None found for this project';
			echo db_error();
		} else {
			echo '
				<H2>Update Mailing List Status</H2>
				<P>
				You can make mailing lists private from here. Please note that private lists
				can still be viewed by members of your project, not the general public.<P>';

			echo '<TABLE BORDER="0">
				<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
				<TD><FONT COLOR="#FFFFFF"><B>List</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Status</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Update</TD></TR>';

			for ($i=0; $i<$rows; $i++) {
				if ($i % 2 != 0) {
					$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
				} else {
					$row_color=' BGCOLOR="#FFFFFF"';
				}

				echo '
					<TR'.$row_color.'><TD>'.db_result($result,$i,'list_name').'</TD>';
				echo '
					<FORM ACTION="'.$PHP_SELF.'" METHOD="POST">
					<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
					<INPUT TYPE="HIDDEN" NAME="change_status" VALUE="y">
					<INPUT TYPE="HIDDEN" NAME="group_list_id" VALUE="'.db_result($result,$i,'group_list_id').'">
					<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
					<TD>
						<FONT SIZE="-1">
						<B>Is Public?</B><BR>
						<INPUT TYPE="RADIO" NAME="is_public" VALUE="1"'.((db_result($result,$i,'is_public')=='1')?' CHECKED':'').'> Yes<BR>
						<INPUT TYPE="RADIO" NAME="is_public" VALUE="0"'.((db_result($result,$i,'is_public')=='0')?' CHECKED':'').'> No
					</TD><TD>
						<FONT SIZE="-1">
						<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Update Status">
					</TD></TR></FORM>';
			}
			echo '</TABLE>';
		}

		mail_footer(array());


	} else {
		/*
			Show main page for choosing 
			either moderotor or delete
		*/
		mail_header(array('title'=>'Mailing List Administration'));

		echo "\n<H2>Mailing List Administration</H2>";
		echo "\n<P>";
		echo "\n<A HREF=\"$PHP_SELF?group_id=$group_id&add_list=1\">Add Mailing List</A><BR>";
		echo "\n<A HREF=\"$PHP_SELF?group_id=$group_id&change_status=1\">Set Public/Private</A>";
		mail_footer(array());
	}

} else {
	/*
		Not logged in or insufficient privileges
	*/
	if (!$group_id) {
		exit_no_group();
	} else {
		exit_permission_denied();
	}
}
?>
