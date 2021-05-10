<?php
/**
 * Module functions
 *
 * @package     modules
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
 * @version     $Id: requesthandler.php,v 1.19 2007/12/11 15:46:29 roufneck Exp $
 *
 * @userprivs   free_access  Free access to paid archives
 */

// loadLibs
props_loadLib('archives');

// Trap 'command' request parameter
switch (props_getkey('request.cmd')) {

    case 'archives-search':
        rh_archives_search();
        break;

    case 'archives-confirm_credit':
        rh_archives_confirm_credit();
        break;

    case 'archives-purchase_content':
        rh_archives_purchase_content();
        break;
}

/**
 * Handles POST/GET cmd=archives-search
 */
function rh_archives_search()
{
    // Populate glossary with info about the search. Sanitize all URL variables.
    $position = props_getrequest('position', VALIDATE_INT);  // force to int value
    props_setkey('archives.daterange_selected', props_getrequest('date_range'));
    props_setkey('archives.sortorder_selected', props_getrequest('archives_sortorder'));

    $search_string = props_getrequest('search_string', VALIDATE_TEXT, 'SANITIZE');
    $search_string = htmlspecialchars_decode($search_string);

    props_setkey('archives.search_string', $search_string);
    props_setkey('request.search_string', $search_string);

    // Determine type of date range restriction, if any
    $date_range = explode(' ', props_getkey('archives.daterange_selected'), 2);
    $date_range_type = (isset($date_range[0])) ? ereg_replace('[^A-Z]', '', $date_range[0]) : '';
    $date_range_value = (isset($date_range[1])) ? intval($date_range[1]) : '';

    // Set the results order based upon the user's selection
    if (props_getkey('archives.sortorder_selected') == 'most_recent') {
        $sql_order = 'published_stamp DESC, score DESC';
    } else {
        $sql_order = 'score DESC, published_stamp DESC';
    }

    // If no errors, process request
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        return;
    }

    // SQL escape search string
    $search_string_sql = sql_escape_string($search_string);

    if (empty($search_string)) {
        $q  = "SELECT story_id, section_id, headline, subhead, byline_prefix, byline_name, byline_suffix, abstract, published_stamp, access_level "
            . "FROM props_stories "
            . "WHERE publication_status_id IN (" . PUBSTATUS_ARCHIVED . "," . PUBSTATUS_PUBLISHED . ") "
            . "AND approved = 1 ";
        if ($date_range_type == "R") {
            $q .= " AND DATE_SUB(NOW(), INTERVAL $date_range_value DAY) <= published_stamp ";
        } elseif ($date_range_type == "Y") {
            $q .= " AND YEAR(published_stamp) = '$date_range_value' ";
        }
        $q .= "ORDER BY published_stamp DESC ";
    } else {
        $q  = "SELECT story_id, section_id, headline, subhead, byline_prefix, byline_name, byline_suffix, abstract, published_stamp, access_level, "
            . "((MATCH (headline) AGAINST ('$search_string_sql'))*5 + "
            . " (MATCH (body_content) AGAINST ('$search_string_sql'))*2 + "
            . " (MATCH (end_content) AGAINST ('$search_string_sql')) ) AS score "
            . "FROM props_stories "
            . "WHERE publication_status_id IN (" . PUBSTATUS_ARCHIVED . "," . PUBSTATUS_PUBLISHED . ") "
            . "AND approved = 1 "
            . "AND (MATCH (headline) AGAINST ('$search_string_sql') "
            . "OR MATCH (body_content) AGAINST ('$search_string_sql') "
            . "OR MATCH (end_content) AGAINST ('$search_string_sql')) ";
        if ($date_range_type == "R") {
            $q .= " AND DATE_SUB(NOW(), INTERVAL $date_range_value DAY) <= published_stamp ";
        } elseif ($date_range_type == "Y") {
            $q .= " AND YEAR(published_stamp) = '$date_range_value' ";
        }
        $q .= "ORDER BY $sql_order ";
    }

    // Get total results
    $result = sql_query($q);
    $num_results = sql_num_rows($result);

    // Free resources
    sql_free_result($result);

    $max_results = 10;

    if (empty($position)) {
        $position = 0;
    }

    $current_page = 1 + ceil(($position) / $max_results);

    $num_pages = 1 + floor($num_results / $max_results);

    $prev_page = $position - $max_results;
    $prev_page = ($prev_page >= 0) ? $prev_page : NULL;

    $next_page = $position + $max_results;
    $next_page = ($next_page < $num_results) ? $next_page : '';

    // Set results properties
    props_setkey('archives.stories_per_page', $max_results);
    props_setkey('archives.num_results', $num_results);
    props_setkey('archives.num_pages', $num_pages);
    props_setkey('archives.current_page', $current_page);
    props_setkey('archives.prev_page', $prev_page);
    props_setkey('archives.next_page', $next_page);

    // Get results for this page
    $q .= "LIMIT $position, $max_results";
    props_setkey('archives.query_handle', sql_query($q));

    // If this is the very first page of results, log the search
    if ($current_page == 1) {
        archives_log_search($search_string);
    }

    return;
}

