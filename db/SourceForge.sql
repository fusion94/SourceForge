#
# SourceForge: Breaking Down the Barriers to Open Source Development
# Copyright 1999-2000 (c) The SourceForge Crew
# http://sourceforge.net
#

#
# Table structure for table 'activity_log'
#
CREATE TABLE activity_log (
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
  KEY idx_activity_log_hour (hour),
  KEY idx_activity_log_group (group_id),
  KEY idx_activity_log_browser (browser),
  KEY idx_activity_log_platform (platform),
  KEY type_idx (type)
);

#
# Table structure for table 'bug'
#
CREATE TABLE bug (
  bug_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  status_id int(11) DEFAULT '0' NOT NULL,
  priority int(11) DEFAULT '0' NOT NULL,
  category_id int(11) DEFAULT '0' NOT NULL,
  submitted_by int(11) DEFAULT '0' NOT NULL,
  assigned_to int(11) DEFAULT '0' NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  summary text,
  details text,
  close_date int(11),
  bug_group_id int(11) DEFAULT '0' NOT NULL,
  resolution_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (bug_id),
  KEY idx_bug_group_id (group_id)
);

#
# Table structure for table 'bug_bug_dependencies'
#
CREATE TABLE bug_bug_dependencies (
  bug_depend_id int(11) DEFAULT '0' NOT NULL auto_increment,
  bug_id int(11) DEFAULT '0' NOT NULL,
  is_dependent_on_bug_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (bug_depend_id),
  KEY idx_bug_bug_dependencies_bug_id (bug_id),
  KEY idx_bug_bug_is_dependent_on_task_id (is_dependent_on_bug_id)
);

#
# Table structure for table 'bug_category'
#
CREATE TABLE bug_category (
  bug_category_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  category_name text,
  PRIMARY KEY (bug_category_id),
  KEY idx_bug_category_group_id (group_id)
);

#
# Table structure for table 'bug_filter'
#
CREATE TABLE bug_filter (
  filter_id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_id int(11) DEFAULT '0' NOT NULL,
  group_id int(11) DEFAULT '0' NOT NULL,
  sql_clause text NOT NULL,
  is_active int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (filter_id)
);

#
# Table structure for table 'bug_group'
#
CREATE TABLE bug_group (
  bug_group_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  group_name text NOT NULL,
  PRIMARY KEY (bug_group_id),
  KEY idx_bug_group_group_id (group_id)
);

#
# Table structure for table 'bug_history'
#
CREATE TABLE bug_history (
  bug_history_id int(11) DEFAULT '0' NOT NULL auto_increment,
  bug_id int(11) DEFAULT '0' NOT NULL,
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) DEFAULT '0' NOT NULL,
  date int(11),
  PRIMARY KEY (bug_history_id),
  KEY idx_bug_history_bug_id (bug_id)
);

#
# Table structure for table 'bug_resolution'
#
CREATE TABLE bug_resolution (
  resolution_id int(11) DEFAULT '0' NOT NULL auto_increment,
  resolution_name text NOT NULL,
  PRIMARY KEY (resolution_id)
);

#
# Table structure for table 'bug_status'
#
CREATE TABLE bug_status (
  status_id int(11) DEFAULT '0' NOT NULL auto_increment,
  status_name text,
  PRIMARY KEY (status_id)
);

#
# Table structure for table 'bug_task_dependencies'
#
CREATE TABLE bug_task_dependencies (
  bug_depend_id int(11) DEFAULT '0' NOT NULL auto_increment,
  bug_id int(11) DEFAULT '0' NOT NULL,
  is_dependent_on_task_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (bug_depend_id),
  KEY idx_bug_task_dependencies_bug_id (bug_id),
  KEY idx_bug_task_is_dependent_on_task_id (is_dependent_on_task_id)
);

#
# Table structure for table 'category'
#
CREATE TABLE category (
  category_id int(11) DEFAULT '0' NOT NULL auto_increment,
  category_name text NOT NULL,
  sub_files int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (category_id)
);

