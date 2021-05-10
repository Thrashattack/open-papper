<?php
/**
 * Locale - default translations
 *
 * If you want to help with a translation, please get in contact with us via
 * the props mailing list. http://sourceforge.net/mail/?group_id=29581
 *
 * Dont remove the lines with BEGIN:STRINGS and END:STRINGS. These are needed
 * for the auto generation of strings.
 *
 * @author      name <email_address>
 * @package     locale
 * @link        http://props.sourceforge.net/
 *              PROPS - Open Source News Publishing Platform
 * @copyright   Copyright (c) 2001 The Herald-Mail Co.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 'LICENSE' file included with this software for more details.
 *
 * @license     http://www.gnu.org/licenses/gpl.txt  GNU GENERAL PUBLIC LICENSE
 * @version     $Id: default.php,v 1.18 2008/03/05 22:42:15 greenie2600 Exp $
 * @author      PHP generated
 */

// Set locale
setlocale(LC_ALL,'en_US', 'usa_usa');

// Date / Time
props_setkey('config.date.format', '%A %e %B %Y');

// Workflow status:
// You can reorder complete lines as you like, add IDs and descriptions
// but DON'T CHANGE NUMBERS AND MEANING after first use of this props installation.
props_setkey('config.workflow_status', array(
    1 => 'Draft',
    2 => 'Ready for first edit',
    3 => 'Ready for final edit',
    4 => 'Ready for publication',
    5 => 'Hold'));

