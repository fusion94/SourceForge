<?php

function portal_get_project_ids ($portal_id) {
	//return an array of group_id's in this project
	$sql="SELECT group_id FROM portal_projects WHERE portal_id='$portal_id' ORDER BY rank ASC";
	$result=db_query($sql);
	return util_result_column_to_array($result);
}

function portal_get_name ($portal_id) {
	return group_getname($portal_id);
}


?>