/**
 * Handles POST/GET cmd=archives-confirm_credit
 */
function rh_archives_confirm_credit()
{
    // User must be logged in
    if (!user_is_logged_in()) {
        // Reset request data and kick user to the login screen
        props_error("Login is required to access this page.");
        props_setkey('request.template', '');
        props_setkey('request.cmd', 'users-login');
        return FALSE;
    }

    // loadLibs
    props_loadLib('stories');

    // Sanitize the provided story_id to guard against malicious URL hacking
    $story_id = props_getrequest('story_id', VALIDATE_INT);

    // Load the story detail into the glossary
    story_to_registry($story_id);

    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // No form post, do nothing
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    // Deduct credits
    $status = archives_purchase_story($_SESSION['PROPS_USER']['user_id'], $story_id);

    if ($status === TRUE) {
        // If success, content is purchased
        props_error("Content purchased.");
        props_setkey('request.cmd', 'displaystory');
        return TRUE;
    } elseif ($status === NULL) {
        // Content is already purchased
        props_setkey('request.cmd', 'displaystory');
        return TRUE;
    } elseif ($status === -1) {
        // Not enough credits left
        props_error("Not enough credits left.");
        // Kick to purchase page
        props_setkey('request.cmd', 'archives-purchase_content');
        return FALSE;
    }

    // Else show error / pay for content page
    return FALSE;
}

/**
 * Handles POST/GET cmd=archives-purchase_content
 */