#
# Table structure for table 'category_link'
#
CREATE TABLE category_link (
  category_link_id int(11) DEFAULT '0' NOT NULL auto_increment,
  parent int(11) DEFAULT '0' NOT NULL,
  child int(11) DEFAULT '0' NOT NULL,
  primary_parent int(1) DEFAULT '1' NOT NULL,
  PRIMARY KEY (category_link_id),
  KEY idx_category_link_child (child),
  KEY idx_category_link_primary_parent (primary_parent),
  KEY idx_category_link_parent (parent)
);

#
# Table structure for table 'cvs'
#
CREATE TABLE cvs (
  cvs_id int(11) DEFAULT '0' NOT NULL auto_increment,
  cvs_server char(20) DEFAULT 'cvs1' NOT NULL,
  cvs_dir char(40) DEFAULT '' NOT NULL,
  dns_name char(60),
  group_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (cvs_id),
  KEY idx_cvs_group_id (group_id)
);

#
# Table structure for table 'filecvstar'
#
CREATE TABLE filecvstar (
  filecvstar_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  module_name varchar(30) DEFAULT '' NOT NULL,
  unix_box varchar(20) DEFAULT 'remission' NOT NULL,
  unix_partition int(11) DEFAULT '0' NOT NULL,
  timestamp int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (filecvstar_id),
  KEY idx_filecvstar_group_id (group_id)
);

#
# Table structure for table 'filedownload_log'
#
CREATE TABLE filedownload_log (
  user_id int(11) DEFAULT '0' NOT NULL,
  filerelease_id int(11) DEFAULT '0' NOT NULL,
  time int(11) DEFAULT '0' NOT NULL,
  KEY all_idx (user_id,filerelease_id),
  KEY time_idx (time),
  KEY filerelease_id_idx (filerelease_id)
);

#
# Table structure for table 'filemodule'
#
CREATE TABLE filemodule (
  filemodule_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  module_name varchar(40),
  recent_filerelease varchar(20) DEFAULT '' NOT NULL,
  PRIMARY KEY (filemodule_id),
  KEY idx_filemodule_group_id (group_id)
);

#
# Table structure for table 'filemodule_monitor'
#
CREATE TABLE filemodule_monitor (
  filemodule_id int(11) DEFAULT '0' NOT NULL,
  user_id int(11) DEFAULT '0' NOT NULL,
  KEY idx_filemodule_monitor_id (filemodule_id)
);

#
# Table structure for table 'filerelease'
#
CREATE TABLE filerelease (
  filerelease_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  user_id int(11) DEFAULT '0' NOT NULL,
  unix_box varchar(20) DEFAULT 'remission' NOT NULL,
  unix_partition int(11) DEFAULT '0' NOT NULL,
  text_notes text,
  text_changes text,
  release_version varchar(20),
  filename varchar(80),
  filemodule_id int(11) DEFAULT '0' NOT NULL,
  file_type varchar(50),
  release_time int(11),
  downloads int(11) DEFAULT '0' NOT NULL,
  file_size int(11),
  post_time int(11) DEFAULT '0' NOT NULL,
  text_format int(11) DEFAULT '0' NOT NULL,
  downloads_week int(11) DEFAULT '0' NOT NULL,
  status char(1) DEFAULT 'N' NOT NULL,
  old_filename varchar(80) DEFAULT '' NOT NULL,
  PRIMARY KEY (filerelease_id),
  KEY group_id_idx (group_id),
  KEY user_id_idx (user_id),
  KEY unix_box_idx (unix_box),
  KEY post_time_idx (post_time)
);

#
# Table structure for table 'forum'
#
CREATE TABLE forum (
  msg_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_forum_id int(11) DEFAULT '0' NOT NULL,
  posted_by int(11) DEFAULT '0' NOT NULL,
  subject text NOT NULL,
  body text NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  is_followup_to int(11) DEFAULT '0' NOT NULL,
  thread_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (msg_id),
  KEY idx_forum_group_forum_id (group_forum_id),
  KEY idx_forum_is_followup_to (is_followup_to),
  KEY idx_forum_thread_id (thread_id)
);

