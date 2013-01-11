<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.35 2000/01/26 13:49:54 tperdue Exp $

require('pre.php');
require('../bug_utils.php');
$is_admin_page='y';

if ($group_id && (user_ismember($group_id,'B2') || user_ismember($group_id,'A'))) {

	if ($post_changes) {
		/*
			Update the database
		*/

		if ($mod_admins) {

			/*
				Post changes for admins
			*/

			$sql="SELECT user_id FROM user_group WHERE group_id='$group_id'";
			$result=db_query($sql);
			$rows=db_numrows($result);

			if ($result && $rows > 0) {
				/*
					Begin iterating and setting the values in the db
				*/
				for ($i=0; $i<$rows; $i++) {
					$user_id=db_result($result,$i,"user_id");
					eval("\$val=\"\$_$user_id\";");
					$sql="UPDATE user_group SET bug_flags='$val' WHERE user_id='$user_id' AND group_id='$group_id'";
					$result2=db_query($sql);
					if (!$result2) {
						$feedback .= " Error Updating User ID $user_id ";
					}
				}
				$feedback .= ' Members Updated ';

			} else {
				bug_header(array ('title'=>'Add/Remove Administrators'));
				echo '<H1>No members in this group</H1>';
				bug_footer(array());
				exit;
			}

		} else if ($bug_cat) {

			$sql="INSERT INTO bug_category VALUES ('', '$group_id','$cat_name')";
			$result=db_query($sql);
			if (!$result) {
				$feedback .= ' Error inserting value ';
			}

			$feedback .= ' Bug Category Inserted ';

		} else if ($bug_group) {

			$sql="INSERT INTO bug_group VALUES ('', '$group_id','$bug_group_name')";
			$result=db_query($sql);
			if (!$result) {
				$feedback .= ' Error inserting value ';
			}

			$feedback .= ' Bug Group Inserted ';

		}

	} 
	/*
		Show UI forms
	*/

	if ($mod_admins) {
		/*
			Show admin list in multiple select box
		*/
		bug_header(array ('title'=>'Add/Remove Administrators'));

		echo '<H1>Bug Administrators</H1>';
		$sql="SELECT user.user_name,user.user_id,user_group.bug_flags ".
			"FROM user,user_group WHERE user.user_id=user_group.user_id AND user_group.group_id='$group_id'";
		$result=db_query($sql);
		$rows=db_numrows($result);

		if ($result && $rows > 0) {

			echo '
				<TABLE WIDTH="100%" BORDER="0">
				<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
				<TD><FONT COLOR="#FFFFFF"><B>User Name</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Permissions</TD></TR>
				<FORM METHOD="POST" ACTION="'.$PHP_SELF.'">
				<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
				<INPUT TYPE="HIDDEN" NAME="mod_admins" VALUE="y">
				<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">';

			for ($i=0; $i<$rows; $i++) {
                        	if ($i % 2 != 0) {
                        	        $row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
                	        } else {
        	                        $row_color=' BGCOLOR="#FFFFFF"';
	                        }
				echo "\n\n<TR$row_color><TD>".db_result($result, $i, "user_name")."</TD><TD>";
				echo "\n<SELECT NAME=\"_".db_result($result, $i, "user_id")."\">";
				echo "\n<OPTION VALUE=\"0\"";
				if (db_result($result,$i,"bug_flags") == "0") { echo " SELECTED"; }
				echo ">None</OPTION>";

				echo "\n<OPTION VALUE=\"1\"";
				if (db_result($result,$i,"bug_flags") == "1") { echo " SELECTED"; }
				echo ">Bug Tech Only</OPTION>";

				echo "\n<OPTION VALUE=\"2\"";
				if (db_result($result,$i,"bug_flags") == "2") { echo " SELECTED"; }
				echo ">Bug Tech &amp; Administrator</OPTION>";

				echo "\n<OPTION VALUE=\"3\"";
				if (db_result($result,$i,"bug_flags") == "3") { echo " SELECTED"; }
				echo ">Administrator Only</OPTION>";
				echo "</SELECT></TD></TR>";

			}
			echo '
				<TR><TD COLSPAN="2" ALIGN="MIDDLE">
				<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="SUBMIT"></TD></TR>
				</FORM></TABLE>';
		} else {
			/*
				No members in this group yet
			*/
			echo '
				<H1>No members in this group</H1>';
		}	

		bug_footer(array());

	} else if ($bug_cat) {
		/*
			Show categories and blank row
		*/
		bug_header(array ('title'=>'Add/Change Categories'));

		echo "<H1>Add Bug Categories</H1>";

		/*
			List of possible categories for this group
		*/
		$sql="select bug_category_id,category_name from bug_category WHERE group_id='$group_id'";
		$result=db_query($sql);
		echo "<P>";
		if ($result && db_numrows($result) > 0) {
			ShowResultSet($result,"Existing Categories");
		} else {
			echo "\n<H1>No bug categories in this group</H1>";
		}
		?>
		<P>
		Add a new bug category:
		<P>
		<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="bug_cat" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
		<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
		<H3>New Category Name:</H3>
		<INPUT TYPE="TEXT" NAME="cat_name" VALUE="" SIZE="15" MAXLENGTH="30"><BR>
		<P>
		<B><FONT COLOR="RED">Once you add a bug category, it cannot be deleted or modified</FONT></B>
		<P>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="SUBMIT">
		</FORM>
		<?php

		bug_footer(array());

	} else if ($bug_group) {
		/*
			Show bug_groups and blank row
		*/
		bug_header(array ('title'=>'Add/Change Groups'));

		echo '<H1>Add Bug Groups</H1>';

		/*
			List of possible bug_groups for this group
		*/
		$sql="select bug_group_id,group_name from bug_group WHERE group_id='$group_id'";
		$result=db_query($sql);
		echo "<P>";
		if ($result && db_numrows($result) > 0) {
			ShowResultSet($result,"Existing Bug Groups");
		} else {
			echo "\n<H1>No bug groups in this project group</H1>";
		}
		?>
		<P>
		Add a new bug group:
		<P>
		<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="bug_group" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
		<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
		<H3>New Bug Group Name:</H3>
		<INPUT TYPE="TEXT" NAME="bug_group_name" VALUE="" SIZE="15" MAXLENGTH="30"><BR>
		<P>
		<B><FONT COLOR="RED">Once you add a bug group, it cannot be deleted or modified</FONT></B>
		<P>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="SUBMIT">
		</FORM>
		<?php

		bug_footer(array());

	} else {
		/*
			Show main page
		*/

		bug_header(array ('title'=>'Bug Administration'));

		echo '
			<H1>Bug Administration</H1>';

		echo '<P>
			<A HREF="'.$PHP_SELF.'?group_id='.$group_id.'&bug_cat=1">Add Bug Categories</A><BR>';
		echo "\nAdd categories of bugs like, 'mail module','gant chart module','interface', etc<P>";
		echo "\n<A HREF=\"$PHP_SELF?group_id=$group_id&bug_group=1\">Add Bug Groups</A><BR>";
		echo "\nAdd Groups of bugs like 'future requests','unreproducible', etc<P>";

		bug_footer(array());
	}

} else {

	//browse for group first message

	if (!$group_id) {
		exit_no_group();
	} else {
		exit_permission_denied();
	}

}
?>