function rh_archives_purchase_content()
{
    // User must be logged in
    if (!user_is_logged_in()) {
        // Reset request data and kick user to the login screen
        props_error("Login is required to access this page.");
        props_setkey('request.template', '');
        props_setkey('request.cmd', 'users-login');
        return FALSE;
    }

    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // No form post, do nothing
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    $cc_name = props_getrequest('cc_name', VALIDATE_TEXT, '!EMPTY');
    $cc_number = props_getrequest('cc_number', VALIDATE_NUM, '!EMPTY');
    $cc_exp_month = props_getrequest('cc_exp_month', VALIDATE_NUM, '!EMPTY');
    $cc_exp_year = props_getrequest('cc_exp_year', VALIDATE_NUM, '!EMPTY');
    $cc_type = props_getrequest('cc_type', VALIDATE_ALPHA, '!EMPTY');
    $subscription_plan_id = props_getrequest('subscription_plan_id', VALIDATE_INT, '!EMPTY');

    if (strtotime("$cc_exp_year-$cc_exp_month-01") <= time()) {
        $GLOBALS['PROPS_ERRORSTACK']['cc_exp_month']['message'] = props_gettext("Your credit card has expired.");
    }

    // If no errors, process request
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        return FALSE;
    }

    $q  = "SELECT * FROM props_archives_subscription_plans "
        . "WHERE plan_id = $subscription_plan_id";
    $result = sql_query($q);
    $subscription_plan = sql_fetch_object($result);
    if ($subscription_plan == FALSE) {
        $GLOBALS['PROPS_ERRORSTACK']['subscription_plan_id']['message'] = props_gettext("Invalid ID.");
        return FALSE;
    }

    // loadLibs
    props_loadLib('commerce');

    // Calc expiration date/time
    if ($subscription_plan->days_until_expire == 0) {
        // This plan does not expire
        $plan_expire = '0000-00-00 00:00';
    } else {
        // Calculate expiration time (86400 seconds = 1 day)
        $plan_expire = date("Y-m-d H:i:00", time() + ($subscription_plan->days_until_expire * 86400));
    }

    // Run the credit card transaction
    $result = cc_transaction($cc_name, $cc_number, $cc_exp_month, $cc_exp_year, $subscription_plan->amount,
        sprintf(props_gettext("Archives subscription renewal for user %s"), $_SESSION['PROPS_USER']['username']));

    // Was transaction successful?
    if (isset($result['approved']) && $result['approved'] == TRUE) {

        // Delete existing subscription info for this user
        $q  = "DELETE FROM props_users_archive_credits "
            . "WHERE user_id = ".$_SESSION['PROPS_USER']['user_id'];
        sql_query($q);

        // Add the credits to the user's account, according to the plan selected
        $q  = "INSERT INTO props_users_archive_credits "
            . "SET user_id = ".$_SESSION['PROPS_USER']['user_id'].", "
            . "credits = '".$subscription_plan->credits."', "
            . "expire = '$plan_expire'";
        sql_query($q);

        // Log the transaction
        cc_log_transaction($subscription_plan->description, $subscription_plan->amount, $result['reference_id']);

        props_error(props_gettext("Transaction was successful.").' '.sprintf(props_gettext("Your credit card has been charged for the amount of %s."), '$'.sprintf("%0.2f", $subscription_plan->amount)));

        // Load libs
        props_loadLib('sendmail');

        // Assemble the email
        $mail = new props_sendmail();

        $credits = ($subscription_plan->credits == '-1') ? strtolower(props_gettext("unlimited")) : $subscription_plan->credits;
        if ($plan_expire == '0000-00-00 00:00') {
            $purchased_line = sprintf(props_gettext("You have purchased %s story credit(s) which do not expire."), $credits);
        } else {
            $purchased_line = sprintf(props_gettext("You have purchased %s story credit(s) which are valid until %s."), $credits, strftime("%c", strtotime($plan_expire)));
        }

        if (isset($_SESSION['PROPS_USER']['fullname']) && !empty($_SESSION['PROPS_USER']['fullname'])) {
            $to_name = $_SESSION['PROPS_USER']['fullname'];
        } else {
            $to_name = $_SESSION['PROPS_USER']['username'];
        }

        // Set headers and body text
        $mail->Sender   = props_getkey('config.mail.bounce_address');
        $mail->From     = props_getkey('config.mail.from_address');
        $mail->FromName = props_getkey('config.mail.from_name');
        $mail->Subject  = props_gettext("Transaction confirmation from").' '.props_getkey('config.publication.name');
        $mail->AddReplyTo(props_getkey('config.mail.from_address'), props_getkey('config.mail.from_name'));
        $mail->AddAddress($_SESSION['PROPS_USER']['email_address'], $to_name);
        $mail->Body  =
              $to_name .",".LF
            . sprintf(props_gettext("This email has been sent from %s."), props_getkey('config.url.root')).LF.LF
            . sprintf(props_gettext("You have received this email because you did a financial transaction on %s."), props_getkey('config.publication.name')).LF.LF
            . "------------------------------------------------".LF
            . props_gettext("Thank you for adding additional credits to your account.").LF
            . $purchased_line.LF
            . sprintf(props_gettext("Your credit card has been charged for the amount of %s."), '$'.sprintf("%0.2f", $subscription_plan->amount)).LF.LF
            . props_gettext("Regards").','.LF.LF
            . props_getkey('config.mail.from_name').LF
            . props_getkey('config.url.root').LF;
        $mail->WordWrap = '72';

        if(!$mail->Send()) {
            trigger_error(props_gettext("Error sending email") . ': ' . $mail->ErrorInfo, E_USER_WARNING);
        }

        // loadLibs
        props_loadLib('stories');

        // Sanitize the provided story_id to guard against malicious URL hacking
        $story_id = props_getrequest('story_id', VALIDATE_INT);

        // Load the story detail into the glossary
        story_to_registry($story_id);

        props_setkey('request.template', '');
        props_setkey('request.cmd', 'displaystory');

        return TRUE;

    } else {
        props_error(sprintf(props_gettext("Received error %s from credit card authorization gateway: %s"), $result["result_code"], $result["result_message"]));
        props_setkey('request.template', '');
        props_setkey('request.cmd', 'archives-transaction_error');

        return FALSE;
    }
}

?>