#
# Table structure for table 'forum_group_list'
#
CREATE TABLE forum_group_list (
  group_forum_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  forum_name text NOT NULL,
  is_public int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (group_forum_id),
  KEY idx_forum_group_list_group_id (group_id)
);

#
# Table structure for table 'forum_monitored_forums'
#
CREATE TABLE forum_monitored_forums (
  monitor_id int(11) DEFAULT '0' NOT NULL auto_increment,
  forum_id int(11) DEFAULT '0' NOT NULL,
  user_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (monitor_id),
  KEY idx_forum_monitor_thread_id (forum_id),
  KEY idx_forum_monitor_combo_id (forum_id,user_id)
);

#
# Table structure for table 'forum_saved_place'
#
CREATE TABLE forum_saved_place (
  saved_place_id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_id int(11) DEFAULT '0' NOT NULL,
  forum_id int(11) DEFAULT '0' NOT NULL,
  save_date int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (saved_place_id)
);

#
# Table structure for table 'forum_thread_id'
#
CREATE TABLE forum_thread_id (
  thread_id int(11) DEFAULT '0' NOT NULL auto_increment,
  PRIMARY KEY (thread_id)
);

#
# Table structure for table 'group_category'
#
CREATE TABLE group_category (
  group_category_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  category_id int(11) DEFAULT '0' NOT NULL,
  primary_category int(1) DEFAULT '1' NOT NULL,
  PRIMARY KEY (group_category_id),
  KEY idx_group_category_group_id (group_id),
  KEY idx_group_category_category_id (category_id)
);

#
# Table structure for table 'group_env'
#
CREATE TABLE group_env (
  group_env_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  env_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (group_env_id),
  KEY group_id_idx (group_id),
  KEY env_id_idx (env_id)
);

#
# Table structure for table 'group_language'
#
CREATE TABLE group_language (
  group_language_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  language_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (group_language_id),
  KEY group_id_idx (group_id),
  KEY language_id_idx (language_id)
);

#
# Table structure for table 'groups'
#
CREATE TABLE groups (
  group_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_name varchar(40),
  homepage varchar(128),
  public int(11) DEFAULT '0' NOT NULL,
  status char(1) DEFAULT 'A' NOT NULL,
  unix_group_name varchar(30) DEFAULT '' NOT NULL,
  unix_box varchar(20) DEFAULT 'shell1' NOT NULL,
  http_domain varchar(80),
  short_description varchar(255),
  cvs_box varchar(20) DEFAULT 'cvs1' NOT NULL,
  license varchar(16),
  register_purpose text,
  license_other text,
  register_time int(11) DEFAULT '0' NOT NULL,
  use_bugs int(11) DEFAULT '1' NOT NULL,
  rand_hash text,
  file_downloads int(11) DEFAULT '0' NOT NULL,
  use_mail int(11) DEFAULT '1' NOT NULL,
  use_survey int(11) DEFAULT '1' NOT NULL,
  use_patch int(11) DEFAULT '1' NOT NULL,
  use_forum int(11) DEFAULT '1' NOT NULL,
  use_pm int(11) DEFAULT '1' NOT NULL,
  use_cvs int(11) DEFAULT '1' NOT NULL,
  use_news int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY (group_id),
  KEY idx_groups_status (status),
  KEY idx_groups_public (public)
);

#
# Table structure for table 'image'
#
CREATE TABLE image (
  image_id int(11) DEFAULT '0' NOT NULL auto_increment,
  image_category int(11) DEFAULT '1' NOT NULL,
  image_type varchar(40) DEFAULT '' NOT NULL,
  image_data blob,
  group_id int(11) DEFAULT '0' NOT NULL,
  image_bytes int(11) DEFAULT '0' NOT NULL,
  image_caption text,
  organization_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (image_id),
  KEY image_category_idx (image_category),
  KEY image_type_idx (image_type),
  KEY group_id_idx (group_id)
);

