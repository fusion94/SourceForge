<?php

function db_connect ($target) {
	global $sys_dbhost,$sys_dbname,$sys_dbuser,$sys_dbpasswd,$conn;
	$conn = @pg_pconnect ("user=$sys_dbuser password=$sys_dbpasswd dbname=$sys_dbname ");
}


function db_query ($qstring) {
	global $conn, $pg_curr_row;
	$result = @pg_Exec ($conn, "$qstring");
	$pg_curr_row[$result] = 0;
	return $result;
}


function db_numrows ($qhandle) {
	return @pg_NumRows ($qhandle);
}


function db_result ($qhandle, $row, $field) {
	return @pg_Result ($qhandle, $row, $field);
}


function db_numfields ($lhandle) {
	return @pg_NumFields ($lhandle);
}


function db_fieldname ($lhandle, $fnumber) {
	return @pg_FieldName ($lhandle, $fnumber);
}


function db_affected_rows ($qhandle) {
	return @pg_cmdTuples ($qhandle);
}

function db_fetch_array($qhandle) {
	global $pg_curr_row;
	$row = @pg_Fetch_Array ($qhandle, $pg_curr_row[$qhandle]);
	$pg_curr_row[$qhandle]++;
	return $row;
}
	
function db_insertid ($r) {
	global $conn;

	$oid = pg_GetLastOID ($r);

	# Search the system catalogues to find all the tables.
	$query	= "SELECT c.relname "
		. "  FROM pg_class c "
		. " WHERE c.relkind = 'r'"
		. "   AND c.relname !~ '^pg_'";

	$res1 = pg_Exec ($conn, $query);

	if (!$res1) {
		echo pg_ErrorMessage ($conn) . "\n";
	} else {
		# For each table, query to see if the OID is present.
		for ($idx = 0; $idx < pg_NumRows ($res1); $idx++) {
			$row   = pg_Fetch_Row ($res1, $idx);
			$table = $row[0];
			$res2  = pg_Exec ($conn, "select oid from $table where oid = $oid");
			if (pg_NumRows ($res2) != 0) {
				# We found the OID in the current table, now find the name of the primary key column
				$query  = "SELECT a.attname FROM pg_class c, pg_attribute a, pg_index i, pg_class c2 "
					. " WHERE c.relname   = '$table' "
					. "   AND i.indrelid  = c.oid "
					. "   AND a.attrelid  = c.oid "
					. "   AND c2.oid      = i.indexrelid "
					. "   AND i.indkey[0] = a.attnum "
					. "   AND c2.relname  ~ '_pkey$'";
				$res2  = pg_Exec ($conn, $query);
				if ($res2) {
					$pkname	= pg_Result ($res2, 0, 0);
					$res2	= pg_Exec ("select $pkname from $table where oid = $oid");
					$row	= pg_Fetch_Row ($res2, 0);
					return $row[0];
				} else {
					return 0;
				}
				break;
			}
		}
	}
}

function db_error() {
	global $conn;
        return @pg_errormessage($conn);
}

?>
