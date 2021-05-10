<?php
/**
 * Lib - archives functions
 *
 * @package     api
 * @subpackage  archives
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
 * @version     $Id: archives.php,v 1.13 2007/11/13 13:10:22 roufneck Exp $
 */

/**
 * Returns the number of remaining credits
 *
 * @param   int  $user_id
 * @return  int  remaining credits
 */
function archives_credits_remaining($user_id)
{
    $q  = "SELECT * FROM props_users_archive_credits "
        . "WHERE user_id = $user_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    if ($row->credits == -1) {
        return strtolower(props_gettext("Unlimited"));
    }

    return $row->credits;
}

/**
 * Returns the remaining time before expiring
 *
 * @param   int  $user_id
 * @return  int  remaining time
 */
function archives_credits_expire($user_id)
{
    $q  = "SELECT * FROM props_users_archive_credits "
        . "WHERE user_id = $user_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    return $row->expire;
}

/**
 * Purchase a story
 *
 * @param   int  $user_id
 * @param   int  $story_id
 * @return  mixed  TRUE on success, FALSE on failure, NULL when purchase has expired
 */
function archives_story_is_purchased($user_id, $story_id)
{
    // Check if user has paid for it before
    $q  = "SELECT * FROM props_users_archive_stories_purchased "
        . "WHERE user_id = $user_id "
        . "AND story_id = $story_id";
    $result = sql_query($q);
    $row = mysql_fetch_object($result);

    if ($row) {
        if ($row->expire == NULL || $row->expire == '0000-00-00 00:00:00') {
            props_debug('CONTENT PAID ARCHIVES: content purchased for lifetime');
            return TRUE;
        }

        if ($row->expire > date('Y-m-d')) {
            props_debug('CONTENT PAID ARCHIVES: content already purchased');
            return TRUE;
        } else {
            props_debug('CONTENT PAID ARCHIVES: content purchase expired');
            return NULL;
        }
    }

    return FALSE;
}

/**
 * Purchase a story
 *
 * @param   int    $user_id
 * @param   int    $story_id
 * @return  mixed  TRUE on success, FALSE on failure, NULL when purchased before,
 *                 -1 when not enough credits left
 */
function archives_purchase_story($user_id, $story_id)
{
    // access to story expires in 1 year
    $expire = date('Y-m-d', (time() + (86400 * 365)));

    // Make sure we do not charge the user twice for the same story. This could
    // conceivably happen if they hit reload on certain pages.
    if (archives_story_is_purchased($user_id, $story_id) !== TRUE) {

        // Decrement the user"s remaining credits
        $q  = "UPDATE props_users_archive_credits SET "
            . "  credits = (credits - 1) "
            . "WHERE user_id = $user_id AND credits > 0";
        $result = sql_query($q);

        if (sql_affected_rows($result) != 1) {
            return -1;
        }

        // Add this story to user's list of purchased stories,
        // so they can access it again in the future
        $q  = "INSERT INTO props_users_archive_stories_purchased SET "
            . "  user_id = $user_id, "
            . "  story_id = $story_id, "
            . "  expire = '$expire' "
            . "ON DUPLICATE KEY UPDATE "
            . "  expire = '$expire'";
        $result = sql_query($q);

        if (sql_affected_rows($result) != 1) {
            // Send an admin alert
            trigger_error("Error adding the story to purchased stories table for user '$user_id'.", E_USER_WARNING);
            return FALSE;
        }

        return TRUE;
    }

    return NULL;
}

/**
 * Return details of subscription plan
 *
 * <code>
 * // returns
 * array('description', 'cost', 'story_credits', 'days_until_expire');
 * </code>
 *
 * @param   int    $subscription_plan_id
 * @return  array  subscription plan details or FALSE on error
 */
function _archives_subscription_details($subscription_plan_id)
{
    $q  = "SELECT * FROM props_archives_subscription_plans "
        . "WHERE plan_id = $subscription_plan_id";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        return FALSE;
    }

    $row = sql_fetch_object($result);
    return array($row->description, $row->cost, $row->story_credits, $row->days_until_expire);
}

/**
 * Logs search string and keywords
 *
 * @param  string  $search_string
 * @param  string  $keywords
 */
function archives_log_search($search_string)
{
    $keywords = explode(' ', $search_string);

    // Log the search string
    $q  = "INSERT DELAYED INTO props_archives_searchlog_strings "
        . "SET search_string = '" . sql_escape_string($search_string) . "'";
    sql_query($q);

    // Log the keywords
    if ($keywords != NULL) {
        foreach($keywords as $keyword) {
            $q  = "INSERT DELAYED INTO props_archives_searchlog_keywords "
                . "SET keyword = '" . sql_escape_string($keyword) . "'";
            sql_query($q);
        }
    }

    // Roughly one out of every 100 times this function is called, trim the archives
    // searchlog tables to eliminate entries older than 90 days.  Someday, someone might
    // want to make the retention time a config parameter
    if (rand(1, 100) == 50) {
        $cutoff_timestamp = time() - (90 * 86400);  // 90 days times 86400 seconds per day

        $q  = "DELETE FROM props_archives_searchlog_strings "
            . "WHERE UNIX_TIMESTAMP(search_timestamp) < $cutoff_timestamp";
        sql_query($q);

        $q  = "DELETE FROM props_archives_searchlog_keywords "
            . "WHERE UNIX_TIMESTAMP(search_timestamp) < $cutoff_timestamp";
        sql_query($q);
    }
}

?>
