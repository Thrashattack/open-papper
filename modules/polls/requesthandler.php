<?php
/**
 * Module functions
 *
 * @package     modules
 * @subpackage  polls
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
 * @version     $Id: requesthandler.php,v 1.25 2007/11/14 11:19:04 roufneck Exp $
 *
 * @userprivs   add_comment  Add poll comment
 */

// Sanitize the provided poll_id to guard against malicious URL hacking
props_setkey('request.poll_id', props_getrequest('poll_id', VALIDATE_INT));

// Populate the glossary with information about this poll
$q  = "SELECT * FROM props_polls "
    . "WHERE poll_id = " . props_getkey('request.poll_id');
$result = sql_query($q);

// Don't allow comments yet
props_setkey('polls.comments_allowed', FALSE);

if (sql_num_rows($result)) {
    $row = sql_fetch_assoc($result);
    props_setkey('polls', $row);
    props_setkey('request.section_id', $row['section_id']);

    // Enable comments if poll is active, commetns are enabled and user has comment privs
    if (props_getkey('polls.poll_active') == TRUE
        && props_getkey('polls.comments_enable') == TRUE
        && user_has_priv('polls', 'add_comment'))
    {
        // User has comment privs
        props_setkey('polls.comments_allowed', TRUE);
    }
} else {
    // invalid poll_id, so use the front page section
    props_setkey('request.section_id', FRONTPAGE_SECTION_ID);
    props_setkey('request.poll_id', -1);
    props_error("Invalid poll id.");
}

// Trap 'command' request parameter
switch (props_getkey('request.cmd')) {

    case 'polls-vote':
        rh_polls_vote();
        break;

    case 'polls-comment':
        rh_polls_comment();
        break;
}

/**
 * Handles POST/GET cmd=polls-vote
 *
 * Redirects to polls.html template
 */
function rh_polls_vote()
{
    // Sanitize the selected poll option to guard against malicious form hacking
    $poll_option = props_getrequest('poll_option', VALIDATE_INT);
    $poll_id = props_getkey('request.poll_id');

    // Check if a user voted for this poll
    if (isset($_SESSION['PROPS_POLL'][$poll_id])
        || isset($_COOKIE['PROPS_POLL_'.$poll_id])) {
        props_error("You already voted for this poll.");
        return;
    }

    // If no option was selected (or option out of range) redirect to error page
    if (($poll_option < 1) || ($poll_option > 10)) {
        props_error("No valid poll option selected.");
        return;
    }

    // If no errors, process request
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error("Please correct the errors first.");
        return;
    }

    // Record vote
    $q  = "UPDATE props_polls SET "
        . "poll_option_" . $poll_option . "_votes = poll_option_" . $poll_option . "_votes + 1 "
        . "WHERE poll_id = $poll_id";
    sql_query($q);

    // Set cookie
    $expire = time()+60*60*24*360; // 360 days
    setcookie('PROPS_POLL_'.$poll_id, TRUE, $expire);
    // Record to session as well
    $_SESSION['PROPS_POLL'][$poll_id] = TRUE;

    // Update poll details
    props_setkey('polls.poll_option_'.$poll_option.'_votes', props_getkey('polls.poll_option_'.$poll_option.'_votes') + 1);

    return;
}

/**
 * Handles POST/GET cmd=polls-comment
 *
 * Redirects to polls.html template
 */
function rh_polls_comment()
{
    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // No form post, do nothing
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    $comment = props_getrequest('comment', VALIDATE_TEXT, 'SANITIZE,!EMPTY,MAX1024');
    $comment = props_censor_text($comment);
    $poll_id = props_getkey('request.poll_id');

    if (props_getkey('polls.comments_allowed') != TRUE || !user_has_priv('polls', 'add_comment')) {
        props_error("You cannot add comments for this poll.");
        return;
    }

    // If no errors, process request
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error("Please correct the errors first.");
        return;
    }

    // Only do the insert if it's not a dupe.
    $q  = "SELECT comment_id FROM props_polls_comments "
        . "WHERE poll_id = $poll_id "
        . "AND user_id = " . $_SESSION['PROPS_USER']['user_id'] . " "
        . "AND bodytext = '" . sql_escape_string($comment) . "'";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        // Insert the comment
        $q  = "INSERT INTO props_polls_comments "
            . "SET poll_id = $poll_id, "
            . "user_id = " . $_SESSION['PROPS_USER']['user_id'] . ", "
            . "timestamp = NOW(), "
            . "bodytext = '" . sql_escape_string($comment) . "'";
        sql_query($q);
    } else {
        props_error("You already added the same comment.");
    }

    return;
}

?>
