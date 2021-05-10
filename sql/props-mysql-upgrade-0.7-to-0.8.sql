--
-- Change `database_name` to your database name
--

RENAME TABLE `props_administrators` TO `_old_props_administrators` ;
RENAME TABLE `props_administrator_functions` TO `_old_props_administrator_functions` ;
RENAME TABLE `props_administrator_groups` TO `_old_props_administrator_groups` ;
RENAME TABLE `props_administrator_group_privs` TO `_old_props_administrator_group_privs` ;
RENAME TABLE `props_administrator_quicksearches` TO `_old_props_administrator_quicksearches` ;
RENAME TABLE `props_archives_signups` TO `_old_props_archives_signups` ;
RENAME TABLE `props_cc_transactions` TO `_old_props_cc_transactions` ;


ALTER TABLE `props_archives_searchlog_keywords` CHANGE `keyword` `keyword` VARCHAR( 64 ) NULL DEFAULT NULL ;


ALTER TABLE `props_archives_searchlog_strings` CHANGE `search_string` `search_string` VARCHAR( 255 ) NULL DEFAULT NULL ;


ALTER TABLE `props_archives_subscription_plans` CHANGE `plan_id` `plan_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_archives_subscription_plans` CHANGE `description` `description` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_archives_subscription_plans` CHANGE `cost` `amount` FLOAT( 10, 2 ) NOT NULL ;
ALTER TABLE `props_archives_subscription_plans` CHANGE `story_credits` `credits` INT NOT NULL ;
ALTER TABLE `props_archives_subscription_plans` CHANGE `days_until_expire` `days_until_expire` MEDIUMINT NOT NULL ;


ALTER TABLE `props_bulletins` CHANGE `bulletin_id` `bulletin_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_bulletins` CHANGE `bulletin_name` `bulletin_name` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_bulletins` CHANGE `bulletin_shortname` `bulletin_shortname` VARCHAR( 64 ) NOT NULL ;
ALTER TABLE `props_bulletins` ADD `from_name` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_bulletins` ADD `from_email` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_bulletins` ADD `bounce_email` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_bulletins` ADD `subject` VARCHAR( 128 ) NULL ;
ALTER TABLE `props_bulletins` ADD `html_template` TEXT NULL ;
ALTER TABLE `props_bulletins` ADD `plaintext_template` TEXT NULL ;
ALTER TABLE `props_bulletins` ADD UNIQUE ( `bulletin_name` ) ;
ALTER TABLE `props_bulletins` ADD UNIQUE ( `bulletin_shortname` ) ;


RENAME TABLE `props_users_bulletin_subscriptions`  TO `props_bulletins_subscriptions` ;
ALTER TABLE `props_bulletins_subscriptions` CHANGE `user_id` `user_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE `props_bulletins_subscriptions` CHANGE `bulletin_id` `bulletin_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE `props_bulletins_subscriptions` ADD PRIMARY KEY ( `user_id` , `bulletin_id` ) ;


CREATE TABLE `props_censored_words` (
  `censored_id` int(10) unsigned NOT NULL auto_increment,
  `pattern` varchar(128) NOT NULL,
  `replacement` varchar(128) NOT NULL,
  PRIMARY KEY  (`censored_id`),
  UNIQUE KEY `pattern` (`pattern`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `props_commerce_log`;
CREATE TABLE `props_commerce_log` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(32) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `description` varchar(128) NOT NULL,
  `amount` float(10,2) default NULL,
  `reference_id` varchar(255) default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;


CREATE TABLE `props_commerce_transactions` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(32) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `description` varchar(128) NOT NULL,
  `amount` float(10,2) default NULL,
  `reference_id` varchar(255) default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;


ALTER TABLE `props_editions` CHANGE `edition_id` `edition_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_editions` CHANGE `label` `label` VARCHAR( 32 ) NULL ;
ALTER TABLE `props_editions` CHANGE `closed` `closed` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `props_editions` DROP INDEX `publish_date` ;
ALTER TABLE `props_editions` DROP INDEX `closed` ;


RENAME TABLE `props_edition_story_xref`  TO `_old_props_edition_story_xref` ;


RENAME TABLE `props_modules`  TO `_old_props_modules` ;


RENAME TABLE `props_photos`  TO `props_media` ;
ALTER TABLE `props_media` CHANGE `photo_id` `media_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_media` ADD `section_id` INT UNSIGNED NULL AFTER `media_id` ;
ALTER TABLE `props_media` ADD `group_id` TINYINT UNSIGNED NOT NULL AFTER `section_id` ;
ALTER TABLE `props_media` CHANGE `width` `width` INT( 10 ) UNSIGNED NULL ;
ALTER TABLE `props_media` CHANGE `height` `height` INT( 10 ) UNSIGNED NULL ;
ALTER TABLE `props_media` ADD `size` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `height` ;
ALTER TABLE `props_media` ADD `duration` VARCHAR( 16 ) NULL AFTER `size` ;
ALTER TABLE `props_media` ADD `path` VARCHAR( 16 ) NOT NULL AFTER `duration` ;
ALTER TABLE `props_media` ADD `type` VARCHAR( 16 ) NOT NULL AFTER `path` ;
ALTER TABLE `props_media` CHANGE `caption` `caption` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_media` CHANGE `subcaption` `subcaption` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_media` CHANGE `credit` `credit_line` VARCHAR( 64 ) NULL ;
ALTER TABLE `props_media` CHANGE `credit_suffix` `credit_suffix` VARCHAR( 64 ) NULL ;
ALTER TABLE `props_media` ADD `credit_url` VARCHAR( 128 ) NULL AFTER `credit_suffix` ;
ALTER TABLE `props_media` ADD `keywords` VARCHAR( 64 ) NULL AFTER `credit_url` ;
ALTER TABLE `props_media` DROP `assigned` ;
ALTER TABLE `props_media` ADD INDEX ( `section_id` ) ;
ALTER TABLE `props_media` ADD INDEX ( `group_id` ) ;


RENAME TABLE `props_story_photo_xref` TO `props_media_story_xref` ;
ALTER TABLE `props_media_story_xref` CHANGE `story_id` `story_id` INT UNSIGNED NOT NULL ;
ALTER TABLE `props_media_story_xref` CHANGE `photo_id` `media_id` INT UNSIGNED NOT NULL ;
ALTER TABLE `props_media_story_xref` CHANGE `position` `position` TINYINT UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `props_media_story_xref` ADD PRIMARY KEY ( `story_id` , `media_id` ) ;
ALTER TABLE `props_media_story_xref` ADD INDEX ( `media_id` ) ;
ALTER TABLE `props_media_story_xref` ADD INDEX ( `story_id` ) ;


ALTER TABLE `props_polls` CHANGE `poll_id` `poll_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_polls` ADD `section_id` INT UNSIGNED NOT NULL AFTER `poll_id` ;
ALTER TABLE `props_polls` ADD `comments_enable` BOOL NOT NULL DEFAULT '1' AFTER `poll_active` ;
ALTER TABLE `props_polls` CHANGE `poll_question` `poll_question` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_1` `poll_option_1` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_2` `poll_option_2` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_3` `poll_option_3` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_4` `poll_option_4` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_5` `poll_option_5` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_6` `poll_option_6` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_7` `poll_option_7` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_8` `poll_option_8` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_9` `poll_option_9` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_10` `poll_option_10` VARCHAR( 255 ) NULL ;
ALTER TABLE `props_polls` CHANGE `poll_option_1_votes` `poll_option_1_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_2_votes` `poll_option_2_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_3_votes` `poll_option_3_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_4_votes` `poll_option_4_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_5_votes` `poll_option_5_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_6_votes` `poll_option_6_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_7_votes` `poll_option_7_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_8_votes` `poll_option_8_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_9_votes` `poll_option_9_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` CHANGE `poll_option_10_votes` `poll_option_10_votes` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_polls` ADD INDEX ( `section_id` ) ;


