# MySQL dump 8.14
#
# Host: localhost    Database: dres
#--------------------------------------------------------
# Server version	3.23.41

#
# Table structure for table 'attributes'
#

DROP TABLE IF EXISTS attributes;
CREATE TABLE attributes (
  attr_id int(11) NOT NULL auto_increment,
  attr_req_id int(11) NOT NULL default '0',
  attr_name varchar(100) NOT NULL default '',
  attr_value text NOT NULL,
  PRIMARY KEY  (attr_id),
  UNIQUE KEY attr_req_id (attr_req_id,attr_name)
) TYPE=MyISAM;

#
# Table structure for table 'estimates'
#

DROP TABLE IF EXISTS estimates;
CREATE TABLE estimates (
  estimate_id int(11) NOT NULL auto_increment,
  estimate_req_id int(11) NOT NULL default '0',
  estimate_name varchar(100) NOT NULL default '',
  estimate_value varchar(16) NOT NULL default '',
  PRIMARY KEY  (estimate_id),
  KEY estimate_req_id (estimate_req_id)
) TYPE=MyISAM;

#
# Table structure for table 'folders'
#

DROP TABLE IF EXISTS folders;
CREATE TABLE folders (
  folder_id varchar(255) NOT NULL default '',
  folder_project_id int(11) NOT NULL default '0',
  folder_name varchar(100) default NULL,
  folder_prefix varchar(16) default NULL,
  folder_id_parent varchar(255) default NULL,
  PRIMARY KEY  (folder_id,folder_project_id)
) TYPE=MyISAM;

#
# Table structure for table 'keywords'
#

DROP TABLE IF EXISTS keywords;
CREATE TABLE keywords (
  keyword_id int(4) NOT NULL auto_increment,
  keyword_req_id int(11) NOT NULL default '0',
  keyword_content varchar(100) NOT NULL default '',
  PRIMARY KEY  (keyword_id),
  KEY keyword_req_id (keyword_req_id)
) TYPE=MyISAM;

#
# Table structure for table 'levels'
#

DROP TABLE IF EXISTS levels;
CREATE TABLE levels (
  level_id int(11) NOT NULL default '0',
  level_name varchar(32) default NULL,
  level_role varchar(32) default NULL,
  PRIMARY KEY  (level_id)
) TYPE=MyISAM;

#
# Table structure for table 'project_users'
#

DROP TABLE IF EXISTS project_users;
CREATE TABLE project_users (
  assgn_id int(4) NOT NULL auto_increment,
  project_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY  (assgn_id),
  UNIQUE KEY assgn_id (assgn_id)
) TYPE=MyISAM;

#
# Table structure for table 'projects'
#

DROP TABLE IF EXISTS projects;
CREATE TABLE projects (
  project_id int(11) NOT NULL auto_increment,
  project_name varchar(100) default NULL,
  PRIMARY KEY  (project_id),
  UNIQUE KEY project_id (project_id)
) TYPE=MyISAM;

#
# Table structure for table 'relations'
#

DROP TABLE IF EXISTS relations;
CREATE TABLE relations (
  rel_id int(11) NOT NULL auto_increment,
  rel_req_id int(11) NOT NULL default '0',
  rel_type varchar(100) NOT NULL default '',
  rel_reg_id_target int(11) NOT NULL default '0',
  rel_comment text,
  PRIMARY KEY  (rel_id)
) TYPE=MyISAM;

#
# Table structure for table 'reports'
#

DROP TABLE IF EXISTS reports;
CREATE TABLE reports (
  report_id int(11) NOT NULL auto_increment,
  report_name varchar(200) default NULL,
  report_date datetime default NULL,
  report_user_id int(11) NOT NULL default '0',
  report_project_id int(11) NOT NULL default '0',
  report_filter_folder_id varchar(255) NOT NULL default '',
  report_filter_recursive tinyint(1) default '0',
  report_filter_priority varchar(32) default NULL,
  report_filter_status varchar(32) default NULL,
  report_filter_keywords text,
  report_filter_text text,
  report_filter_versions varchar(32) default NULL,
  PRIMARY KEY  (report_id),
  UNIQUE KEY report_id (report_id)
) TYPE=MyISAM;

