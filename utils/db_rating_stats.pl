#!/usr/bin/perl
#
# $Id: db_rating_stats.pl,v 1.7 2000/03/08 15:19:20 tperdue Exp $
#
use DBI;

require("include.pl");  # Include all the predefined functions

&db_connect;

#
#    aggregate the ratings
#

# create a temp table
my $query = "CREATE TABLE survey_rating_aggregate2 (type int not null,id int not null,response float not null,count int not null)";
my $rel = $dbh->prepare($query);
$rel->execute();

# insert into the temp table
$query = "INSERT INTO survey_rating_aggregate2 SELECT type,id,avg(response),count(*) FROM survey_rating_response GROUP BY type,id;";
$rel = $dbh->prepare($query);
$rel->execute();

# drop the old table

$query = "DROP TABLE survey_rating_aggregate;";
$rel = $dbh->prepare($query);
$rel->execute();

# rename the table

$query = "ALTER TABLE survey_rating_aggregate2 RENAME AS survey_rating_aggregate;";
$rel = $dbh->prepare($query);
$rel->execute();

# create an index

$query = "CREATE INDEX idx_survey_rating_aggregate_type_id ON survey_rating_aggregate(type,id);";
$rel = $dbh->prepare($query);
$rel->execute();

#
#    get the forum total message count
#

# create a temp table
my $query = "CREATE TABLE forum_agg_msg_count2 (group_forum_id int not null primary key, count int not null)";
my $rel = $dbh->prepare($query);
$rel->execute();
#
# insert into the temp table
#
$query = "INSERT INTO forum_agg_msg_count2 SELECT group_forum_id,count(*) FROM forum GROUP BY group_forum_id;";
$rel = $dbh->prepare($query);
$rel->execute();
#
# drop the old table
#
$query = "DROP TABLE forum_agg_msg_count2;";
$rel = $dbh->prepare($query);
$rel->execute();
#
# rename the table
#
$query = "ALTER TABLE forum_agg_msg_count2 RENAME AS forum_agg_msg_count;";
$rel = $dbh->prepare($query);
$rel->execute();