#
# Table structure for table 'mail_group_list'
#
CREATE TABLE mail_group_list (
  group_list_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  list_name text,
  is_public int(11) DEFAULT '0' NOT NULL,
  password varchar(16),
  list_admin int(11) DEFAULT '0' NOT NULL,
  status int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (group_list_id),
  KEY idx_mail_group_list_group (group_id)
);

#
# Table structure for table 'mailaliases'
#
CREATE TABLE mailaliases (
  mailaliases_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  domain varchar(80),
  user_name varchar(20),
  email_forward varchar(255),
  PRIMARY KEY (mailaliases_id)
);

#
# Table structure for table 'news_bytes'
#
CREATE TABLE news_bytes (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  submitted_by int(11) DEFAULT '0' NOT NULL,
  is_approved int(11) DEFAULT '0' NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  forum_id int(11) DEFAULT '0' NOT NULL,
  summary text,
  details text,
  PRIMARY KEY (id),
  KEY idx_news_bytes_forum (forum_id),
  KEY idx_news_bytes_group (group_id),
  KEY idx_news_bytes_approved (is_approved)
);

#
# Table structure for table 'organization'
#
CREATE TABLE organization (
  organization_id int(11) DEFAULT '0' NOT NULL auto_increment,
  org_name varchar(60) DEFAULT '' NOT NULL,
  org_type int(11) DEFAULT '0' NOT NULL,
  org_url text,
  register_time int(11) DEFAULT '0' NOT NULL,
  org_icon int(11),
  org_logo int(11),
  org_descriptivetext text,
  PRIMARY KEY (organization_id)
);

#
# Table structure for table 'organization_group'
#
CREATE TABLE organization_group (
  group_id int(11) DEFAULT '0' NOT NULL,
  organization_id int(11) DEFAULT '0' NOT NULL,
  KEY group_id_idx (group_id),
  KEY organization_id_idx (organization_id)
);

#
# Table structure for table 'organization_user'
#
CREATE TABLE organization_user (
  user_id int(11) DEFAULT '0' NOT NULL,
  organization_id int(11) DEFAULT '0' NOT NULL,
  KEY user_id_idx (user_id),
  KEY organization_id_idx (organization_id)
);

#
# Table structure for table 'patch'
#
CREATE TABLE patch (
  patch_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  patch_status_id int(11) DEFAULT '0' NOT NULL,
  patch_category_id int(11) DEFAULT '0' NOT NULL,
  submitted_by int(11) DEFAULT '0' NOT NULL,
  assigned_to int(11) DEFAULT '0' NOT NULL,
  open_date int(11) DEFAULT '0' NOT NULL,
  summary text,
  code mediumtext,
  close_date int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (patch_id),
  KEY idx_patch_group_id (group_id)
);

#
# Table structure for table 'patch_category'
#
CREATE TABLE patch_category (
  patch_category_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  category_name text NOT NULL,
  PRIMARY KEY (patch_category_id),
  KEY idx_patch_group_group_id (group_id)
);

#
# Table structure for table 'patch_history'
#
CREATE TABLE patch_history (
  patch_history_id int(11) DEFAULT '0' NOT NULL auto_increment,
  patch_id int(11) DEFAULT '0' NOT NULL,
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) DEFAULT '0' NOT NULL,
  date int(11),
  PRIMARY KEY (patch_history_id),
  KEY idx_patch_history_patch_id (patch_id)
);

#
# Table structure for table 'patch_status'
#
CREATE TABLE patch_status (
  patch_status_id int(11) DEFAULT '0' NOT NULL auto_increment,
  status_name text,
  PRIMARY KEY (patch_status_id)
);

#
# Table structure for table 'project_assigned_to'
#
CREATE TABLE project_assigned_to (
  project_assigned_id int(11) DEFAULT '0' NOT NULL auto_increment,
  project_task_id int(11) DEFAULT '0' NOT NULL,
  assigned_to_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (project_assigned_id),
  KEY idx_project_assigned_to_task_id (project_task_id),
  KEY idx_project_assigned_to_assigned_to (assigned_to_id)
);

