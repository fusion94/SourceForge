<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: project_admin_utils.php,v 1.8 2000/06/16 12:58:48 tperdue Exp $

function project_admin_header($params) {
	global $DOCUMENT_ROOT,$group_id;
	$params['group']=$group_id;
	site_header($params);

	html_tabs('home',$group_id);

	echo '
		<P><B>
		<A HREF="/project/admin/?group_id='.$group_id.'">Admin</A> | 
		<A HREF="/project/admin/userperms.php?group_id='.$group_id.'">User Permissions</A> | 
		<A HREF="/project/admin/editgroupinfo.php?group_id='.$group_id.'">Edit Public Info</A> |
		<A HREF="/project/admin/filerelease-list.php?group_id='.$group_id.'">Edit File Releases</A>  
		<BR><A HREF="/project/admin/addfile.php?group_id='.$group_id.'">Release New File</A> | 
		<A HREF="/project/admin/module-add.php?group_id='.$group_id.'">Add New Module</A> |
		<A HREF="/people/createjob.php?group_id='.$group_id.'">Post Jobs</A> | 
		<A HREF="/people/?group_id='.$group_id.'">Edit Jobs</A>
		</B>
		<P>';
}

function project_admin_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

?>
