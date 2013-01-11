<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: navbar.php,v 1.3 2000/01/13 18:36:35 precision Exp $

      ////////////////////////////////////
      //                                //  
      //      VA Linux Menu Server      //
      //       http://valinux.com       //
      //                                //
      // James Byers - jbyers@linux.com //
      //   C. 1999 VA Linux Systems     //
      //                                //
		// Hacked beyond belief for Alexandria
      //////////////////////////////////// 

// note on variable naming convention:
//   form variables start with "_"
//   cookie variables start with "__"
//   internal variables are typed in Hungarian notation

// MenuTable
//   Returns a string formatted as an HTML table:
//
//     <table cellpadding=0 cellspacing=0 border=0>
//     <tr><td>
//     <a href='http://url1.com'><img border=0 src='image1.gif' alt='alt1'></a>
//     </td></tr>
//     <tr><td>
//     <a href='http://url2.com'><img border=0 src='image2.gif' alt='alt2'></a>
//     </td></tr>
//     </table>
//
function MenuTable($strMenuName, $strMenuSubset = "", $strIPAddress = "") {

	// offload non-formatting work to MenuString
	//
	$aMenuResult = split("\n", MenuString($strMenuName, $strMenuSubset, $strIPAddress));

	print("<table cellpadding=0 cellspacing=0 border=0 width=110>\n");
	
	// loop across array, accounting for trailing "\n"
	//
	for ($i = 0; $i < sizeof($aMenuResult) - 1; $i += 4) {
		// variable vspace; 3 if main type, 1 if sub type
		($aMenuResult[$i+2] == 1) ? $nVSpace = 3 : $nVSpace = 1; 

		// test for move from type 1->2 for submenu table start or 2->1 for submenu table end
		//
		
		// make destination url
		$menu_url = $aMenuResult[$i];
		if ($GLOBALS[session_useparseurl])
		{
			if (ereg("\?",$menu_url,$ereg_matches))
			{ $menu_url .= "&"; }
			else
			{ $menu_url .= "?"; }
			$menu_url .= "session_hash=$GLOBALS[session_hash]";
		}
		
		if ($aMenuResult[$i+2] == 1) 
			printf("<tr><td colspan=3 width=110 align='left'>\n");
		else 
			printf("<tr><td width=107 align='left'>\n");

		printf("<a href=\"%s\">",$menu_url);
		html_image("navbar/" . $aMenuResult[$i+1],
			array(align=>"left",vspace=>$nVSpace,alt=>$aMenuResult[$i+3]));
		print("</a>");
		
		if ($aMenuResult[$i+2] == 1) 
			printf("</td></tr>\n");
		else 
			printf("</td>\n<td bgcolor='white' width=1><img src='/images/clear.gif' width=1 height=1>\n</td><td width=2><img src='/images/clear.gif' width=2 height=1>\n</td></tr>\n");

		$nLastButtonType = $aMenuResult[$i+2];
	}
	print("</table>\n");

}

function MenuString($strMenuName, $strMenuSubset = "", $strIPAddress = "") {

	// set group list
	$strGroupList = $GLOBALS[CUSTOMERS]->type;

	$dbResult = mysql_query("SELECT buttons.file AS file, buttons.alt AS alt, buttons.url AS url, buttons.tid as tid, buttons.security_ip as securityip, buttons.security_user as securityuser FROM menus, buttons WHERE menus.name='$strMenuName' AND buttons.mid=menus.mid AND buttons.active=1 AND (buttons.subset = '$strMenuSubset' OR buttons.subset = '') ORDER BY buttons.sequence", $GLOBALS[DB]);

	while($dbRow = mysql_fetch_array($dbResult)) {
		// test for restrictions; append if none
		//
		if (empty($dbRow["securityuser"]) && empty($dbRow["securityip"])) {
			// append data to result string
			//
			$strReturn = $strReturn . $dbRow["url"] . "\n" . $dbRow["file"] . "\n" . $dbRow["tid"] . "\n" . $dbRow["alt"] . "\n";
		} else {
			// set status variables: -1 iff not tested; 0 if tested and failed, 1 if tested and succeeded
			//
			$nValidIP = -1;
			$nValidGroup = -1;

			// verify against group list
			//
			if (!empty($dbRow["securityuser"]) && !empty($strGroupList)) {

				$nValidGroup = 0;

				// on second thought, DONT up-case group strings; grouping is case-sensitive
				//
				//$dbRow["securityuser"] = strtoupper($dbRow["securityuser"]);
				//$strGroupList = strtoupper($strGroupList);

				// search for first instance where a character in strGroupList is contained in dbRow[...]
				//
				for ($i = 0; $i < strlen($strGroupList); $i++) {
					// test for string match; append " " so strrpos is always greater than 0
					//
					if (strrpos(" " . $dbRow["securityuser"], substr($strGroupList, $i, 1)) != 0) {
						$nValidGroup = 1;
						break;
					}
				}
			}

			// verify against IP address
			//
			if (!empty($dbRow["securityip"]) && !empty($strIPAddress)) {
		
				$nValidIP = 0;

				// split list of IP addresses by "," delimiter
				//
				$aIPRanges = explode(",", $dbRow["securityip"]);
				for ($i = 0; $i < sizeof($aIPRanges); $i++) {
					// clean and split individual IPs by "." delimiter
					//
					$aIPRef = explode(".", chop(trim($aIPRanges[$i])));
					$aIPTest = explode(".", chop(trim($strIPAddress)));
					for ($j = 0; $j < 4; $j++) {
						if ($aIPRef[$j] == "*") {
							$nValidIP = 1; 
							break;
						} else if ($aIPRef[$j] == $aIPTest[$j]) {
							continue; 
						} else {
							$nValidIP = 0;
							break;
						}
					}
				}
			}

			// test status variables and append if authorized
			//
			if ((($nValidGroup == 1) && ($nValidIP == 1)) || (($nValidGroup == 1) && ($nValidIP == -1)) || (($nValidGroup == -1) && ($nValidIP == 1))) {
				// append data to result string
				//
				$strReturn = $strReturn . $dbRow["url"] . "\n" . $dbRow["file"] . "\n" . $dbRow["tid"] . "\n" . $dbRow["alt"] . "\n";
			}
		} // else
	} // while

	return $strReturn;
}

?>
