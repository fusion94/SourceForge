<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: cal.php,v 1.8 2000/01/13 18:36:36 precision Exp $
//
// bool mycal_show_month(string day_callback, int year, int month)
// Show a calendar in month view
//                              
// This function shows a calendar, it takes the following arguments:
//   - string day_callback:
//     The name of a user function which gets called for each day of the month.
//     Can be used for example to show a day bold or plain depending whether events are stored for this day.
//     The callback function must return the day as integer and take three arguments: int year, int month and int day.
//   - int year:
//     Year to show.
//   - int month
//     Month to show.
//

function mycal_show_month($day_callback, $year = 0, $month = 0) {
        // set up default value for month (current month) if none was specified
        if($month == 0) {
                $month = date("m");
        }

        // setup default value for year (current year) if none was specified
        if($year == 0) {
                $year = date("Y");
        }
                                  
        $prev_month = date("m", mktime(0, 0, 0, $month - 1, 1, $year));
        $next_month = date("m", mktime(0, 0, 0, $month + 1, 1, $year));

        $prev_year = date("Y", mktime(0, 0, 0, $month + 1, 1, $year));
        $next_year = date("Y", mktime(0, 0, 0, $month - 1, 1, $year));
        ?>

        <style TYPE="text/css">
         TD.Some {
                font-family :Tahoma, Verdana, Arial; 
                font-size :12px; 
                color :#000000; 
                font-weight :normal;
         }
         A {
                font-family :Tahoma, Verdana, Arial;
                font-size :12px;
                color :#000000;
                font-weight :normal;
                text-decoration: none;
         }
        </style>

        <table align="CENTER" border="1" cellspacing="0" cellpadding="2" bgcolor="WHITE" bordercolor="Gray">
        <tr> 
                <td> 
                <table width="140" border="0" cellpadding="1" cellspacing="0" bgcolor="#FFFFFF">
                <tr height="18" bgcolor="Silver"> 
                <td width="20" height="18" align="RIGHT" valign="MIDDLE"> <a href="<?php print("$PHP_SELF?month=$prev_month&year=$prev_year"); ?>" class="noevent">&lt;</a> </td>
                <td width="120" colspan="5" align="CENTER" valign="MIDDLE" class="SOME"> 
                <?php print(date("F", mktime(0, 0, 0, $month, 1, $year))." $year"); ?> </td>
                <td width="20" height="18" align="RIGHT" valign="MIDDLE"> <a href="<?php print("$PHP_SELF?month=$next_month&year=$next_year"); ?>" class="noevent">&gt;</a> </td>
        </tr>
        <tr> 
                <td align="RIGHT" class="SOME" width="20" height="15">S</td>
                <td align="RIGHT" class="SOME" width="20" height="15">M</td>
                <td align="RIGHT" class="SOME" width="20" height="15">T</td>
                <td align="RIGHT" class="SOME" width="20" height="15">W</td>
                <td align="RIGHT" class="SOME" width="20" height="15">T</td>
                <td align="RIGHT" class="SOME" width="20" height="15">F</td>
                <td align="RIGHT" class="SOME" width="20" height="15">S</td>
        </tr>
        <tr> 
                <td height="1" align="MIDDLE" colspan="7"> 
                <hr size="1" noshade>
                </td>
        </tr>
        
	<?php
	$day = 1 - date("w", mktime(0, 0, 0, $month, 1, $year));
	$days_in_month = date("t", mktime(0, 0, 0, $month, 1, $year));

		// Loop through all days in the month
		while($day < $days_in_month) {
		print("<tr>");
        
		// Print a row containing seven days
		for ($j=1; $j<=7; $j++) {
			print("<td align='right' width=20 height=15 valign='bottom' class='some'>\n");
			print(($day <= $days_in_month && $day > 0) ? $day_callback($year, $month, $day):  "&nbsp;");
			print("</td>\n");
			$day++;
		}
		print("</tr>");
	}
	print("</table>\n </td>\n </tr>\n </table>\n");
	return(true);
}

// Put whatever you want here in this function all it does now it print plain text
// date information
function mycal_show_month_day ($year, $month, $day) {
	print("$day");
}

mycal_show_month("mycal_show_month_day", $year, $month);
?>