CREATE TABLE `props_polls_comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` DATETIME NOT NULL,
  `bodytext` mediumtext NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `poll_id` (`poll_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;


ALTER TABLE `props_sections` CHANGE `section_id` `section_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_sections` CHANGE `parent_id` `parent_id` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_sections` CHANGE `fullname` `fullname` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_sections` CHANGE `shortname` `shortname` VARCHAR( 64 ) NOT NULL ;
ALTER TABLE `props_sections` CHANGE `sortorder` `sortorder` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_sections` CHANGE `static_content` `static_content` mediumtext ;
ALTER TABLE `props_sections` ADD `auto_archive_enabled` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_sections` ADD `auto_archive_access_level` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_sections` ADD `auto_archive_days` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_sections` DROP INDEX `fullname` ;
ALTER TABLE `props_sections` DROP INDEX `shortname` ;
ALTER TABLE `props_sections` DROP INDEX `sortorder` ;
ALTER TABLE `props_sections` ADD UNIQUE ( `shortname` );


CREATE TABLE `props_sessions` (
  `session_id` varchar(128) NOT NULL,
  `session_data` blob NOT NULL,
  `session_expire` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `http_user_agent` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `props_stats_log`;
CREATE TABLE `props_stats_log` (
  `log_stamp` date NOT NULL,
  `command` varchar(32) NOT NULL,
  `id` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`log_stamp`,`command`,`id`)
) ENGINE=MyISAM;


ALTER TABLE `props_stories` CHANGE `story_id` `story_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_stories` CHANGE `created_stamp` `created_stamp` DATETIME NOT NULL ;
ALTER TABLE `props_stories` CHANGE `modified_stamp` `modified_stamp` DATETIME NULL ;
ALTER TABLE `props_stories` CHANGE `workflow_status_id` `workflow_status_id` TINYINT UNSIGNED NULL DEFAULT NULL ;
ALTER TABLE `props_stories` CHANGE `section_id` `section_id` INT UNSIGNED NULL DEFAULT NULL ;
ALTER TABLE `props_stories` CHANGE `publication_status_id` `publication_status_id` TINYINT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `props_stories` CHANGE `access_level` `access_level` TINYINT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `props_stories` CHANGE `revision` `revision` TINYINT UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `props_stories` CHANGE `revision_description` `revision_description` varchar(128) default NULL ;
ALTER TABLE `props_stories` CHANGE `origination` `origination` varchar(128) default NULL ;
ALTER TABLE `props_stories` CHANGE `headline` `headline` varchar(128) NOT NULL default '' ;
ALTER TABLE `props_stories` CHANGE `subhead` `subhead` varchar(128) default NULL ;
ALTER TABLE `props_stories` CHANGE `byline_prefix` `byline_prefix` varchar(128) default NULL ;
ALTER TABLE `props_stories` CHANGE `byline_name` `byline_name` varchar(128) default NULL ;
ALTER TABLE `props_stories` CHANGE `byline_suffix` `byline_suffix` varchar(128) default NULL ;
ALTER TABLE `props_stories` CHANGE `body_content` `body_content` mediumtext ;
ALTER TABLE `props_stories` CHANGE `end_content` `end_content` mediumtext ;
ALTER TABLE `props_stories` CHANGE `abstract` `abstract` mediumtext ;
ALTER TABLE `props_stories` CHANGE `notes` `notes` mediumtext ;
ALTER TABLE `props_stories` CHANGE `copyright` `copyright` varchar(128) default NULL ;
ALTER TABLE `props_stories` ADD `edition_id` INT UNSIGNED NULL AFTER `story_id` ;
ALTER TABLE `props_stories` ADD `content_type_id` TINYINT NOT NULL DEFAULT '1' AFTER `section_id` ;
ALTER TABLE `props_stories` ADD `weight` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '50';
ALTER TABLE `props_stories` ADD `comments_enable` BOOL NOT NULL DEFAULT '1' AFTER `access_level` ;
ALTER TABLE `props_stories` ADD `approved` BOOL NOT NULL DEFAULT '1' AFTER `comments_enable` ;
ALTER TABLE `props_stories` ADD `rss_feed` BOOL NOT NULL DEFAULT '1' AFTER `approved` ;
ALTER TABLE `props_stories` ADD `source_url` VARCHAR( 256 ) NULL AFTER `origination` ;
ALTER TABLE `props_stories` ADD `source_desc` VARCHAR( 128 ) NULL AFTER `source_url` ;
ALTER TABLE `props_stories` ADD INDEX ( `edition_id` ) ;
ALTER TABLE `props_stories` ADD INDEX ( `content_type_id` ) ;
ALTER TABLE `props_stories` DROP INDEX `published_stamp` ;
ALTER TABLE `props_stories` DROP INDEX `revision` ;


CREATE TABLE `props_stories_comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `story_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `timestamp` DATETIME NOT NULL,
  `bodytext` mediumtext NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `story_id` (`story_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;


ALTER TABLE `props_stories_previous_versions` ADD `revision_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `props_stories_previous_versions` CHANGE `story_id` `story_id` INT UNSIGNED NOT NULL ;
ALTER TABLE `props_stories_previous_versions` CHANGE `revision` `revision` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `props_stories_previous_versions` CHANGE `revision_description` `revision_description` varchar(128) default NULL ;
ALTER TABLE `props_stories_previous_versions` CHANGE `body_content` `body_content` mediumtext ;
ALTER TABLE `props_stories_previous_versions` CHANGE `end_content` `end_content` mediumtext ;
ALTER TABLE `props_stories_previous_versions` ADD INDEX ( `story_id` ) ;


CREATE TABLE `props_threadcodes` (
  `threadcode_id` int(10) unsigned NOT NULL auto_increment,
  `threadcode` varchar(32) NOT NULL,
  PRIMARY KEY  (`threadcode_id`)
) ENGINE=MyISAM;


CREATE TABLE `props_threadcodes_stories_xref` (
  `threadcode_id` int(10) unsigned NOT NULL,
  `story_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`threadcode_id`,`story_id`)
) ENGINE=MyISAM;