#
# Table structure for table 'project_dependencies'
#
CREATE TABLE project_dependencies (
  project_depend_id int(11) DEFAULT '0' NOT NULL auto_increment,
  project_task_id int(11) DEFAULT '0' NOT NULL,
  is_dependent_on_task_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (project_depend_id),
  KEY idx_project_dependencies_task_id (project_task_id),
  KEY idx_project_is_dependent_on_task_id (is_dependent_on_task_id)
);

#
# Table structure for table 'project_group_list'
#
CREATE TABLE project_group_list (
  group_project_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  project_name text NOT NULL,
  is_public int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (group_project_id),
  KEY idx_project_group_list_group_id (group_id)
);

#
# Table structure for table 'project_history'
#
CREATE TABLE project_history (
  project_history_id int(11) DEFAULT '0' NOT NULL auto_increment,
  project_task_id int(11) DEFAULT '0' NOT NULL,
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) DEFAULT '0' NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (project_history_id),
  KEY idx_project_history_task_id (project_task_id)
);

#
# Table structure for table 'project_status'
#
CREATE TABLE project_status (
  status_id int(11) DEFAULT '0' NOT NULL auto_increment,
  status_name text NOT NULL,
  PRIMARY KEY (status_id)
);

#
# Table structure for table 'project_task'
#
CREATE TABLE project_task (
  project_task_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_project_id int(11) DEFAULT '0' NOT NULL,
  summary text NOT NULL,
  details text NOT NULL,
  percent_complete int(11) DEFAULT '0' NOT NULL,
  priority int(11) DEFAULT '0' NOT NULL,
  hours float(10,2) DEFAULT '0.00' NOT NULL,
  start_date int(11) DEFAULT '0' NOT NULL,
  end_date int(11) DEFAULT '0' NOT NULL,
  created_by int(11) DEFAULT '0' NOT NULL,
  status_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (project_task_id),
  KEY idx_project_task_group_project_id (group_project_id)
);

#
# Table structure for table 'security_log'
#
CREATE TABLE security_log (
  security_log_id int(11) DEFAULT '0' NOT NULL auto_increment,
  session_hash varchar(32) DEFAULT '' NOT NULL,
  time int(11) DEFAULT '0' NOT NULL,
  user_id int(11) DEFAULT '0' NOT NULL,
  category varchar(32),
  description text,
  PRIMARY KEY (security_log_id)
);

#
# Table structure for table 'session'
#
CREATE TABLE session (
  user_id int(11) DEFAULT '0' NOT NULL,
  session_hash char(32) DEFAULT '' NOT NULL,
  ip_addr char(15) DEFAULT '' NOT NULL,
  time int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (session_hash),
  KEY idx_session_user_id (user_id),
  KEY time_idx (time)
);

#
# Table structure for table 'session_text_vars'
#
CREATE TABLE session_text_vars (
  session_hash varchar(32) DEFAULT '' NOT NULL,
  name varchar(40) DEFAULT '' NOT NULL,
  value text,
  KEY session_var (session_hash,name),
  PRIMARY KEY (session_hash,name)
);

#
# Table structure for table 'snippet'
#
CREATE TABLE snippet (
  snippet_id int(11) DEFAULT '0' NOT NULL auto_increment,
  created_by int(11) DEFAULT '0' NOT NULL,
  name text,
  description text,
  type int(11) DEFAULT '0' NOT NULL,
  language int(11) DEFAULT '0' NOT NULL,
  license text NOT NULL,
  category int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (snippet_id),
  KEY idx_snippet_language (language),
  KEY idx_snippet_category (category)
);

#
# Table structure for table 'snippet_package'
#
CREATE TABLE snippet_package (
  snippet_package_id int(11) DEFAULT '0' NOT NULL auto_increment,
  created_by int(11) DEFAULT '0' NOT NULL,
  name text,
  description text,
  category int(11) DEFAULT '0' NOT NULL,
  language int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (snippet_package_id),
  KEY idx_snippet_package_language (language),
  KEY idx_snippet_package_category (category)
);