//BEGIN:STRINGS
$DEFAULT["%s results found"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["%s sent you the following story from %s:"] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["+archives.free_access"] = "Free access to paid archives";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["+content.assign_to_edition"] = "Assign to edition";
// D:/htdocs/props/modules/content/tags/assign_to_edition.php
$DEFAULT["+content.assign_to_section"] = "Assign to section";
// D:/htdocs/props/modules/content/tags/assign_to_section.php
$DEFAULT["+content.submit"] = "Submit content";
// D:/htdocs/props/modules/content/requesthandler.php
$DEFAULT["+displaystory.add_comment"] = "Add story comment";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["+polls.add_comment"] = "Add poll comment";
// D:/htdocs/props/modules/polls/requesthandler.php
$DEFAULT[".admincontent"] = "Content";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_delete.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_delete.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT[".admincontent.assign_to_edition"] = "Assign to edition";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT[".admincontent.censored_words_add"] = "Add censored words";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
$DEFAULT[".admincontent.censored_words_delete"] = "Delete censored words";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
$DEFAULT[".admincontent.censored_words_edit"] = "Edit censored words";
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
$DEFAULT[".admincontent.censored_words_manage"] = "Censored words";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
$DEFAULT[".admincontent.edition_add"] = "Add edition";
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
$DEFAULT[".admincontent.edition_delete"] = "Delete edition";
// D:/htdocs/props/modules/admincontent/admin/edition_delete.php
$DEFAULT[".admincontent.edition_order"] = "Order edition";
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
$DEFAULT[".admincontent.edition_preview"] = "Preview staging editions";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT[".admincontent.edition_publish"] = "Publish edition";
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
$DEFAULT[".admincontent.editions_manage"] = "Manage editions";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT[".admincontent.editions_manage_live"] = "Manage live site";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT[".admincontent.editions_manage_staging"] = "Manage staging editions";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT[".admincontent.manage_threadcodes"] = "Thread codes";
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
$DEFAULT[".admincontent.section_add"] = "Add section";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
$DEFAULT[".admincontent.section_delete"] = "Delete section";
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
$DEFAULT[".admincontent.section_edit"] = "Edit section";
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT[".admincontent.sections_manage"] = "Section management";
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
$DEFAULT[".admincontent.sections_reorder"] = "Reorder sections";
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
$DEFAULT[".admincontent.story_add"] = "Add story";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
$DEFAULT[".admincontent.story_delete"] = "Delete story";
// D:/htdocs/props/modules/admincontent/admin/story_delete.php
$DEFAULT[".admincontent.story_edit"] = "Edit story";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT[".admincontent.story_edit_comments"] = "Edit story comments";
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
$DEFAULT[".admincontent.story_revision_history"] = "View revision history";
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
$DEFAULT[".admincontent.story_view"] = "View story";
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT[".admincontent.storysearch"] = "Story search";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT[".admincontent.threadcodes_delete"] = "Delete thread codes";
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
$DEFAULT[".admincontent.wysiswg_editor"] = "Use WYSIWYG editor";
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT[".adminmain"] = "Props";
// D:/htdocs/props/modules/adminmain/admin/about.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT[".adminmain.about"] = "About";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT[".adminmain.mainmenu"] = "Main menu";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT[".adminmain.site_status"] = "View site status";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT[".archives"] = "Archives";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/search_log.php
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT[".archives.payments_report"] = "View payments stats";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT[".archives.search_log"] = "Search log";
// D:/htdocs/props/modules/archives/admin/search_log.php
$DEFAULT[".archives.subscription_plan_add"] = "Add subscription plan";
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
$DEFAULT[".archives.subscription_plan_delete"] = "Delete subscription plan";
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT[".archives.subscription_plan_edit"] = "Edit subscription plan";
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT[".archives.subscription_plans_manage"] = "Manage subscription plans";
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
$DEFAULT[".media"] = "Media";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/media/admin/media_view.php
$DEFAULT[".media.media_delete"] = "Delete media";
// D:/htdocs/props/modules/media/admin/media_edit.php
$DEFAULT[".media.media_edit"] = "Edit media";
// D:/htdocs/props/modules/media/admin/media_edit.php
$DEFAULT[".media.media_picker"] = "Assign media";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_picker.php
$DEFAULT[".media.media_search"] = "Search media";
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT[".media.media_upload"] = "Upload media";
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT[".media.media_view"] = "View original media";
// D:/htdocs/props/modules/media/admin/media_view.php
$DEFAULT[".polls"] = "Polls";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
$DEFAULT[".polls.delete_poll"] = "Delete poll";
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT[".polls.poll_add"] = "Add poll";
// D:/htdocs/props/modules/polls/admin/poll_add.php
$DEFAULT[".polls.poll_edit"] = "Edit poll";
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT[".polls.poll_edit_comments"] = "Edit poll comments";
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
$DEFAULT[".polls.polls_manage"] = "Manage polls";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT[".stats"] = "Stats";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT[".stats.stats_media"] = "View media stats";
// D:/htdocs/props/modules/stats/admin/stats_media.php
$DEFAULT[".stats.stats_sections"] = "View section stats";
// D:/htdocs/props/modules/stats/admin/stats_sections.php
$DEFAULT[".stats.stats_stories"] = "View story stats";
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT[".system"] = "System";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
// D:/htdocs/props/modules/system/admin/info.php
// D:/htdocs/props/modules/system/admin/phpinfo.php
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT[".system.db_check"] = "Check tables";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT[".system.db_maintenance"] = "Database maintenance";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT[".system.db_optimize"] = "Optimize tables";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT[".system.info"] = "System info";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT[".system.phpinfo"] = "PHP info";
// D:/htdocs/props/modules/system/admin/phpinfo.php
$DEFAULT[".system.update"] = "System update";
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT[".system.update_i18n"] = "Update translation strings";
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT[".system.update_privs"] = "Update user privs";
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT[".system.update_tags"] = "Update template tags";
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT[".users"] = "Users";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
// D:/htdocs/props/modules/users/admin/bulletins_manage.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT[".users.bookmark_add"] = "Add bookmark";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT[".users.bulletin_add"] = "Add bulletin";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
$DEFAULT[".users.bulletin_delete"] = "Delete bulletin";
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
$DEFAULT[".users.bulletin_edit"] = "Edit bulletin";
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
$DEFAULT[".users.bulletin_send"] = "Send bulletin";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT[".users.bulletins_manage"] = "Manage bulletins";
// D:/htdocs/props/modules/users/admin/bulletins_manage.php
$DEFAULT[".users.group_add"] = "Add user group";
// D:/htdocs/props/modules/users/admin/group_add.php
$DEFAULT[".users.group_delete"] = "Delete user group";
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT[".users.group_edit"] = "Edit user group";
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT[".users.groups_manage"] = "Group management";
// D:/htdocs/props/modules/users/admin/groups_manage.php
$DEFAULT[".users.preferences"] = "Preferences";
// D:/htdocs/props/modules/users/admin/preferences.php
$DEFAULT[".users.user_add"] = "Add user";
// D:/htdocs/props/modules/users/admin/user_add.php
$DEFAULT[".users.user_delete"] = "Delete user";
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT[".users.user_edit"] = "Edit user";
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT[".users.users_manage"] = "User management";
// D:/htdocs/props/modules/users/admin/users_manage.php
$DEFAULT[".users.users_search"] = "Users search";
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT[".users.wysiswg_editor"] = "Use WYSIWYG editor";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["A group with this name already exists."] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["A preview bulletin has been send to %s."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Abstract"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Access level"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Actions"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/polls/admin/polls_manage.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/users/admin/bulletins_manage.php
// D:/htdocs/props/modules/users/admin/groups_manage.php
$DEFAULT["Activation instructions"] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Active"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT["Add"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Add bookmark"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT["Add story"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Administrator"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Administrator privileges"] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["All"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["All content"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["All text fields are optional."] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Amount"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["An email has been send to %s."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["An email with activation instructions has been send to %s."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["An email with password recovery instructions has been send to %s."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Any"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Approve"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
$DEFAULT["Approve content for publishing"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Approve user submitted content."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
$DEFAULT["Approved for publication"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Archived"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Archives subscription renewal for user %s"] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Are you sure you want to delete this?"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Assign a weight to each story below (100 = hot story, 10 = low priority), then click update."] = "";
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
$DEFAULT["Assigned"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Assigned by"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Assigned to"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["At least two poll options must be entered."] = "";
// D:/htdocs/props/modules/polls/admin/poll_add.php
$DEFAULT["Auto archiving"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Auto change the access level of published stories in this section to %s after %s days."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Available tags"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Basic login"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Before the frontpage can display anything, you first need to %s, %s to it and %s it."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Best matches first"] = "";
// D:/htdocs/props/modules/archives/tags/archives_sortresults_radioselect.php
// D:/htdocs/props/modules/archives/tags/archives_sortresults_radioselect.php
$DEFAULT["Body content"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Bookmark added."] = "";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT["Bookmark name may not be empty."] = "";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT["Bookmarks"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/users/admin/bookmark_add.php
// D:/htdocs/props/modules/users/admin/preferences.php
$DEFAULT["Bot"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["Bounce email"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Browser info"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Bug report"] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Bulletin has been sent to %s subscribers."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Bulletin name"] = "";
// D:/htdocs/props/modules/users/admin/bulletins_manage.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Bulletin preview"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Bulletin shortname"] = "";
// D:/htdocs/props/modules/users/admin/bulletins_manage.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Bulletin subscriptions"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Byline"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Byline name"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["CURRENT LIVE SITE"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/content/tags/assign_to_edition.php
$DEFAULT["Can't write to the cache dir."] = "";
// D:/htdocs/props/lib/media.php
$DEFAULT["Cancel"] = "";
// D:/htdocs/props/lib/common.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Caption"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
$DEFAULT["Caption or credit line contains"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Change the admin control panel language."] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
$DEFAULT["Changing the language can be done at your %s panel."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Click and drag a folder icon onto a section name to add it to that section."] = "";
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
$DEFAULT["Click and drag a folder icon onto another folder to append it below that folder."] = "";
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
$DEFAULT["Click cancel when you finished uploading media files."] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Click on a section name to edit it."] = "";
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
$DEFAULT["Close"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/media/admin/media_view.php
// D:/htdocs/props/modules/media/admin/media_view.php
// D:/htdocs/props/modules/media/admin/media_view.php
$DEFAULT["Closed"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Comment"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
$DEFAULT["Comments"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["Comments enabled"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Content access management: free access, registered access and paid access (subscriptions)."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Content assigned to"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Content max 7 days old and in the RSS feed"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Content not approved"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Content purchased."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Content submitted."] = "";
// D:/htdocs/props/modules/content/requesthandler.php
$DEFAULT["Copyright"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Could not access file"] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["Could not establish connection to credit card authorization gateway."] = "";
// D:/htdocs/props/lib/commerce.php
$DEFAULT["Could not execute"] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["Could not instantiate mail function."] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["Could not redirect to server: "] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/lib/users.php
$DEFAULT["Created by"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Creation date is at least"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Creation date is no more than"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Credit URL"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Credit line"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Credit suffix"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Credits expire date"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Credits remaining"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Current Edition"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Current Poll"] = "";
// D:/htdocs/props/modules/polls/tags/poll.php
$DEFAULT["Current edition"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Currently published edition (live site)"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT["Database"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Database error. Please try again later."] = "";
// D:/htdocs/props/lib/common.php
// D:/htdocs/props/lib/database/mysql.php
// D:/htdocs/props/lib/database/mysql.php
// D:/htdocs/props/lib/database/mysql.php
// D:/htdocs/props/lib/database/mysqli.php
// D:/htdocs/props/lib/database/mysqli.php
// D:/htdocs/props/lib/database/mysqli.php
$DEFAULT["Date"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Days until expire"] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["Default"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/preferences.php
$DEFAULT["Default admin account settings detected. For security reasons, please change the default password."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Default user type"] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["Delete"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Deleted by Administrator"] = "";
// D:/htdocs/props/modules/displaystory/tags/story_comments.php
// D:/htdocs/props/modules/polls/tags/poll_comments.php
$DEFAULT["Delivery of content to multiple target platforms (HTML, XHTML, XML/XSL, WAP/WML, text, etc)."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Description"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["Details"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["Developers"] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Disable"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Disabled"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["Done"] = "";
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
$DEFAULT["Duration"] = "";
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Edit"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/polls/admin/polls_manage.php
// D:/htdocs/props/modules/users/admin/groups_manage.php
$DEFAULT["Edit comments"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Edit story"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Edition"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/content/tags/assign_to_edition.php
$DEFAULT["Editors notes. These will not be displayed on the frontpage."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Email address"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Email address where bounced emails will return. Can be the same as from email."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Enable"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Enable comments"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT["Enabled"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["End content"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["End date"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Engine"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Enter here a revision description"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Error moving uploaded file."] = "";
// D:/htdocs/props/lib/media.php
$DEFAULT["Error sending bulletin."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Error sending email"] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Error sending email."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Ever"] = "";
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
$DEFAULT["Export to CSV"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Extendable database abstraction layer."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["False"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Feature requests"] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Features"] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["File Error: Could not open file"] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["Find stories"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Find stories matching all of the following criteria"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["First"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Forums"] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Founder"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Free"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/displaystory/tags/related_storylist.php
// D:/htdocs/props/modules/globaltags/tags/storylist.php
$DEFAULT["From email"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["From name"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Frontpage"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Full name"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Full section name"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Function info"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Generate report"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Getting started"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Go back"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/edition_delete.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_delete.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/users/admin/bookmark_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Group"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Group cannot be deleted because it contains users. First delete or reassign the users before deleting this group."] = "";
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["Group details"] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["Group name"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["Groups"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
$DEFAULT["Guest"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/displaystory/tags/story_comments.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/polls/tags/poll_comments.php
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["HTML message"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Headline"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Hide or display help"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Hide or display the side menu"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Hits"] = "";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Hold down Ctrl or Apple key to select multiple tags or double click"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["ID"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["IMPORTANT!"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["IP Address"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["IP address"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["IP address of sender: %s."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["If users are not assigned to a group, they will get the default group for their user type, if available."] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["If you could not validate your action by clicking on the link, please visit this page:"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["If you did not register at our site, please disregard this email. You do not need to unsubscribe or take any further action."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["If you did not request this password change, please IGNORE and DELETE this email immediately. Only continue if you wish your password to be reset!"] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["If you still experience problems, please contact an administrator to rectify the problem."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["In"] = "";
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
$DEFAULT["In edit queue"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["In the past %s days"] = "";
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
$DEFAULT["Include in RSS feed"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Internal Server Error"] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Invalid Email address."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Invalid ID."] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_view.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Invalid date."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Invalid format."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Invalid integer for field '%s'."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Invalid key."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Invalid login. Please check your username and password."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Invalid page referer. Please submit this form again."] = "";
// D:/htdocs/props/lib/common.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_delete.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_delete.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/content/requesthandler.php
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/polls/requesthandler.php
// D:/htdocs/props/modules/users/admin/bookmark_add.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Invalid poll id."] = "";
// D:/htdocs/props/modules/polls/requesthandler.php
$DEFAULT["It is strictly forbidden to delete the front page section."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
$DEFAULT["It will ask you for an user id number and your key. These are shown below:"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Joined"] = "";
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["Key"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Keywords"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Label"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
$DEFAULT["Language"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/users/admin/preferences.php
$DEFAULT["Last"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Last %s days"] = "";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Last active"] = "";
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["Last checked"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Last login"] = "";
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Last year"] = "";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Login"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/lib/admin.php
$DEFAULT["Login is required to access this page."] = "";
// D:/htdocs/props/lib/stories.php
// D:/htdocs/props/lib/stories.php
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Login request detected without login details."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Logout"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Mailinglists and subscriptions."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Media"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Media details"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Media files size"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Media handling '%s'."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Members"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/groups_manage.php
$DEFAULT["Message displayed when an email client can not view HTML."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Message displayed when an email client can view HTML."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Modified"] = "";
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Modified by"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Modify search"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Modules"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Most popular searches in the past 90 days"] = "";
// D:/htdocs/props/modules/archives/admin/search_log.php
$DEFAULT["Most recent first"] = "";
// D:/htdocs/props/modules/archives/tags/archives_sortresults_radioselect.php
// D:/htdocs/props/modules/archives/tags/archives_sortresults_radioselect.php
$DEFAULT["Must be at least %s characters long."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["NOTE: Clicking the 'Send Bulletin' button will immediately transmit the message to all subscribers. This operation CANNOT BE UNDONE. Please make sure your message is complete and accurate before sending."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["NOTE: This story has already been published to the current edition. Any changes you make here will cause the story to be updated immediately on the live site."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["NOTE: This story is not yet approved and will not be accessible on the site."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["New password"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Next"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/archives/tags/archives_next_page.php
$DEFAULT["Next index"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["No"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["No PHP info available."] = "";
// D:/htdocs/props/modules/system/admin/phpinfo.php
// D:/htdocs/props/modules/system/admin/phpinfo.php
$DEFAULT["No default group"] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["No edition is currently published."] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT["No editions are currently open."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/content/tags/assign_to_edition.php
$DEFAULT["No expiration"] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
$DEFAULT["No file was uploaded."] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["No results found."] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/search_log.php
// D:/htdocs/props/modules/archives/admin/search_log.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/polls/admin/polls_manage.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["No staging editions are currently open."] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT["No valid poll option selected."] = "";
// D:/htdocs/props/modules/polls/requesthandler.php
$DEFAULT["No."] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT["Not approved"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Not assigned"] = "";
// D:/htdocs/props/lib/sections.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/content/tags/assign_to_edition.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Not checked"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Not enough credits left."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Not working?"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Notes"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Number of credits"] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["OK"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Only a length of %s character(s) is allowed."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["OpenID authentication error."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["OpenID authentication failed:"] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["OpenID login"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["OpenID verification cancelled."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["OpenID verification is not enabled."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Operating system"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Option"] = "";
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT["Optional"] = "";
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
$DEFAULT["Optional content which will appear on the section front"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Options"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Order"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Order sections for edition"] = "";
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
$DEFAULT["Origination"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Overhead"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Overview of system statistics."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["PROPS is a multi language extensible Internet publishing system, designed specifically for periodicals such as newspapers and magazines who want to publish online, either exclusively or as an extension of their print publication."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Paid archives"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Parent section"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Password"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/users/admin/user_add.php
$DEFAULT["Password recovery information from"] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Password recovery instructions"] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Path"] = "";
// D:/htdocs/props/modules/stats/admin/stats_sections.php
$DEFAULT["Pattern"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
$DEFAULT["Payments report"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Permission denied"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/lib/admin.php
$DEFAULT["Permissions-based multiuser and group management system."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Plaintext message"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Plan description"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["Please correct the errors first."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/polls/requesthandler.php
// D:/htdocs/props/modules/polls/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Please cut and paste, or type those numbers into the corresponding fields in the form."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Please register first at our site."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Please register first."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Please select the section in which the poll will appear."] = "";
// D:/htdocs/props/modules/polls/admin/poll_add.php
$DEFAULT["Plug-in API allowing modular extension of base functionality."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Poll question"] = "";
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT["Powerfull tag-based templating system, which gives strict separation of design and content."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Preview"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Preview email address"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Previous"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/archives/tags/archives_prev_page.php
$DEFAULT["Publication status"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Publish"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Publish date"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
$DEFAULT["Published"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Question"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["Quick pick name"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["RSS feed enabled"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Received error %s from credit card authorization gateway: %s"] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Records"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Reference ID"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Regards"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Register"] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Registered"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Registration information from"] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["Registration required"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/displaystory/tags/related_storylist.php
// D:/htdocs/props/modules/globaltags/tags/storylist.php
$DEFAULT["Remaining credits used for purchasing paid archive stories."] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Remove"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Replacement"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
$DEFAULT["Requires archives purchase"] = "";
// D:/htdocs/props/modules/displaystory/tags/related_storylist.php
// D:/htdocs/props/modules/globaltags/tags/storylist.php
$DEFAULT["Restricted area."] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Retype email address"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Retype password"] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Revenue"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Revision"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Revision by"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
$DEFAULT["Revision date"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
$DEFAULT["SMTP Error: Could not authenticate."] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["SMTP Error: Could not connect to SMTP host."] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["SMTP Error: Data not accepted."] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["SMTP Error: The following recipients failed"] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["Save"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/edition_order.php
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Save & preview"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Search"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["Search options"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Search results"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Section"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/polls/admin/polls_manage.php
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Section details"] = "";
// D:/htdocs/props/modules/polls/admin/poll_add.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
$DEFAULT["Select a bulletin to send"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Select file"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Send bulletin"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Send preview"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Server"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Server address"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Server info"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Server name"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["Set to -1 for unlimited credits."] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["Set to 0 for no expiration date."] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plan_add.php
// D:/htdocs/props/modules/archives/admin/subscription_plan_edit.php
$DEFAULT["Settings"] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
// D:/htdocs/props/modules/admincontent/admin/edition_add.php
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Short directory name"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Short directory name '%s' is already in use by another section."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Shortcuts"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["Signups"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Simply click on the link below and complete the rest of the form:"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Size"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Sorry for the inconvenience, we correct the error as soon as possible."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Sort by"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Source URL"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Source description"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Staged for publication"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Staging editions"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Staging editions (under construction)"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
$DEFAULT["Standard values 'prefix', 'name' and 'suffix' will not be stored."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Start date"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Static page content"] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["Statistics"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Status"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Stories"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Story"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Story credits included"] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
$DEFAULT["Story weight"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Stripped out html tags."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Sub caption"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Subhead"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_view.php
$DEFAULT["Subject"] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Subject can contain %s format."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["Submit Vote"] = "";
// D:/htdocs/props/modules/polls/tags/poll.php
$DEFAULT["Subscribers"] = "";
// D:/htdocs/props/modules/users/admin/bulletins_manage.php
$DEFAULT["Successfully checked table '%s'."] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Successfully optimized table '%s'."] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Summary"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Supplied email adresses do not match."] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Supplied passwords do not match."] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Support request"] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Table"] = "";
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Tags"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Tags are used in the templates."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT["Tags are used to tie related stories together, ex: 'Formula 1 2007')"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Tags assigned to this story"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Thank you for adding additional credits to your account."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Thank you for registering and enjoy your stay!"] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["The account is now activated and can be used."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["The administrator has been notified."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["The bookmark system is very flexible. You can create bookmarks from every non posted page, the [+] will indicate that a screen is available for bookmarking. Bookmarks can be added by clicking on [+] after '%s' in the sidebar. You can delete bookmarks at your %s panel."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["The bookmark system is very flexible. You can create bookmarks from every non posted page, the [+] will indicate that a screen is available for bookmarking. Bookmarks can be added by clicking on [+] after '%s'. You can delete bookmarks at your %s."] = "";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT["The content search function is very flexible and powerfull. Here are some examples to get started."] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["The credits will not expire when there is no date set."] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["The database is currently offline. Please try again later."] = "";
// D:/htdocs/props/lib/database/mysql.php
// D:/htdocs/props/lib/database/mysqli.php
$DEFAULT["The file was only partially uploaded."] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["The following From address failed"] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["The uploaded file exceeds the maximum size '%s'."] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["The uploaded image dimensions are too big. Unable to resize the image."] = "";
// D:/htdocs/props/lib/media.php
$DEFAULT["There are no bulletins. Please create one first."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["There are no published editions."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["There are no staging editions."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["There are no subscribers for this bulletin."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["There is already another section named '%s' below the selected parent section."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_add.php
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["There is no valid password recovery request pending for this account."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["There was an error activating this account."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["These editions are under construction."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["This OpenID is already registered."] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["This account cannot be activated."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["This account is closed. Please contact a site administrator for more information."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/lib/users.php
$DEFAULT["This audio file format is not supported."] = "";
// D:/htdocs/props/lib/media.php
$DEFAULT["This bookmark already exists."] = "";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT["This bookmark name already exists."] = "";
// D:/htdocs/props/modules/users/admin/bookmark_add.php
$DEFAULT["This edition contains stories. Please first delete or reassign the stories."] = "";
// D:/htdocs/props/modules/admincontent/admin/edition_delete.php
$DEFAULT["This email address is already registered."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["This email address is not registered."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["This email has been sent from %s."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["This field may not be empty."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["This is the current live edition."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["This media file format is not supported."] = "";
// D:/htdocs/props/lib/media.php
$DEFAULT["This month"] = "";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["This name already exists."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_add.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
$DEFAULT["This pattern already exists."] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_add.php
// D:/htdocs/props/modules/admincontent/admin/censored_words_edit.php
$DEFAULT["This protects against unwanted spam and malicious abuse."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["This section cannot be deleted because it contains sub-sections. You must first move or delete the sub-sections before you can delete this section."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
$DEFAULT["This section cannot be deleted because stories are assigned to it. You must first delete or reassign the stories before you can delete this section."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_delete.php
$DEFAULT["This site has nothing published yet. Please come back in a short time."] = "";
// D:/htdocs/props/lib/editions.php
$DEFAULT["This username already exists."] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["This username is already taken."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["This video file format is not supported."] = "";
// D:/htdocs/props/lib/media.php
$DEFAULT["This year"] = "";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Thread code"] = "";
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Timestamp"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Today"] = "";
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["Today or Yesterday"] = "";
// D:/htdocs/props/modules/archives/tags/archives_date_select.php
$DEFAULT["Top 20 search keywords"] = "";
// D:/htdocs/props/modules/archives/admin/search_log.php
$DEFAULT["Top 20 search strings"] = "";
// D:/htdocs/props/modules/archives/admin/search_log.php
$DEFAULT["Total"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/system/admin/db_maintenance.php
$DEFAULT["Total editions"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Total media files"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Total size"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Total stories"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Total users"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Transaction ID"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
$DEFAULT["Transaction confirmation from"] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Transaction was successful."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["True"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["Type"] = "";
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Unassigned"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["Unknown encoding"] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["Unkown upload error."] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Unlimited"] = "";
// D:/htdocs/props/lib/archives.php
$DEFAULT["Unselect for removal."] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
$DEFAULT["Update all user privs (frontpage and admin control panel)."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT["Update the locales with all the detected strings."] = "";
// D:/htdocs/props/modules/system/admin/update.php
$DEFAULT["Uploaded file '%s'"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["User"] = "";
// D:/htdocs/props/modules/users/admin/groups_manage.php
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["User ID"] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["User details"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["User groups"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["User privileges"] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["User types"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Username"] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/modules/admincontent/admin/story_edit_comments.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/archives/admin/payments_report.php
// D:/htdocs/props/modules/polls/admin/poll_edit_comments.php
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Users"] = "";
// D:/htdocs/props/modules/users/admin/users_manage.php
// D:/htdocs/props/modules/users/admin/users_search.php
$DEFAULT["Valid format"] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["Valid media types"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_upload.php
$DEFAULT["Version"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
// D:/htdocs/props/modules/admincontent/admin/story_revision_history.php
$DEFAULT["View Results"] = "";
// D:/htdocs/props/modules/polls/tags/poll.php
$DEFAULT["View history"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["View original"] = "";
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["View statistics for:"] = "";
// D:/htdocs/props/modules/stats/admin/stats_media.php
// D:/htdocs/props/modules/stats/admin/stats_sections.php
// D:/htdocs/props/modules/stats/admin/stats_stories.php
$DEFAULT["View stories"] = "";
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/editions_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Votes"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["We require that you validate your password recovery to ensure that you instigated this action."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["We require that you validate your registration to ensure that the email address you entered was correct."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["While responding to your request an error encountered in the application."] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["With a fresh installation, the first thing you need to do is updating %s. This will enable access to all admin functions."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["Workflow management."] = "";
// D:/htdocs/props/modules/adminmain/admin/about.php
$DEFAULT["Workflow status"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["Yes"] = "";
// D:/htdocs/props/modules/polls/admin/polls_manage.php
$DEFAULT["You already added the same comment."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/polls/requesthandler.php
$DEFAULT["You already voted for this poll."] = "";
// D:/htdocs/props/modules/polls/requesthandler.php
$DEFAULT["You are automatically logged in. If you don't want this, then please use the logout option and login again without the 'Remember me' box checked."] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["You are logged in as"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["You are logged in with cookies."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/lib/users.php
$DEFAULT["You are logged in."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/lib/users.php
$DEFAULT["You are now logged out."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["You can drag and drop the thumbnails to order assigned images."] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["You can not delete a founder and have to demote this user first."] = "";
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["You can set the group as default for guests, users and administrators. Founders have always all available privs."] = "";
// D:/htdocs/props/modules/users/admin/group_add.php
// D:/htdocs/props/modules/users/admin/group_edit.php
$DEFAULT["You can view the full story %s."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["You can view the full story at %s."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["You cannot add comments for this poll."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
// D:/htdocs/props/modules/polls/requesthandler.php
$DEFAULT["You cannot move a root section."] = "";
// D:/htdocs/props/modules/admincontent/admin/section_edit.php
$DEFAULT["You cannot send paid archive content via email."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["You did not select any criteria to search on."] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["You do not have permission to demote or promote founders."] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["You do not have permission to perform the selected action."] = "";
// D:/htdocs/props/modules/admincontent/admin/censored_words_manage.php
// D:/htdocs/props/modules/admincontent/admin/manage_threadcodes.php
// D:/htdocs/props/modules/admincontent/admin/sections_manage.php
// D:/htdocs/props/modules/media/admin/media_edit.php
// D:/htdocs/props/modules/polls/admin/poll_edit.php
// D:/htdocs/props/modules/system/admin/db_maintenance.php
// D:/htdocs/props/modules/system/admin/db_maintenance.php
// D:/htdocs/props/modules/system/admin/update.php
// D:/htdocs/props/modules/system/admin/update.php
// D:/htdocs/props/modules/system/admin/update.php
// D:/htdocs/props/modules/users/admin/bulletin_edit.php
// D:/htdocs/props/modules/users/admin/group_edit.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["You don't have permission to access this screen."] = "";
// D:/htdocs/props/lib/admin.php
// D:/htdocs/props/lib/admin.php
$DEFAULT["You have purchased %s story credit(s) which are valid until %s."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["You have purchased %s story credit(s) which do not expire."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["You have received this email because an user account password recovery was instigated on %s."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["You have received this email because this email address was used during registration for %s."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["You have received this email because you did a financial transaction on %s."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["You have successfully verified %s as your identity."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["You must activate this account first."] = "";
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/lib/users.php
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["You must be logged in to view this page."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["You must enter a HTML or plaintext message, or both."] = "";
// D:/htdocs/props/modules/users/admin/bulletin_send.php
// D:/htdocs/props/modules/users/admin/bulletin_send.php
$DEFAULT["You must enter a password."] = "";
// D:/htdocs/props/lib/users.php
$DEFAULT["You must login with your OpenID URL first. After that you will be redirected to the register form."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["You must provide at least one recipient email address."] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["You must specify a date which is more recent than that of the current live edition."] = "";
// D:/htdocs/props/modules/admincontent/admin/edition_publish.php
$DEFAULT["You only need to confirm the password if you entered a new one."] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["You only need to specify this if you are changing the users e-mail address."] = "";
// D:/htdocs/props/modules/users/admin/preferences.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["Your comment has been added."] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["Your credit card has been charged for the amount of %s."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Your credit card has expired."] = "";
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["Your credit(s) do not expire."] = "";
// D:/htdocs/props/modules/archives/tags/archives_credits_expire.php
$DEFAULT["Your password is changed."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Your preferences are updated."] = "";
// D:/htdocs/props/modules/users/requesthandler.php
$DEFAULT["Your previous purchase of this content has expired."] = "";
// D:/htdocs/props/lib/stories.php
$DEFAULT["account is blocked and not accessible"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["add an edition"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["add content"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["audio"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["audio/video"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["days ago"] = "";
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
// D:/htdocs/props/modules/admincontent/admin/storysearch.php
$DEFAULT["graphics"] = "";
// D:/htdocs/props/modules/media/admin/media_picker.php
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["help"] = "";
// D:/htdocs/props/lib/admin.php
$DEFAULT["here"] = "";
// D:/htdocs/props/modules/displaystory/requesthandler.php
$DEFAULT["invalid option"] = "";
// D:/htdocs/props/lib/common.php
$DEFAULT["mailer is not supported."] = "";
// D:/htdocs/props/lib/phpmailer/class.phpmailer.php
$DEFAULT["misc"] = "";
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["name"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["no value"] = "";
// D:/htdocs/props/modules/system/admin/info.php
$DEFAULT["prefix"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["publish"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["registered user with access to user/frontpage privs"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["seconds"] = "";
// D:/htdocs/props/modules/media/admin/media_search.php
$DEFAULT["suffix"] = "";
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_add.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
// D:/htdocs/props/modules/admincontent/admin/story_edit.php
$DEFAULT["template tags and user privs"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["unlimited"] = "";
// D:/htdocs/props/modules/archives/admin/subscription_plans_manage.php
// D:/htdocs/props/modules/archives/requesthandler.php
$DEFAULT["user with access to all available privs, even when not assigned"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["user with access to user/frontpage and admin privs"] = "";
// D:/htdocs/props/modules/users/admin/user_add.php
// D:/htdocs/props/modules/users/admin/user_edit.php
$DEFAULT["version"] = "";
// D:/htdocs/props/modules/adminmain/admin/mainmenu.php
$DEFAULT["votes"] = "";
// D:/htdocs/props/modules/polls/admin/poll_edit.php
//END:STRINGS

?>
