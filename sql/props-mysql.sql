--
-- Database: `props`
--
-- $Id: props-mysql.sql,v 1.52 2008/03/17 08:54:02 roufneck Exp $
--

-- --------------------------------------------------------

ALTER DATABASE CHARACTER SET utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `props_archives_searchlog_keywords`
--

DROP TABLE IF EXISTS `props_archives_searchlog_keywords`;
CREATE TABLE `props_archives_searchlog_keywords` (
  `search_timestamp` datetime NOT NULL,
  `keyword` varchar(64) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_archives_searchlog_strings`
--

DROP TABLE IF EXISTS `props_archives_searchlog_strings`;
CREATE TABLE `props_archives_searchlog_strings` (
  `search_timestamp` datetime NOT NULL,
  `search_string` varchar(255) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_archives_subscription_plans`
--

DROP TABLE IF EXISTS `props_archives_subscription_plans`;
CREATE TABLE `props_archives_subscription_plans` (
  `plan_id` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(128) collate utf8_unicode_ci NOT NULL,
  `amount` float(10,2) NOT NULL,
  `credits` int(11) NOT NULL,
  `days_until_expire` mediumint(9) NOT NULL,
  PRIMARY KEY  (`plan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_bulletins`
--

DROP TABLE IF EXISTS `props_bulletins`;
CREATE TABLE `props_bulletins` (
  `bulletin_id` int(10) unsigned NOT NULL auto_increment,
  `bulletin_name` varchar(128) collate utf8_unicode_ci NOT NULL,
  `bulletin_shortname` varchar(64) collate utf8_unicode_ci NOT NULL,
  `from_name` varchar(128) collate utf8_unicode_ci NOT NULL,
  `from_email` varchar(128) collate utf8_unicode_ci NOT NULL,
  `bounce_email` varchar(128) collate utf8_unicode_ci NOT NULL,
  `subject` varchar(128) collate utf8_unicode_ci default NULL,
  `html_template` text collate utf8_unicode_ci,
  `plaintext_template` text collate utf8_unicode_ci,
  PRIMARY KEY  (`bulletin_id`),
  UNIQUE KEY `bulletin_name` (`bulletin_name`),
  UNIQUE KEY `bulletin_shortname` (`bulletin_shortname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_bulletins_subscriptions`
--

DROP TABLE IF EXISTS `props_bulletins_subscriptions`;
CREATE TABLE `props_bulletins_subscriptions` (
  `user_id` int(10) unsigned NOT NULL,
  `bulletin_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`bulletin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_censored_words`
--

DROP TABLE IF EXISTS `props_censored_words`;
CREATE TABLE `props_censored_words` (
  `censored_id` int(10) unsigned NOT NULL auto_increment,
  `pattern` varchar(128) collate utf8_unicode_ci NOT NULL,
  `replacement` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`censored_id`),
  UNIQUE KEY `pattern` (`pattern`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_commerce_log`
--

DROP TABLE IF EXISTS `props_commerce_log`;
CREATE TABLE `props_commerce_log` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(32) collate utf8_unicode_ci NOT NULL,
  `transaction_date` datetime NOT NULL,
  `description` varchar(128) collate utf8_unicode_ci NOT NULL,
  `amount` float(10,2) default NULL,
  `reference_id` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_commerce_transactions`
--

DROP TABLE IF EXISTS `props_commerce_transactions`;
CREATE TABLE `props_commerce_transactions` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(32) collate utf8_unicode_ci NOT NULL,
  `transaction_date` datetime NOT NULL,
  `description` varchar(128) collate utf8_unicode_ci NOT NULL,
  `amount` float(10,2) default NULL,
  `reference_id` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_editions`
--

DROP TABLE IF EXISTS `props_editions`;
CREATE TABLE `props_editions` (
  `edition_id` int(10) unsigned NOT NULL auto_increment,
  `publish_date` datetime default NULL,
  `label` varchar(32) collate utf8_unicode_ci default NULL,
  `closed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`edition_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_media`
--

DROP TABLE IF EXISTS `props_media`;
CREATE TABLE `props_media` (
  `media_id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned default NULL,
  `group_id` tinyint(3) NOT NULL,
  `width` int(10) unsigned default NULL,
  `height` int(10) unsigned default NULL,
  `size` int(10) unsigned NOT NULL default '0',
  `duration` varchar(16) collate utf8_unicode_ci default NULL,
  `path` varchar(16) collate utf8_unicode_ci NOT NULL,
  `type` varchar(16) collate utf8_unicode_ci NOT NULL,
  `caption` varchar(255) collate utf8_unicode_ci default NULL,
  `subcaption` varchar(255) collate utf8_unicode_ci default NULL,
  `credit_line` varchar(64) collate utf8_unicode_ci default NULL,
  `credit_suffix` varchar(64) collate utf8_unicode_ci default NULL,
  `credit_url` varchar(128) collate utf8_unicode_ci default NULL,
  `keywords` varchar(64) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`media_id`),
  KEY `section_id` (`section_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_media_story_xref`
--

DROP TABLE IF EXISTS `props_media_story_xref`;
CREATE TABLE `props_media_story_xref` (
  `media_id` int(10) unsigned NOT NULL,
  `story_id` int(10) unsigned NOT NULL,
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`media_id`,`story_id`),
  KEY `media_id` (`media_id`),
  KEY `story_id` (`story_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_polls`
--

DROP TABLE IF EXISTS `props_polls`;
CREATE TABLE `props_polls` (
  `poll_id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned NOT NULL,
  `poll_active` tinyint(1) NOT NULL default '0',
  `comments_enable` tinyint(1) NOT NULL default '1',
  `poll_question` varchar(255) collate utf8_unicode_ci NOT NULL,
  `poll_option_1` varchar(255) collate utf8_unicode_ci NOT NULL,
  `poll_option_2` varchar(255) collate utf8_unicode_ci NOT NULL,
  `poll_option_3` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_4` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_5` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_6` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_7` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_8` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_9` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_10` varchar(255) collate utf8_unicode_ci default NULL,
  `poll_option_1_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_2_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_3_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_4_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_5_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_6_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_7_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_8_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_9_votes` mediumint(8) unsigned NOT NULL default '0',
  `poll_option_10_votes` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`poll_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_polls_comments`
--

DROP TABLE IF EXISTS `props_polls_comments`;
CREATE TABLE `props_polls_comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `bodytext` mediumtext collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `poll_id` (`poll_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_sections`
--

DROP TABLE IF EXISTS `props_sections`;
CREATE TABLE `props_sections` (
  `section_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `fullname` varchar(128) collate utf8_unicode_ci NOT NULL,
  `shortname` varchar(64) collate utf8_unicode_ci NOT NULL,
  `sortorder` tinyint(3) unsigned NOT NULL default '0',
  `static_content` mediumtext collate utf8_unicode_ci,
  `auto_archive_enabled` tinyint(1) unsigned NOT NULL default '0',
  `auto_archive_access_level` tinyint(3) unsigned NOT NULL default '0',
  `auto_archive_days` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`section_id`),
  UNIQUE KEY `shortname` (`shortname`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_sessions`
--

DROP TABLE IF EXISTS `props_sessions`;
CREATE TABLE `props_sessions` (
  `session_id` varchar(128) collate utf8_unicode_ci NOT NULL,
  `session_data` blob NOT NULL,
  `session_expire` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `http_user_agent` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_stats_log`
--

DROP TABLE IF EXISTS `props_stats_log`;
CREATE TABLE `props_stats_log` (
  `log_stamp` date NOT NULL,
  `command` varchar(32) collate utf8_unicode_ci NOT NULL,
  `id` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`log_stamp`,`command`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_stories`
--

DROP TABLE IF EXISTS `props_stories`;
CREATE TABLE `props_stories` (
  `story_id` int(10) unsigned NOT NULL auto_increment,
  `edition_id` int(10) unsigned default NULL,
  `section_id` int(10) unsigned default NULL,
  `content_type_id` tinyint(4) NOT NULL default '1',
  `created_stamp` datetime NOT NULL,
  `modified_stamp` datetime default NULL,
  `published_stamp` datetime default NULL,
  `workflow_status_id` tinyint(3) unsigned default NULL,
  `publication_status_id` tinyint(3) unsigned NOT NULL default '1',
  `created_by` int(10) unsigned default NULL,
  `modified_by` int(10) unsigned default NULL,
  `assigned_to` int(10) unsigned default NULL,
  `assigned_by` int(10) unsigned default NULL,
  `revision` tinyint(3) unsigned NOT NULL default '0',
  `revision_description` varchar(128) collate utf8_unicode_ci default NULL,
  `origination` varchar(128) collate utf8_unicode_ci default NULL,
  `headline` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `subhead` varchar(128) collate utf8_unicode_ci default NULL,
  `byline_prefix` varchar(128) collate utf8_unicode_ci default NULL,
  `byline_name` varchar(128) collate utf8_unicode_ci default NULL,
  `byline_suffix` varchar(128) collate utf8_unicode_ci default NULL,
  `body_content` mediumtext collate utf8_unicode_ci,
  `end_content` mediumtext collate utf8_unicode_ci,
  `abstract` mediumtext collate utf8_unicode_ci,
  `notes` mediumtext collate utf8_unicode_ci,
  `copyright` varchar(128) collate utf8_unicode_ci default NULL,
  `source_url` varchar(256) collate utf8_unicode_ci default NULL,
  `source_desc` varchar(128) collate utf8_unicode_ci default NULL,
  `access_level` tinyint(3) unsigned NOT NULL default '1',
  `comments_enable` tinyint(1) NOT NULL default '1',
  `approved` tinyint(1) NOT NULL default '1',
  `rss_feed` tinyint(1) NOT NULL default '1',
  `weight` tinyint(3) unsigned NOT NULL default '50',
  PRIMARY KEY  (`story_id`),
  KEY `edition_id` (`edition_id`),
  KEY `section_id` (`section_id`),
  KEY `publication_status_id` (`publication_status_id`),
  KEY `workflow_status_id` (`workflow_status_id`),
  KEY `content_type` (`content_type_id`),
  FULLTEXT KEY `headline` (`headline`),
  FULLTEXT KEY `body_content` (`body_content`),
  FULLTEXT KEY `end_content` (`end_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_stories_comments`
--

DROP TABLE IF EXISTS `props_stories_comments`;
CREATE TABLE `props_stories_comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `story_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `timestamp` datetime NOT NULL,
  `bodytext` mediumtext collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `story_id` (`story_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_stories_previous_versions`
--

DROP TABLE IF EXISTS `props_stories_previous_versions`;
CREATE TABLE `props_stories_previous_versions` (
  `revision_id` int(10) unsigned NOT NULL auto_increment,
  `story_id` int(10) unsigned NOT NULL,
  `revision` tinyint(3) unsigned NOT NULL default '0',
  `revision_description` varchar(128) collate utf8_unicode_ci default NULL,
  `body_content` mediumtext collate utf8_unicode_ci,
  `end_content` mediumtext collate utf8_unicode_ci,
  `modified_stamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `story_id` (`story_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_threadcodes`
--

DROP TABLE IF EXISTS `props_threadcodes`;
CREATE TABLE `props_threadcodes` (
  `threadcode_id` int(10) unsigned NOT NULL auto_increment,
  `threadcode` varchar(32) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`threadcode_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_threadcodes_stories_xref`
--

DROP TABLE IF EXISTS `props_threadcodes_stories_xref`;
CREATE TABLE `props_threadcodes_stories_xref` (
  `threadcode_id` int(10) unsigned NOT NULL,
  `story_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`threadcode_id`,`story_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users`
--

DROP TABLE IF EXISTS `props_users`;
CREATE TABLE `props_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned default NULL,
  `user_type` tinyint(3) unsigned NOT NULL,
  `username` varchar(14) collate utf8_unicode_ci NOT NULL,
  `fullname` varchar(128) collate utf8_unicode_ci default NULL,
  `password_md5` varchar(64) collate utf8_unicode_ci default NULL,
  `openid_url` varchar(128) collate utf8_unicode_ci default NULL,
  `email_address` varchar(128) collate utf8_unicode_ci NOT NULL,
  `language` varchar(8) collate utf8_unicode_ci default NULL,
  `registered` datetime default NULL,
  `last_login` datetime default NULL,
  `last_ip` varchar(32) collate utf8_unicode_ci default NULL,
  `activation_key` varchar(64) collate utf8_unicode_ci default NULL,
  `recoverpw_key` varchar(64) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`user_id`),
  KEY `group_id` (`group_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email_address` (`email_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users_archive_credits`
--

DROP TABLE IF EXISTS `props_users_archive_credits`;
CREATE TABLE `props_users_archive_credits` (
  `user_id` int(10) unsigned NOT NULL,
  `credits` int(11) NOT NULL,
  `expire` datetime default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users_archive_stories_purchased`
--

DROP TABLE IF EXISTS `props_users_archive_stories_purchased`;
CREATE TABLE `props_users_archive_stories_purchased` (
  `user_id` int(10) unsigned NOT NULL,
  `story_id` int(10) unsigned NOT NULL,
  `expire` datetime default NULL,
  PRIMARY KEY  (`user_id`,`story_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users_bookmarks`
--

DROP TABLE IF EXISTS `props_users_bookmarks`;
CREATE TABLE `props_users_bookmarks` (
  `bookmark_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `bookmark_type` tinyint(1) NOT NULL default '1',
  `bookmark_name` varchar(32) collate utf8_unicode_ci NOT NULL,
  `bookmark_url` varchar(256) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`bookmark_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users_groupprivs`
--

DROP TABLE IF EXISTS `props_users_groupprivs`;
CREATE TABLE `props_users_groupprivs` (
  `group_id` int(10) unsigned NOT NULL,
  `priv_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`group_id`,`priv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users_groups`
--

DROP TABLE IF EXISTS `props_users_groups`;
CREATE TABLE `props_users_groups` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `group_name` varchar(32) collate utf8_unicode_ci NOT NULL,
  `group_desc` varchar(256) collate utf8_unicode_ci NOT NULL,
  `default_user_type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `props_users_privs`
--

DROP TABLE IF EXISTS `props_users_privs`;
CREATE TABLE `props_users_privs` (
  `priv_id` int(10) unsigned NOT NULL auto_increment,
  `module` varchar(32) collate utf8_unicode_ci NOT NULL,
  `function` varchar(32) collate utf8_unicode_ci NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `in_menu` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`priv_id`),
  UNIQUE KEY `module` (`module`,`function`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Default predefined values
--

INSERT INTO `props_users` SET
  `user_type` = 1, `fullname` = 'Master Administrator', `username` = 'admin', `password_md5` = MD5('password'), `email_address` = 'admin@example.com';

INSERT INTO `props_sections` SET
  `section_id` = '1', `parent_id` = '0', `fullname` = 'Front page', `shortname` = 'front_page', `sortorder` = '1', `static_content` = NULL, `auto_archive_enabled` = '0', `auto_archive_access_level` = '0' , `auto_archive_days` = '0';