ALTER TABLE `props_users` CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `props_users` CHANGE `login` `username` varchar(14) NOT NULL ;
ALTER TABLE `props_users` CHANGE `password_md5` `password_md5` VARCHAR( 64 ) NULL ;
ALTER TABLE `props_users` CHANGE `email` `email_address` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `props_users` ADD `group_id` INT UNSIGNED NULL AFTER `user_id` ;
ALTER TABLE `props_users` ADD `user_type` tinyint(3) UNSIGNED NOT NULL AFTER `group_id` ;
ALTER TABLE `props_users` ADD `fullname` VARCHAR( 128 ) NULL AFTER `username` ;
ALTER TABLE `props_users` ADD `openid_url` VARCHAR( 128 ) NULL AFTER `password_md5` ;
ALTER TABLE `props_users` ADD `language` VARCHAR( 8 ) NULL AFTER `email_address` ;
ALTER TABLE `props_users` ADD `registered` DATETIME NULL ;
ALTER TABLE `props_users` ADD `last_login` DATETIME NULL ;
ALTER TABLE `props_users` ADD `last_ip` VARCHAR( 32 ) NULL ;
ALTER TABLE `props_users` ADD `activation_key` VARCHAR( 64 ) NULL ;
ALTER TABLE `props_users` ADD `recoverpw_key` VARCHAR( 64 ) NULL ;
ALTER TABLE `props_users` ADD INDEX ( `group_id` ) ;
ALTER TABLE `props_users` ADD UNIQUE (`username`) ;
ALTER TABLE `props_users` ADD UNIQUE (`email_address`) ;
ALTER TABLE `props_users` DROP `bulletin_subscriber` ;


