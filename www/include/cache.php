<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: cache.php,v 1.31 2000/01/13 18:36:35 precision Exp $


// #################################### function cache_display

function cache_display($name,$function,$time) {
	$filename = "/sfcache/sfcache_$name.sf";

	if (!file_exists($filename) || ((time() - filectime($filename)) > $time)) {
		// file is non-existant or expired, must redo
		clearstatcache();
		if (!file_exists($filename)) {
			@touch($filename);
		}

		if (!$rfh=@fopen($filename,"r"))  { // bad cache dir?
			return cache_get_new_data($function);
		}
		if (!flock($rfh,1+4)) { // non-blocking read lock
			return cache_get_new_data($function); // another writer has an exclusive lock already
		}
		if(!flock($rfh,2)) { // upgrade to exclusive lock and block for it
			return cache_get_new_data($function);
		}

		// and write to file
		if (!$fhandle = fopen($filename,'w')) {
			flock($rfh,3); //release lock
			fclose($rfh);
			return cache_get_new_data($function);
		}
		$return=cache_get_new_data($function);
		fwrite($fhandle,$return); //write the file
		fclose($fhandle); //close the file
		flock($rfh,3); //release lock
		fclose($rfh); //close the lock
		return $return;
	} else {
		// file is good, use it for return value
		if (!$rfh = fopen($filename,'r')) { //bad filename
			return cache_get_new_data($function);
		}
		while(!flock($rfh,1+4)) { // obtained non blocking shared lock 
			usleep(250000); // wait 0.25 seconds for the lock to become available
		}
		$result=stripslashes(fread($rfh,200000));
		flock($rfh,3); // cancel read lock
		fclose($rfh);
		return $result;
	}
}

function cache_get_new_data($function) {
	$furl=fopen("http://localhost/write_cache.php?function=".urlencode($function),'r');
	return stripslashes(fread($furl,200000));
}
?>
