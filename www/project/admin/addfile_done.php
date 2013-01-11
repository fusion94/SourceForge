<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: addfile_done.php,v 1.62 2000/07/12 21:01:41 tperdue Exp $

require "pre.php";    
require "filechecks.php";
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));
// error checking

if (!$form_release_version) exit_error("Submit Error","You must enter a release version number.");
if (!$form_filemodule) exit_error("Submit Error","You must define and select a filemodule.");
if (!filechecks_islegalname($form_filename)) exit_error("Illegal Filename","Illegal Filename");

// date field
if (!$form_release_time) {
	$unix_release_time = time();
} elseif (!ereg("[0-9]{4}-[0-9]{2}-[0-9]{2}",$form_release_time)) {
	exit_error("Invalid Date Format","Date entry could not be parsed.");
} else { //is valid date... parse it
	$date_list = split("-",$form_release_time,3);
	$unix_release_time = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
}

// make sure not submitting files in the future

if ($unix_release_time > time()) {
	$unix_release_time = time();
}


// check and see if any releases have been made in
// this filemodule in the last 6 hours.

$then=(time()-21600);
$result=db_query("SELECT * FROM filerelease WHERE filemodule_id='$form_filemodule' AND post_time > '$then'");


// add to filerelease

db_query("INSERT INTO filerelease (group_id,user_id,unix_box,unix_partition,text_notes,text_changes,"
	. "release_version,filename,filemodule_id,file_type,release_time,file_size,post_time,text_format,status) VALUES "
	. "($group_id," . user_getid() . ",'remission',3,'$form_text_notes','$form_text_changes',"
	. "'$form_release_version','$form_filename',$form_filemodule,'$form_filetype',$unix_release_time,"
	. "$form_filesize," . time() . ",'$form_text_format','N')");



// make this the most recent release in module

db_query("UPDATE filemodule SET recent_filerelease='$form_release_version' WHERE "
	. "filemodule_id=$form_filemodule");

// check and see if any releases have been made in 
// this filemodule in the last 6 hours. If not, send an email
// to everyone monitoring the file.

if ($result && db_numrows($result) < 1) {
	//get the emails of those who are monitoring this filemodule
	$sql="SELECT user.email,filemodule.module_name,groups.unix_group_name ".
		"FROM user,filemodule_monitor,filemodule,groups ".
		"WHERE user.user_id=filemodule_monitor.user_id ".
		"AND groups.group_id=filemodule.group_id ".
		"AND filemodule_monitor.filemodule_id=filemodule.filemodule_id ".
		"AND filemodule_monitor.filemodule_id='$form_filemodule'";

	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		//send the email
		$array_emails=result_column_to_array($result);
		$list=implode($array_emails,', ');

// http://download.sourceforge.net/unix_group_name/filename

		$subject='SourceForge File Release Notice';

		$body = "To: noreply@$GLOBALS[HTTP_HOST]".
			"\nBCC: $list".
			"\nSubject: $subject".
			"\n\nA new version of ". db_result($result,0,'module_name')." has been released. ".
			"\nYou can download it from SourceForge by following this link: ".
			"\n\nhttp://".$GLOBALS['sys_download_host']."/".db_result($result,0,'unix_group_name')."/$form_filename ".
			"\n\nYou requested to be notified when new versions of this file ".
			"\nwere released. If you don't wish to be notified in the ".
			"\nfuture, please login to SourceForge and click this link: ".
			"\nhttp://$GLOBALS[HTTP_HOST]/project/filemodule_monitor.php?filemodule_id=$form_filemodule ";

		exec ("/bin/echo \"$body\" | /usr/sbin/sendmail -fnoreply@$GLOBALS[HTTP_HOST] -t");
		$feedback .= ' email sent - users tracking ';
	} else {
		echo db_error();
		$feedback .= ' email not sent - no users tracking ';
	}
} else {
	$feedback .= ' email not sent - '.db_numrows($result).' release(s) in last 6 hours ';
}

project_admin_header(array('title'=>'File Release Confirmation','group'=>$group_id));
?>

<P><B>Success!</B>

<P>This file has been successfully added to the SourceForge repository. It will be available
for download within 120 seconds.

<P><A href="/project/?group_id=<?php print $group_id; ?>">[Return to Project Page]</A>
<BR><A href="/project/admin/?group_id=<?php print $group_id; ?>">[Return to Project Administration]</A>

<?php
project_admin_footer(array());
?>
