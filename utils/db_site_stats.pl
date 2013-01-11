#!/usr/bin/perl
#
# $Id: db_site_stats.pl,v 1.8 2000/05/03 10:22:44 tperdue Exp $
#

$yesterday = time() - 86400;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($yesterday);

if ($mday < 10) {
    $mday = "0$mday";
}
$month = ($mon + 1);

if ($month < 10) {
    $month = "0$month";
}

#good until the year 2099, then we're in trouble....
if ($year < 99) {
        $year = ($year + 2000);
} else {
        $year = ($year + 1900);
}

$yesterday_formatted = $year . $month . $mday;

#print $yesterday_formatted;

use DBI;

require("include.pl");  # Include all the predefined functions

&db_connect;

#
#   start to aggregate the stats reports
#

#logo showings by day

$rel = $dbh->prepare("DELETE FROM stats_agg_logo_by_day WHERE day='$yesterday_formatted';");
$rel->execute();

$query="INSERT INTO stats_agg_logo_by_day SELECT day, count(*) FROM activity_log WHERE type=1 AND day='$yesterday_formatted' GROUP BY day;";
$rel = $dbh->prepare($query);
$rel->execute();

#logo showings by group

$rel = $dbh->prepare("DELETE FROM stats_agg_logo_by_group WHERE day='$yesterday_formatted';");
$rel->execute();

$query="INSERT INTO stats_agg_logo_by_group SELECT day,group_id,count(*) FROM activity_log WHERE type=1 AND day='$yesterday_formatted' GROUP BY day,group_id;";
$rel = $dbh->prepare($query);
$rel->execute();

#page views by day

$rel = $dbh->prepare("DELETE FROM stats_agg_pages_by_day WHERE day='$yesterday_formatted';");
$rel->execute();

$query="INSERT INTO stats_agg_pages_by_day SELECT day, count(*) FROM activity_log WHERE type=0 AND day='$yesterday_formatted' GROUP BY day;";
$rel = $dbh->prepare($query);
$rel->execute();

$sql="DROP TABLE IF EXISTS activity_log_old";
$rel = $dbh->prepare($sql);
$rel->execute();

$sql="ALTER TABLE activity_log RENAME AS activity_log_old";
$rel = $dbh->prepare($sql);
$rel->execute();

$sql="CREATE TABLE activity_log (
  day int(11) DEFAULT '0' NOT NULL,
  hour int(11) DEFAULT '0' NOT NULL,
  group_id int(11) DEFAULT '0' NOT NULL,
  browser varchar(8) DEFAULT 'OTHER' NOT NULL,
  ver float(10,2) DEFAULT '0.00' NOT NULL,
  platform varchar(8) DEFAULT 'OTHER' NOT NULL,
  time int(11) DEFAULT '0' NOT NULL,
  page text,
  type int(11) DEFAULT '0' NOT NULL,
  KEY idx_activity_log_day (day),
  KEY idx_activity_log_group (group_id),
  KEY type_idx (type)
)";
$rel = $dbh->prepare($sql);
$rel->execute();

$today = time();

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($today);

if ($mday < 10) {
    $mday = "0$mday";
}
$month = ($mon + 1);

if ($month < 10) {
    $month = "0$month";
}

#good until the year 2099, then we're in trouble....
if ($year < 99) {
        $year = ($year + 2000);
} else {
        $year = ($year + 1900);
}

$today_formatted = $year . $month . $mday;

$sql="INSERT INTO activity_log SELECT * FROM activity_log_old WHERE day='$today_formatted'";
$rel = $dbh->prepare($sql);
$rel->execute();

$sql="DELETE FROM activity_log_old WHERE day='$today_formatted'";
$rel = $dbh->prepare($sql);
$rel->execute();
