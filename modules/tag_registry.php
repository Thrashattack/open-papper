<?php
/**
 * Admin function
 *
 * Maps PROPS-tags to the module in which they are defined. If you create a
 * tag you must add it to this file or it will not be recognized.
 * You can do this manual, or use the 'update tags' button on the backend
 * mainmenu screen.
 *
 * Dont remove the line BEGIN:TAGS and END:TAGS. These are needed for
 * the auto generation of tags.
 *
 * @package     tags
 * @subpackage  registry
 * @version     $Id: tag_registry.php,v 1.23 2007/11/20 07:39:55 roufneck Exp $
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
 */

// We want this array to be global, but we want to make sure its
// contents have not already been manipulated from the outside
unset($TAG);
global $TAG;

//BEGIN:TAGS
$TAG["abstract"] = "displaystory";
$TAG["archives_credits_expire"] = "archives";
$TAG["archives_credits_remaining"] = "archives";
$TAG["archives_current_page"] = "archives";
$TAG["archives_date_select"] = "archives";
$TAG["archives_next_page"] = "archives";
$TAG["archives_num_pages"] = "archives";
$TAG["archives_prev_page"] = "archives";
$TAG["archives_search_results"] = "archives";
$TAG["archives_search_string"] = "archives";
$TAG["archives_sortresults_radioselect"] = "archives";
$TAG["archives_stories_purchased_list"] = "archives";
$TAG["archives_subscription_plans_radioselect"] = "archives";
$TAG["assign_to_edition"] = "content";
$TAG["assign_to_section"] = "content";
$TAG["base_ssl_url"] = "globaltags";
$TAG["base_url"] = "globaltags";
$TAG["body_content"] = "displaystory";
$TAG["bookmarks"] = "users";
$TAG["byline_name"] = "displaystory";
$TAG["byline_prefix"] = "displaystory";
$TAG["byline_suffix"] = "displaystory";
$TAG["cc_exp_month_select"] = "commerce";
$TAG["cc_exp_year_select"] = "commerce";
$TAG["cc_transaction_result_code"] = "commerce";
$TAG["cc_transaction_result_message"] = "commerce";
$TAG["cc_type_select"] = "commerce";
$TAG["copyright"] = "displaystory";
$TAG["current_datetime"] = "globaltags";
$TAG["debug_info"] = "globaltags";
$TAG["edition_date"] = "globaltags";
$TAG["edition_id"] = "globaltags";
$TAG["editionlist"] = "globaltags";
$TAG["editionyears"] = "globaltags";
$TAG["end_content"] = "displaystory";
$TAG["error_message"] = "globaltags";
$TAG["frontpage_url"] = "globaltags";
$TAG["gen_url"] = "globaltags";
$TAG["getkey"] = "globaltags";
$TAG["headline"] = "displaystory";
$TAG["include"] = "globaltags";
$TAG["list_bulletins"] = "users";
$TAG["most_popular_stories"] = "stats";
$TAG["page_id"] = "globaltags";
$TAG["poll"] = "polls";
$TAG["poll_comments"] = "polls";
$TAG["poll_results"] = "polls";
$TAG["poweredby"] = "globaltags";
$TAG["publication_name"] = "globaltags";
$TAG["related_storylist"] = "displaystory";
$TAG["request"] = "globaltags";
$TAG["request_uri"] = "globaltags";
$TAG["request_url"] = "globaltags";
$TAG["revision"] = "displaystory";
$TAG["scripts_url"] = "globaltags";
$TAG["section"] = "globaltags";
$TAG["section_fullname"] = "globaltags";
$TAG["section_shortname"] = "globaltags";
$TAG["sectionlist"] = "globaltags";
$TAG["standalone_media"] = "media";
$TAG["story_comments"] = "displaystory";
$TAG["story_date"] = "displaystory";
$TAG["story_id"] = "displaystory";
$TAG["story_media"] = "media";
$TAG["story_modified"] = "displaystory";
$TAG["story_url"] = "displaystory";
$TAG["story_views"] = "displaystory";
$TAG["storylist"] = "globaltags";
$TAG["subhead"] = "displaystory";
$TAG["tag_skeleton"] = "globaltags";
$TAG["user_name"] = "users";
$TAG["wordcount"] = "displaystory";
//END:TAGS

?>
