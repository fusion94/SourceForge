<?php
$ERROR_IS_ERROR=false;
$ERROR_STRING='';

function error_get_string() {
	global $ERROR_STRING;
	return $ERROR_STRING;
}

function error_set_string($string) {
	global $ERROR_STRING;
	if (strlen($ERROR_STRING) > 0) {
		//if there's already a string, add to it, delimiting it
		$ERROR_STRING .= ' || '.$string;
	} else {
		$ERROR_STRING=$string;
	}
}

function error_set_true() {
	global $ERROR_IS_ERROR;
	$ERROR_IS_ERROR=true;
}

function error_set_false() {
	global $ERROR_IS_ERROR;
	$ERROR_IS_ERROR=false;
}

function error_is_error() {
	global $ERROR_IS_ERROR;
	return $ERROR_IS_ERROR;
}

?>
