#
#   copy from existing database
#

SELECT * FROM users INTO OUTFILE '/tmp/db-users.sql';
SELECT * FROM forum INTO OUTFILE '/tmp/db-forum.sql';
SELECT * FROM forum_group_list INTO OUTFILE '/tmp/db-forum_group_list.sql';

#
#   SQL for mysql
#

CREATE TABLE forum (
  msg_id int DEFAULT '0' NOT NULL auto_increment,
  group_forum_id int DEFAULT '0' NOT NULL,
  posted_by int DEFAULT '0' NOT NULL,
  subject text NOT NULL,
  date int DEFAULT '0' NOT NULL,
  is_followup_to int DEFAULT '0' NOT NULL,
  thread_id int DEFAULT '0' NOT NULL,
  has_followups int DEFAULT '0',
  PRIMARY KEY (msg_id),
  KEY idx_forum_group_forum_id (group_forum_id),
  KEY idx_forum_is_followup_to (is_followup_to),
  KEY idx_forum_thread_id (thread_id)
);

CREATE TABLE forum_group_list (
  group_forum_id int DEFAULT '0' NOT NULL auto_increment,
  group_id int DEFAULT '0' NOT NULL,
  forum_name text NOT NULL,
  is_public int DEFAULT '0' NOT NULL,
  description text,
  PRIMARY KEY (group_forum_id),
  KEY idx_forum_group_list_group_id (group_id)
);

CREATE TABLE users (
  user_id int DEFAULT '0' NOT NULL auto_increment,
  user_name text NOT NULL,
  email text NOT NULL,
  user_pw varchar(32) DEFAULT '' NOT NULL,
  realname varchar(32) DEFAULT '' NOT NULL,
  status char(1) DEFAULT 'A' NOT NULL,
  shell varchar(20) DEFAULT '/bin/bash' NOT NULL,
  unix_pw varchar(40) DEFAULT '' NOT NULL,
  unix_status char(1) DEFAULT 'N' NOT NULL,
  unix_uid int DEFAULT '0' NOT NULL,
  unix_box varchar(10) DEFAULT 'shell1' NOT NULL,
  add_date int DEFAULT '0' NOT NULL,
  confirm_hash varchar(32),
  mail_siteupdates int DEFAULT '0' NOT NULL,
  mail_va int DEFAULT '0' NOT NULL,
  email_new text,
  people_view_skills int DEFAULT '0' NOT NULL,
  timezone varchar(64) DEFAULT 'GMT',
  PRIMARY KEY (user_id),
  KEY idx_user_user (status)
);


LOAD DATA INFILE '/tmp/db-users.sql' INTO TABLE users;
LOAD DATA INFILE '/tmp/db-forum.sql' INTO TABLE forum;
LOAD DATA INFILE '/tmp/db-forum_group_list.sql' INTO TABLE forum_group_list;


#
#   SQL for postgres
#


CREATE TABLE forum (
  msg_id serial,
  group_forum_id int DEFAULT '0' NOT NULL,
  posted_by int DEFAULT '0' NOT NULL,
  subject text NOT NULL,
  date int DEFAULT '0' NOT NULL,
  is_followup_to int DEFAULT '0' NOT NULL,
  thread_id int DEFAULT '0' NOT NULL,
  has_followups int DEFAULT '0',
  PRIMARY KEY (msg_id)
);

CREATE INDEX idx_forum_group_forum_id ON forum (group_forum_id);
CREATE INDEX idx_forum_is_followup_to ON forum (is_followup_to);
CREATE INDEX idx_forum_thread_id ON forum (thread_id);

CREATE TABLE forum_group_list (
  group_forum_id serial,
  group_id int DEFAULT '0' NOT NULL,
  forum_name text NOT NULL,
  is_public int DEFAULT '0' NOT NULL,
  description text,
  PRIMARY KEY (group_forum_id)
);

CREATE INDEX idx_forum_group_list_group_id ON forum_group_list (group_id);

CREATE TABLE users (
  user_id serial,
  user_name text NOT NULL,
  email text NOT NULL,
  user_pw varchar(32) DEFAULT '' NOT NULL,
  realname varchar(32) DEFAULT '' NOT NULL,
  status char(1) DEFAULT 'A' NOT NULL,
  shell varchar(20) DEFAULT '/bin/bash' NOT NULL,
  unix_pw varchar(40) DEFAULT '' NOT NULL,
  unix_status char(1) DEFAULT 'N' NOT NULL,
  unix_uid int DEFAULT '0' NOT NULL,
  unix_box varchar(10) DEFAULT 'shell1' NOT NULL,
  add_date int DEFAULT '0' NOT NULL,
  confirm_hash varchar(32),
  mail_siteupdates int DEFAULT '0' NOT NULL,
  mail_va int DEFAULT '0' NOT NULL,
  email_new text,
  people_view_skills int DEFAULT '0' NOT NULL,
  timezone varchar(64) DEFAULT 'GMT',
  PRIMARY KEY (user_id)
);

CREATE INDEX idx_user_user ON users (status);

COPY users FROM '/tmp/db-users.sql';
COPY forum FROM '/tmp/db-forum.sql';
COPY forum_group_list FROM '/tmp/db-forum_group_list.sql';




