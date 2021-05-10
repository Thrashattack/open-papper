--  Add polls table
DROP TABLE IF EXISTS props_polls;
CREATE TABLE props_polls (
  poll_id int(11) NOT NULL auto_increment,
  poll_active tinyint(1) NOT NULL default '0',
  poll_question char(255) NOT NULL default '',
  poll_option_1 char(255) NOT NULL default '',
  poll_option_2 char(255) NOT NULL default '',
  poll_option_3 char(255) default NULL,
  poll_option_4 char(255) default NULL,
  poll_option_5 char(255) default NULL,
  poll_option_6 char(255) default NULL,
  poll_option_7 char(255) default NULL,
  poll_option_8 char(255) default NULL,
  poll_option_9 char(255) default NULL,
  poll_option_10 char(255) default NULL,
  poll_option_1_votes int(11) NOT NULL default '0',
  poll_option_2_votes int(11) NOT NULL default '0',
  poll_option_3_votes int(11) NOT NULL default '0',
  poll_option_4_votes int(11) NOT NULL default '0',
  poll_option_5_votes int(11) NOT NULL default '0',
  poll_option_6_votes int(11) NOT NULL default '0',
  poll_option_7_votes int(11) NOT NULL default '0',
  poll_option_8_votes int(11) NOT NULL default '0',
  poll_option_9_votes int(11) NOT NULL default '0',
  poll_option_10_votes int(11) NOT NULL default '0',
  PRIMARY KEY  (poll_id)
) TYPE=MyISAM;


--  Add various functions / permissions to admin screens

INSERT INTO props_administrator_functions VALUES (194,'polls','add_poll','Add a new poll',1);
INSERT INTO props_administrator_functions VALUES (196,'polls','edit_polls','List existing polls',1);
INSERT INTO props_administrator_functions VALUES (197,'polls','edit_poll','Edit an existing poll',0);
INSERT INTO props_administrator_functions VALUES (198,'polls','delete_poll','Delete an existing poll',0);
INSERT INTO props_administrator_functions VALUES (199,'users','send_bulletin','Send an email bulletin',1);
INSERT INTO props_administrator_functions VALUES (200,'users','user_search','Search user accounts',1);
INSERT INTO props_administrator_functions VALUES (201,'users','edit_user','Edit an existing user account',0);
INSERT INTO props_administrator_functions VALUES (202,'users','add_user','Add a new user account',1);
INSERT INTO props_administrator_functions VALUES (203,'users','manage_bulletins','Manage bulletins',1);
INSERT INTO props_administrator_functions VALUES (204,'users','add_bulletin','Add a new bulletin',0);
INSERT INTO props_administrator_functions VALUES (205,'users','edit_bulletin','Edit an existing bulletin',0);

-- Split 'edit story' priv into separate view/change perms
DELETE FROM props_administrator_functions WHERE module = 'adminstories' and function = 'edit_story';
INSERT INTO props_administrator_functions VALUES (206,'adminstories','view_story','View an existing story',0);
INSERT INTO props_administrator_functions VALUES (207,'adminstories','change_story','Save changes to an existing story',0);
DELETE FROM props_administrator_group_privs WHERE module = 'adminstories' AND function = 'edit_story';

--  Add new modules

INSERT INTO props_modules VALUES ('polls','Polls',1);
INSERT INTO props_modules VALUES ('users','Users',1);
INSERT INTO props_modules VALUES ('editions','Editions',1);


--  Add WYSIWYG editor column to admin user prefs

ALTER TABLE props_administrators ADD COLUMN wysiwyg_editor char(20) NOT NULL default '*None*';


--  Add column for section front static content

ALTER TABLE props_sections ADD COLUMN static_content mediumtext NULL;

--  Add Access Level column to stories
ALTER TABLE props_stories ADD COLUMN access_level int NOT NULL default 1;

--  Set correct Access Level for any stories marked as paid archives items
UPDATE props_stories SET access_level = 3 WHERE paid_archives = 1;

-- Get rid of the now-obsolete paid_archives_item column
ALTER TABLE props_stories DROP COLUMN paid_archives;

-- Add the (related stories) thread column
ALTER TABLE props_stories ADD COLUMN thread char(25) NOT NULL default '';

-- Create the table which contains a list of all bulletins
DROP TABLE IF EXISTS props_bulletins;
CREATE TABLE props_bulletins (
  bulletin_id int(11) NOT NULL auto_increment,
  bulletin_name char(128) default NULL,
  bulletin_shortname char(64) NOT NULL default '',
  PRIMARY KEY  (bulletin_id)
) TYPE=MyISAM;

INSERT INTO props_bulletins VALUES (1,'General Bulletin','general');
INSERT INTO props_bulletins VALUES (2,'Breaking News','breakingnews');

-- Create the table which tracks which users are subscribed to which bulletins
DROP TABLE IF EXISTS props_users_bulletin_subscriptions;
CREATE TABLE props_users_bulletin_subscriptions(
    user_id int NOT NULL,
    bulletin_id int NOT NULL
) TYPE=MyISAM;

-- Add the assigned column
ALTER TABLE props_photos ADD COLUMN assigned int(1) NOT NULL default '0';

-- Add the bulletin_subscriber column
ALTER TABLE props_users ADD COLUMN bulletin_subscriber int(11) NOT NULL default '0';