#
# Table structure for table 'snippet_package_item'
#
CREATE TABLE snippet_package_item (
  snippet_package_item_id int(11) DEFAULT '0' NOT NULL auto_increment,
  snippet_package_version_id int(11) DEFAULT '0' NOT NULL,
  snippet_version_id int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (snippet_package_item_id),
  KEY idx_snippet_package_item_pkg_ver (snippet_package_version_id)
);

#
# Table structure for table 'snippet_package_version'
#
CREATE TABLE snippet_package_version (
  snippet_package_version_id int(11) DEFAULT '0' NOT NULL auto_increment,
  snippet_package_id int(11) DEFAULT '0' NOT NULL,
  changes text,
  version text,
  submitted_by int(11) DEFAULT '0' NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (snippet_package_version_id),
  KEY idx_snippet_package_version_pkg_id (snippet_package_id)
);

#
# Table structure for table 'snippet_version'
#
CREATE TABLE snippet_version (
  snippet_version_id int(11) DEFAULT '0' NOT NULL auto_increment,
  snippet_id int(11) DEFAULT '0' NOT NULL,
  changes text,
  version text,
  submitted_by int(11) DEFAULT '0' NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  code text,
  PRIMARY KEY (snippet_version_id),
  KEY idx_snippet_version_snippet_id (snippet_id)
);

#
# Table structure for table 'stats_agg_logo_by_day'
#
CREATE TABLE stats_agg_logo_by_day (
  day int(11),
  count int(11)
);

#
# Table structure for table 'stats_agg_logo_by_group'
#
CREATE TABLE stats_agg_logo_by_group (
  group_id int(11),
  count int(11)
);

#
# Table structure for table 'stats_agg_pages_by_browser'
#
CREATE TABLE stats_agg_pages_by_browser (
  browser varchar(8),
  count int(11)
);

#
# Table structure for table 'stats_agg_pages_by_day'
#
CREATE TABLE stats_agg_pages_by_day (
  day int(11),
  count int(11)
);

#
# Table structure for table 'stats_agg_pages_by_hour'
#
CREATE TABLE stats_agg_pages_by_hour (
  hour int(11),
  count int(11)
);

#
# Table structure for table 'stats_agg_pages_by_plat_brow_ver'
#
CREATE TABLE stats_agg_pages_by_plat_brow_ver (
  platform varchar(8),
  browser varchar(8),
  ver float(10,2),
  count int(11)
);

#
# Table structure for table 'stats_agg_pages_by_platform'
#
CREATE TABLE stats_agg_pages_by_platform (
  platform varchar(8),
  count int(11)
);

#
# Table structure for table 'survey_question_types'
#
CREATE TABLE survey_question_types (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  type text NOT NULL,
  PRIMARY KEY (id)
);

#
# Table structure for table 'survey_questions'
#
CREATE TABLE survey_questions (
  question_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  question text NOT NULL,
  question_type int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (question_id),
  KEY idx_survey_questions_group (group_id)
);

#
# Table structure for table 'survey_rating_aggregate'
#
CREATE TABLE survey_rating_aggregate (
  type int(11) DEFAULT '0' NOT NULL,
  id int(11) DEFAULT '0' NOT NULL,
  response float(10,2) DEFAULT '0.00' NOT NULL,
  count int(11) DEFAULT '0' NOT NULL,
  KEY idx_survey_rating_aggregate_type_id (type,id)
);

#
# Table structure for table 'survey_rating_response'
#
CREATE TABLE survey_rating_response (
  user_id int(11) DEFAULT '0' NOT NULL,
  type int(11) DEFAULT '0' NOT NULL,
  id int(11) DEFAULT '0' NOT NULL,
  response int(11) DEFAULT '0' NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  KEY idx_survey_rating_responses_user_type_id (user_id,type,id),
  KEY idx_survey_rating_responses_type_id (type,id)
);