ALTER TABLE `props_users_archive_credits` CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL ;
ALTER TABLE `props_users_archive_credits` CHANGE `stories_remaining` `credits` INT NOT NULL ;
ALTER TABLE `props_users_archive_credits` CHANGE `expire` `expire` DATETIME NULL ;
ALTER TABLE `props_users_archive_credits` ADD PRIMARY KEY ( `user_id` ) ;


ALTER TABLE `props_users_archive_stories_purchased` CHANGE `user_id` `user_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE `props_users_archive_stories_purchased` CHANGE `story_id` `story_id` INT UNSIGNED NOT NULL ;
ALTER TABLE `props_users_archive_stories_purchased` CHANGE `access_expires` `expire` DATETIME NULL ;
ALTER TABLE `props_users_archive_stories_purchased` ADD PRIMARY KEY ( `user_id` , `story_id` ) ;


CREATE TABLE `props_users_bookmarks` (
  `bookmark_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `bookmark_type` tinyint(1) NOT NULL default '1',
  `bookmark_name` varchar(32) NOT NULL,
  `bookmark_url` varchar(256) NOT NULL,
  PRIMARY KEY  (`bookmark_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;


CREATE TABLE `props_users_groupprivs` (
  `group_id` int(10) unsigned NOT NULL,
  `priv_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`group_id`,`priv_id`)
) ENGINE=MyISAM;


