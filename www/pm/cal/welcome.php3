<?
	include ("header.php3");
	include ("config.php3");

	$currentday = date("j", time());
	$currentmonth = date("m", time());
	$currentyear = date("Y", time());

	$lastday = 1;
	$database = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);

	if (!$month)
	{
		$month = date("m", time());
		$year = date("Y", time());
	}
	

	mysql_select_db($mysql_database, $database);

	echo "<center><font face=Arial size=5><b>";
	echo date('F', mktime(0,0,0,$month,1,$year));
	echo " $year<p><font size=3></b><p>";
	
	$firstday = date( 'w', mktime(0,0,0,$month,1,$year));
	while (checkdate($month,$lastday,$year))
	{
	        $lastday++;
	}      
	
	$nextmonth = $month+1;
	$nextyear = $year;
	if ($nextmonth == 13)
	{
		$nextmonth = 1;
		$nextyear = $year + 1;
	}
	$lastmonth = $month-1;
	$lastyear = $year;
	if ($lastmonth == 0)
	{
		$lastmonth = 12;
		$lastyear = $year-1;
	}

	echo "<table><tr>";     
        echo "<td><form method=post action=welcome.php3><input type=submit value='<<'>
                <input type=hidden name=month value=$lastmonth>
                <input type=hidden name=year value=$lastyear></form></td>";
        echo "<td><form method=post action=operate.php3>
                <input type=submit name=action value=\"Add item to calendar\">
                <input type=hidden name=month value=$month>
                <input type=hidden name=year value=$year>  
                </form></td>";
        echo "<td><form method=post action=welcome.php3><input type=submit value='>>'>
                <input type=hidden name=month value=$nextmonth> 
                <input type=hidden name=year value=$nextyear></form></td></tr></table>";


	echo "<table width=400 cellpadding=5 cellspacing=5 border=1>";
	echo "<tr><td width=14%><b>Sunday</td><td width=14%><b>Monday</td>
		  <td width=14%><b>Tuesday</td><td width=14%><b>Wednesday</td>
		  <td width=14%><b>Thursday</td><td width=16%><b>Friday</td>
		  <td width=14%><b>Saturday</td></tr>";

	for ($i=0; $i<7; $i++)
	{
		if ($i < $firstday)
		{
			echo "<td></td>";
		}
		else
		{
			$thisday = ($i+1)-$firstday;

			if ($currentyear > $year)
			{
				echo "<td valign=top bgcolor=dddddd>";
			}
			else if ($currentmonth > $month && $currentyear == $year)
			{
				echo "<td valign=top bgcolor=dddddd>";
			}
			else if ($currentmonth == $month && $currentday > $thisday && $currentyear == $year)
			{
				echo "<td valign=top bgcolor=dddddd>";
			}
			else
			{
				echo "<td valign=top bgcolor=white>";
			}
			echo "<a href=display.php3?day=$thisday&month=$month&year=$year>$thisday</a><br><hr>
				<font size=2>";
			$query2 = mysql_query("SELECT subject FROM $mysql_tablename WHERE stamp >= \"$year-$month-$thisday 00:00:00\" and stamp <= \"$year-$month-$thisday 23:59:59\" ORDER BY stamp");
			for ($j = 0; $j<mysql_num_rows($query2); $j++)
			{
				$results = mysql_fetch_array($query2);
				if ($results["subject"])
				{
					echo "$results[subject]<br><hr>";
				}
			}
			if (mysql_num_rows($query2) < 4)
			{
				for ($j=0; $j<(4-mysql_num_rows($query2)); $j++)
					echo "<br>";
			}
			echo "</td>";
		}
	}

	echo "</tr>\n";
	$nextday = ($i+1)-$firstday;

	for ($j = 0; $j<5; $j++)
	{
		echo "<tr>";
		for ($k = 0; $k<7; $k++)
		{
			if ($nextday < $lastday)
			{
				if ($currentyear > $year)
                                {       
                                        echo "<td valign=top bgcolor=dddddd>";
                                }
				else if ($currentmonth > $month && $currentyear == $year)
                        	{       
                               		echo "<td valign=top bgcolor=dddddd>";
                        	}               
                        	else if ($currentmonth == $month && $currentday > $nextday && $currentyear == $year)
                        	{
                                	echo "<td valign=top bgcolor=dddddd>";
                        	}
                        	else    
                        	{
                               		echo "<td valign=top bgcolor=white>";
                        	}
				echo "<a href=display.php3?day=$nextday&month=$month&year=$year>$nextday</a><br><hr>
					<font size=2>";
				$query3 = mysql_query("SELECT subject FROM $mysql_tablename WHERE stamp >= \"$year-$month-$nextday 00:00:00\" AND stamp <= \"$year-$month-$nextday 23:59:59\" ORDER BY stamp");
				for ($i = 0; $i<mysql_num_rows($query3)+4; $i++)
				{
					$results2 = mysql_fetch_array($query3);
					if ($results2["subject"])
					{
						echo "$results2[subject]<br><hr>";
					}
					else if ($i < 4)
					{
						echo "<br>";
					}
				}
				echo "</td>";
				$nextday++;
			}
		}
		echo "</tr>\n";
	}

	echo "</table><font size=3>";


	echo "<table><tr>";
	echo "<td><form method=post action=welcome.php3><input type=submit value='<<'>
		<input type=hidden name=month value=$lastmonth>
		<input type=hidden name=year value=$lastyear></form></td>";
	echo "<td><form method=post action=operate.php3>
		<input type=submit name=action value=\"Add item to calendar\">
		<input type=hidden name=month value=$month>
		<input type=hidden name=year value=$year>
		</form></td>";
	echo "<td><form method=post action=welcome.php3><input type=submit value='>>'>
		<input type=hidden name=month value=$nextmonth>
		<input type=hidden name=year value=$nextyear></form></td></tr></table>";

?>