#
# Table structure for table 'survey_responses'
#
CREATE TABLE survey_responses (
  user_id int(11) DEFAULT '0' NOT NULL,
  group_id int(11) DEFAULT '0' NOT NULL,
  survey_id int(11) DEFAULT '0' NOT NULL,
  question_id int(11) DEFAULT '0' NOT NULL,
  response text NOT NULL,
  date int(11) DEFAULT '0' NOT NULL,
  KEY idx_survey_responses_user_survey (user_id,survey_id),
  KEY idx_survey_responses_user_survey_question (user_id,survey_id,question_id),
  KEY idx_survey_responses_survey_question (survey_id,question_id),
  KEY idx_survey_responses_group_id (group_id)
);

#
# Table structure for table 'surveys'
#
CREATE TABLE surveys (
  survey_id int(11) DEFAULT '0' NOT NULL auto_increment,
  group_id int(11) DEFAULT '0' NOT NULL,
  survey_title text NOT NULL,
  survey_questions text NOT NULL,
  is_active int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY (survey_id),
  KEY idx_surveys_group (group_id)
);

#
# Table structure for table 'top_group'
#
CREATE TABLE top_group (
  group_id int(11) DEFAULT '0' NOT NULL,
  group_name varchar(40),
  downloads_all int(11) DEFAULT '0' NOT NULL,
  rank_downloads_all int(11) DEFAULT '0' NOT NULL,
  rank_downloads_all_old int(11) DEFAULT '0' NOT NULL,
  downloads_week int(11) DEFAULT '0' NOT NULL,
  rank_downloads_week int(11) DEFAULT '0' NOT NULL,
  rank_downloads_week_old int(11) DEFAULT '0' NOT NULL,
  userrank int(11) DEFAULT '0' NOT NULL,
  rank_userrank int(11) DEFAULT '0' NOT NULL,
  rank_userrank_old int(11) DEFAULT '0' NOT NULL,
  forumposts_week int(11) DEFAULT '0' NOT NULL,
  rank_forumposts_week int(11) DEFAULT '0' NOT NULL,
  rank_forumposts_week_old int(11) DEFAULT '0' NOT NULL,
  pageviews_proj int(11) DEFAULT '0' NOT NULL,
  rank_pageviews_proj int(11) DEFAULT '0' NOT NULL,
  rank_pageviews_proj_old int(11) DEFAULT '0' NOT NULL,
  KEY rank_downloads_all_idx (rank_downloads_all),
  KEY rank_downloads_week_idx (rank_downloads_week),
  KEY rank_userrank_idx (rank_userrank),
  KEY rank_forumposts_week_idx (rank_forumposts_week),
  KEY pageviews_proj_idx (pageviews_proj)
);

#
# Table structure for table 'unix_user'
#
CREATE TABLE unix_user (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  status int(2) DEFAULT '0' NOT NULL,
  username varchar(15) DEFAULT '' NOT NULL,
  user_id int(11) DEFAULT '0' NOT NULL,
  shell varchar(20) DEFAULT '/bin/bash' NOT NULL,
  password varchar(40) DEFAULT '' NOT NULL,
  md5_password varchar(32) DEFAULT '' NOT NULL,
  added_by varchar(32) DEFAULT '' NOT NULL,
  datetime int(14) DEFAULT '0' NOT NULL,
  PRIMARY KEY (id),
  KEY idx_unix_user_user_id (user_id),
  KEY idx_unix_user_status (status)
);

#
# Table structure for table 'user'
#
CREATE TABLE user (
  user_id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_name text NOT NULL,
  email text NOT NULL,
  user_pw varchar(32) DEFAULT '' NOT NULL,
  realname varchar(32) DEFAULT '' NOT NULL,
  status char(1) DEFAULT 'A' NOT NULL,
  shell varchar(20) DEFAULT '/bin/bash' NOT NULL,
  unix_pw varchar(40) DEFAULT '' NOT NULL,
  unix_status char(1) DEFAULT 'N' NOT NULL,
  unix_uid int(11) DEFAULT '0' NOT NULL,
  unix_box varchar(10) DEFAULT 'shell1' NOT NULL,
  add_date int(11) DEFAULT '0' NOT NULL,
  confirm_hash varchar(32),
  mail_siteupdates int(11) DEFAULT '0' NOT NULL,
  mail_va int(11) DEFAULT '0' NOT NULL,
  authorized_keys text,
  email_new text,
  PRIMARY KEY (user_id),
  KEY idx_user_user (status)
);

