<?php
/**
 * Lib - stories functions
 *
 * @package     api
 * @subpackage  stories
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
 * @version     $Id: stories.php,v 1.24 2008/03/17 08:54:03 roufneck Exp $
 */

/**
 * Defines
 */
define('PROPS_CONTENTTYPE_STORY',   1);
define('PROPS_CONTENTTYPE_MEDIA',   2);
define('PROPS_CONTENTTYPE_GALLERY', 3);
define('PROPS_CONTENTTYPE_BLOG',    4);

/**
 * Add story details to the registry
 *
 * Variables available in registry key 'story':
 * - <b>section_id</b>
 * - <b>story_id</b>
 * - <b>revision</b>
 * - <b>access_level</b>
 * - <b>origination</b>
 * - <b>copyright</b>
 * - <b>published_stamp</b>
 * - <b>modified_stamp</b>
 * - <b>headline</b>
 * - <b>subhead</b>
 * - <b>byline_prefix</b>
 * - <b>byline_name</b>
 * - <b>byline_suffix</b>
 * - <b>abstract</b>
 * - <b>body_content</b>     - body content with paragraphs
 * - <b>body_content_raw</b> - body content directly from database
 * - <b>end_content</b>
 * - <b>comments_allowed</b>
 *
 * @param  int  $story_id
 *
 * @todo  Fix Undefined property: stdClass::$section_shortname
 *        Need to get section info from other table.
 */
function story_to_registry($story_id)
{
    //  Retrieve story detail
    $q  = "SELECT * FROM props_stories WHERE story_id = $story_id "
        . "AND approved = " . TRUE . " ";

    // Protect unpublished stories
    if (props_getkey('request.preview') != TRUE) {
        $q .= "AND publication_status_id IN (" . PUBSTATUS_PUBLISHED . "," . PUBSTATUS_ARCHIVED . ") ";
    }

    $result = sql_query($q);
    $row = sql_fetch_assoc($result);

    if (!$row) {
        props_setkey('request.cmd', 'error-404');
        return FALSE;
    }

    props_setkey('story', $row);
    props_setkey('story.body_content', '<p>' . ereg_replace("\n", '</p><p>', $row['body_content']) . '</p>');
    props_setkey('story.body_content_raw', $row['body_content']);

    // Die if section_id is invalid
    if (!section_is_valid($row['section_id'])) {
        trigger_error("Story section_id no longer exists: ".$row['section_id'], E_USER_ERROR);
    }

    props_setkey('request.section_id', $row['section_id']);

    // Don't allow comments yet
    props_setkey('story.comments_allowed', FALSE);

    // Enable comments if comments are enabled and user has comment privs
    if (props_getkey('story.comments_enable') == TRUE && user_has_priv('displaystory', 'add_comment')) {
        // User has comment privs
        props_setkey('story.comments_allowed', TRUE);
    }
}

/**
 * Checks if a story_id exists
 * @param   int   $story_id
 * @return  bool  TRUE on success, FALSE on failure
 */
function story_id_is_valid($story_id)
{
    $story_id = intval($story_id);

    $q  = "SELECT story_id FROM props_stories ";
    $q .= "WHERE story_id = $story_id";
    $result = sql_query($q);

    if (sql_num_rows($result)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Returns the number of comments associated with a story
 * @param   int  $story_id
 * @return  int  number of comments
 */
function story_num_comments($story_id)
{
    $q  = "SELECT COUNT(*) as comments "
        . "FROM props_stories_comments "
        . "WHERE story_id = $story_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);
    return $row->comments;
}

/**
 * Returns the threadcode_id of the given threadcode
 * @param   string  $threadcode
 * @return  int     threadcode_id or FALSE on failure
 */
function get_threadcode_id($threadcode)
{
    $q  = "SELECT threadcode_id "
        . "FROM props_threadcodes "
        . "WHERE threadcode = '$threadcode'";
    $result = sql_query($q);

    if (mysql_num_rows($result)) {
        $row = sql_fetch_object($result);
        return $row->threadcode_id;
    } else {
        return FALSE;
    }

}

/**
 * Checks is a user has access to a story.
 * @param   int   $story_id
 * @return  bool  TRUE on success, FALSE on failure
 */
function stories_check_access_level($story_id = '')
{
    $story_id = ($story_id) ? $story_id: props_getrequest('story_id', VALIDATE_INT);
    if (!$story_id) {
        trigger_error('No story ID found.', E_USER_WARNING);
        return FALSE;
    }

    switch (props_getkey('story.access_level')) {
        case ACCESS_PAID_ARCHIVES:
            // User must be logged in
            if (!user_is_logged_in()) {
                // Reset request data and kick user to the login screen
                props_debug('CONTENT PAID ARCHIVES: login is required');
                props_error("Login is required to access this page.");
                props_setkey('request.template', '');
                props_setkey('request.cmd', 'users-login');
                return FALSE;
            }

            // Paid archives are off, so give access
            if (props_getkey('config.archives.paid') == FALSE) {
                props_debug('CONTENT PAID ARCHIVES: paid archives off');
                return TRUE;
            }

            // Check if user has free access priv
            if (user_has_priv('archives', 'free_access')) {
                props_debug('CONTENT PAID ARCHIVES: free access priv override');
                return TRUE;
            }

            // Check if user has valid periodic subscription
            $q  = "SELECT * FROM props_users_archive_credits "
                . "WHERE user_id = ".$_SESSION['PROPS_USER']['user_id'];
            $result = sql_query($q);
            $archive_credits = sql_fetch_object($result);

            if ($archive_credits) {
                if ($archive_credits->credits == -1 && ($archive_credits->expire == NULL
                    || $archive_credits->expire == '0000-00-00 00:00:00'))
                {
                    // User has unlimited credits (periodic subscription)
                    props_debug('CONTENT PAID ARCHIVES: unlimited credits - periodic subscription');
                    return TRUE;
                }
            }

            // Check if user has paid for it before
            $status = archives_story_is_purchased($_SESSION['PROPS_USER']['user_id'], $story_id);

            if ($status === TRUE) {
                return TRUE;
            } elseif ($status === NULL) {
                props_error("Your previous purchase of this content has expired.");
            }

            // Check for credits left
            if ($archive_credits) {
                if ($archive_credits->credits > 0 && ($archive_credits->expire == NULL
                    || $archive_credits->expire == '0000-00-00 00:00:00'))
                {
                    // Enough credits, present credit confirm screen
                    props_debug('CONTENT PAID ARCHIVES: user must confirm credits');
                    props_setkey('request.template', '');
                    props_setkey('request.cmd', 'archives-confirm_credit');
                    return FALSE;
                }
            }

            // User must pay for gaining access
            props_debug('CONTENT PAID ARCHIVES: must purchase content');

            props_setkey('request.template', '');
            props_setkey('request.cmd', 'archives-purchase_content');
            return FALSE;

            break;

        case ACCESS_REG_REQUIRED:
            // User must be logged in
            if (!user_is_logged_in()) {
                // Reset request data and kick user to the login screen
                props_debug('CONTENT REG REQUIRED: login is required');
                props_error("Login is required to access this page.");
                props_setkey('request.template', '');
                props_setkey('request.cmd', 'users-login');
                return FALSE;
            }

            props_debug('CONTENT REG REQUIRED: user is logged in');
            // Give access
            return TRUE;

            break;

        default:

            // Just give access
            props_debug('CONTENT FREE ACCESS: display content');
            return TRUE;

            break;
    }
}

?>