CREATE TABLE `props_users_groups` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `group_name` varchar(32) NOT NULL,
  `group_desc` varchar(256) NOT NULL,
  `default_user_type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=MyISAM;


CREATE TABLE `props_users_privs` (
  `priv_id` int(10) unsigned NOT NULL auto_increment,
  `module` varchar(32) NOT NULL,
  `function` varchar(32) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `in_menu` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`priv_id`),
  UNIQUE KEY `module` (`module`,`function`,`type`)
) ENGINE=MyISAM;


RENAME TABLE `props_weather`  TO `_old_props_weather` ;
RENAME TABLE `props_workflow_status`  TO `_old_props_workflow_status` ;


-- Copy threadcodes

INSERT INTO props_threadcodes(threadcode) SELECT DISTINCT(thread) FROM props_stories WHERE thread != '';
INSERT INTO props_threadcodes_stories_xref SELECT t1.threadcode_id, t2.story_id FROM props_threadcodes AS t1, props_stories AS t2 WHERE t1.threadcode = t2.thread;
ALTER TABLE `props_stories` DROP `thread` ;


-- --------------------------------------------------------

-- Update Media

UPDATE props_media SET group_id = 1, type = 'jpg' WHERE file_extension = 'jpg';
UPDATE props_media SET group_id = 1, type = 'jpg' WHERE file_extension = 'jpeg';
UPDATE props_media SET group_id = 1, type = 'gif' WHERE file_extension = 'gif';
UPDATE props_media SET group_id = 1, type = 'png' WHERE file_extension = 'png';
UPDATE props_media SET group_id = 1, type = 'tiff' WHERE file_extension = 'tiff';
UPDATE props_media SET group_id = 1, type = 'bmp' WHERE file_extension = 'bmp';
ALTER TABLE `props_media` DROP `file_extension` ;

-- Register all users

UPDATE props_users SET user_type = 4, registered = NOW() WHERE 1;

-- Delete users with duplicate admin logins or email_adresses

DELETE `props_users`  FROM `props_users` , `_old_props_administrators` WHERE props_users.email_address = _old_props_administrators.email_address ;
DELETE `props_users`  FROM `props_users` , `_old_props_administrators` WHERE props_users.username = _old_props_administrators.login ;

-- Copy admins to user table

INSERT INTO props_users SELECT '', NULL, 2, old.login, old.fullname, old.password_md5, NULL, old.email_address, NULL, NOW(), NULL, NULL, NULL, NULL FROM _old_props_administrators AS old;

-- Update admin_id's in story fields

UPDATE props_stories SET created_by = (SELECT props_users.user_id FROM props_users WHERE props_users.username = props_stories.created_by);
UPDATE props_stories SET modified_by = (SELECT props_users.user_id FROM props_users WHERE props_users.username = props_stories.modified_by);
UPDATE props_stories SET assigned_to = (SELECT props_users.user_id FROM props_users WHERE props_users.username = props_stories.assigned_to);
UPDATE props_stories SET assigned_by = (SELECT props_users.user_id FROM props_users WHERE props_users.username = props_stories.assigned_by);

ALTER TABLE `props_stories` CHANGE `created_by` `created_by` INT UNSIGNED NULL ;
ALTER TABLE `props_stories` CHANGE `modified_by` `modified_by` INT UNSIGNED NULL ;
ALTER TABLE `props_stories` CHANGE `assigned_to` `assigned_to` INT UNSIGNED NULL ;
ALTER TABLE `props_stories` CHANGE `assigned_by` `assigned_by` INT UNSIGNED NULL ;

-- Update admin_id's in story_revision field

UPDATE props_stories_previous_versions SET modified_by = (SELECT props_users.user_id FROM props_users WHERE props_users.username = props_stories_previous_versions.modified_by);
ALTER TABLE `props_stories_previous_versions` CHANGE `modified_by` `modified_by` INT UNSIGNED NOT NULL ;

-- Copy edition_id's from old xref table

UPDATE props_stories SET edition_id = (SELECT _old_props_edition_story_xref.edition_id FROM _old_props_edition_story_xref WHERE props_stories.story_id = _old_props_edition_story_xref.story_id);

-- --------------------------------------------------------

-- Default values

INSERT INTO `props_users` SET `user_type` = 1, `fullname` = 'Master Administrator', `username` = 'admin', `password_md5` = '5f4dcc3b5aa765d61d8327deb882cf99', `email_address` = 'admin@example.com';