#
# Table structure for table 'user_group'
#
CREATE TABLE user_group (
  user_group_id int(11) DEFAULT '0' NOT NULL auto_increment,
  user_id int(11) DEFAULT '0' NOT NULL,
  group_id int(11) DEFAULT '0' NOT NULL,
  admin_flags char(16) DEFAULT '' NOT NULL,
  bug_flags int(11) DEFAULT '0' NOT NULL,
  forum_flags int(11) DEFAULT '0' NOT NULL,
  project_flags int(11) DEFAULT '2' NOT NULL,
  patch_flags int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY (user_group_id),
  KEY idx_user_group_user_id (user_id),
  KEY idx_user_group_group_id (group_id),
  KEY bug_flags_idx (bug_flags),
  KEY forum_flags_idx (forum_flags),
  KEY project_flags_idx (project_flags),
  KEY admin_flags_idx (admin_flags)
);

#
# Table structure for table 'user_preferences'
#
CREATE TABLE user_preferences (
  user_id int(11) DEFAULT '0' NOT NULL,
  preference_name varchar(20),
  preference_value varchar(20),
  KEY idx_user_pref_user_id (user_id)
);

#
# Dumping data for table 'bug_resolution'
#

INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (1,'Fixed');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (2,'Invalid');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (3,'Wont Fix');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (4,'Later');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (5,'Remind');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (6,'Works For Me');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (100,'None');
INSERT INTO bug_resolution (resolution_id, resolution_name) VALUES (101,'Duplicate');

#
# Dumping data for table 'bug_status'
#

INSERT INTO bug_status (status_id, status_name) VALUES (1,'Open');
INSERT INTO bug_status (status_id, status_name) VALUES (3,'Closed');
INSERT INTO bug_status (status_id, status_name) VALUES (100,'None');

#
# Dumping data for table 'patch_category'
#

INSERT INTO patch_category (patch_category_id, group_id, category_name) VALUES (100,0,'None');

#
# Dumping data for table 'patch_status'
#

INSERT INTO patch_status (patch_status_id, status_name) VALUES (1,'Open');
INSERT INTO patch_status (patch_status_id, status_name) VALUES (2,'Closed');
INSERT INTO patch_status (patch_status_id, status_name) VALUES (3,'Deleted');
INSERT INTO patch_status (patch_status_id, status_name) VALUES (4,'Postponed');
INSERT INTO patch_status (patch_status_id, status_name) VALUES (100,'None');

#
# Dumping data for table 'project_status'
#

INSERT INTO project_status (status_id, status_name) VALUES (1,'Open');
INSERT INTO project_status (status_id, status_name) VALUES (2,'Closed');
INSERT INTO project_status (status_id, status_name) VALUES (100,'None');
INSERT INTO project_status (status_id, status_name) VALUES (3,'Deleted');

#
# Dumping data for table 'survey_question_types'
#

INSERT INTO survey_question_types (id, type) VALUES (1,'Radio Buttons 1-5');
INSERT INTO survey_question_types (id, type) VALUES (2,'Text Area');
INSERT INTO survey_question_types (id, type) VALUES (3,'Radio Buttons Yes/No');
INSERT INTO survey_question_types (id, type) VALUES (4,'Comment Only');
INSERT INTO survey_question_types (id, type) VALUES (5,'Text Field');
INSERT INTO survey_question_types (id, type) VALUES (100,'None');

INSERT INTO user 
(user_id, user_name, email, user_pw, realname, status, shell, unix_pw, unix_status, unix_uid, unix_box, add_date, confirm_hash, mail_siteupdates, mail_va, authorized_keys, email_new)  VALUES (100,'None','noreply@sourceforge.net','*********34343','0','S','0','0','0',0,'0',940000000,NULL,1,0,NULL,NULL);