#
# Table structure for table 'requirements'
#

DROP TABLE IF EXISTS requirements;
CREATE TABLE requirements (
  req_id int(11) NOT NULL auto_increment,
  req_project_id int(11) NOT NULL default '1',
  req_identifier varchar(32) NOT NULL default '',
  req_name text NOT NULL,
  req_description text NOT NULL,
  req_priority varchar(16) NOT NULL default '',
  req_status varchar(16) NOT NULL default '',
  req_revision_version mediumint(10) NOT NULL default '0',
  req_revision_date datetime NOT NULL default '0000-00-00 00:00:00',
  req_revision_label varchar(100) default NULL,
  req_revision_author varchar(100) NOT NULL default '',
  req_revision_author_id varchar(100) default NULL,
  req_revision_comment text,
  req_rationale text,
  req_source text,
  req_viewpoint text,
  req_estimate_importance varchar(16) NOT NULL default '',
  req_estimate_cost varchar(16) NOT NULL default '',
  req_estimate_stability varchar(16) NOT NULL default '',
  req_estimate_risk varchar(16) NOT NULL default '',
  req_estimate_verifiability varchar(16) NOT NULL default '',
  req_definition_input text,
  req_definition_condition text,
  req_definition_processing text,
  req_definition_output text,
  req_folder_id varchar(255) NOT NULL default '0',
  req_id_root int(11) NOT NULL default '0',
  _lock_user varchar(32) default NULL,
  _lock_date datetime default NULL,
  PRIMARY KEY  (req_id),
  UNIQUE KEY req_id (req_id),
  KEY req_identifer (req_identifier)
) TYPE=MyISAM;

#
# Table structure for table 'samples'
#

DROP TABLE IF EXISTS samples;
CREATE TABLE samples (
  sample_id int(11) NOT NULL auto_increment,
  sample_req_id int(11) NOT NULL default '0',
  sample_name varchar(100) NOT NULL default '',
  sample_content text NOT NULL,
  PRIMARY KEY  (sample_id),
  UNIQUE KEY sample_req_id (sample_req_id,sample_name)
) TYPE=MyISAM;

#
# Table structure for table 'scenarios'
#

DROP TABLE IF EXISTS scenarios;
CREATE TABLE scenarios (
  scenario_id int(11) NOT NULL auto_increment,
  scenario_req_id int(11) NOT NULL default '0',
  scenario_name varchar(100) NOT NULL default '',
  scenario_content text NOT NULL,
  PRIMARY KEY  (scenario_id),
  UNIQUE KEY scenario_req_id (scenario_req_id,scenario_name)
) TYPE=MyISAM;

#
# Table structure for table 'testcases'
#

DROP TABLE IF EXISTS testcases;
CREATE TABLE testcases (
  case_id int(11) NOT NULL auto_increment,
  case_req_id int(11) NOT NULL default '0',
  case_name varchar(100) NOT NULL default '',
  case_content text NOT NULL,
  PRIMARY KEY  (case_id),
  UNIQUE KEY case_req_id (case_req_id,case_name)
) TYPE=MyISAM;

#
# Table structure for table 'users'
#

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id int(11) NOT NULL auto_increment,
  user_name varchar(100) default NULL,
  user_login varchar(50) default NULL,
  user_password varchar(50) default NULL,
  user_email varchar(200) default NULL,
  user_level int(11) NOT NULL default '0',
  user_date_registered datetime default NULL,
  user_date_logged datetime default NULL,
  PRIMARY KEY  (user_id),
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM;

INSERT INTO projects (project_id, project_name) VALUES (1, "Default project");
INSERT INTO users (user_id, user_name, user_login, user_password, user_level) VALUES (1, "DRES Administrator", "admin", "admin", 10);
INSERT INTO project_users (project_id, user_id) VALUES (1, 1);
INSERT INTO folders (folder_id, folder_project_id, folder_name, folder_prefix, folder_id_parent) VALUES ("/", 1, "Requirements", "R", NULL);
