<?php
/**
 * Module functions
 *
 * @package     modules
 * @subpackage  displaystory
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
 * @version     $Id: requesthandler.php,v 1.21 2007/11/14 11:19:03 roufneck Exp $
 *
 * @userprivs   add_comment  Add story comment
 */

// loadLibs
props_loadLib('stories,archives');

// Sanitize the provided story_id to guard against malicious URL hacking
$story_id = props_getrequest('story_id', VALIDATE_INT);

// Load the story detail into the glossary
story_to_registry($story_id);

// Trap 'command' request parameter
switch (props_getkey('request.cmd')) {

    case 'displaystory-email':

        if (props_getkey('story.access_level') == ACCESS_PAID_ARCHIVES) {
            props_error("You cannot send paid archive content via email.");
            props_setkey('request.cmd', 'displaystory');
            rh_displaystory();
            break;
        }

        rh_displaystory_email();
        break;

    case 'displaystory-comment':
        rh_displaystory_comment();
        break;

    default:
        rh_displaystory();
        break;
}

/**
 * Handles POST/GET cmd=displaystory
 */
function rh_displaystory()
{
    // Check valid access
    if (stories_check_access_level()) {
        return;
    }
}

/**
 * Handles POST/GET cmd=displaystory-email
 */
function rh_displaystory_email()
{
    // Check valid access
    if (!stories_check_access_level()) {
        return;
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

    $to_email = props_getrequest('to_email', VALIDATE_EMAIL, '!EMPTY');
    $from_email = props_getrequest('from_email', VALIDATE_EMAIL, '!EMPTY');
    $from_name = props_getrequest('from_name', VALIDATE_NAME, '!EMPTY');
    $comments = props_getrequest('comments', VALIDATE_TEXT);

    // If no errors, do update, otherwise drop through and display errors
    if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
        // Load PHPmailer
        props_loadLib('sendmail,url');

        // Assemble the email
        $mail = new props_sendmail();

        // Set headers and body text
        $mail->Sender   = $from_email;
        $mail->From     = $from_email;
        $mail->FromName = $from_name;
        $mail->Subject  = props_getkey('config.publication.name').': '.props_getkey('story.headline');
        $mail->AddReplyTo($from_email, $from_name);

        if (!empty($comments)) {
            $comments = LF.props_gettext("Comments").': '.$comments.LF;
        }

        $urlargs = array ('cmd' => 'displaystory', 'story_id' => props_getkey('story.story_id'));
        $story_url = genurl($urlargs);

        $message_body =
             '<p>'.sprintf(props_gettext("%s sent you the following story from %s:"), $from_email, '<a href="'.props_getkey('config.url.root').'">'.props_getkey('config.publication.name').'</a>').'</p>'.LF
            .'<p>'.$comments.'</p>'.LF
            .'<hr />'.LF
            .strftime('%x', strtotime(props_getkey('story.published_stamp'))).LF.LF
            .'<h1>'.props_getkey('story.headline').'</h1>'.LF
            .((props_getkey('story.subhead')) ? '<h2>'.props_getkey('story.subhead').'</h2>'.LF : '')
            .'<p>'.props_getkey('story.abstract').'</p>'.LF
            .'<p>'.sprintf(props_gettext("You can view the full story %s."), '<a href="'.$story_url.'">' . props_gettext("here") . '</a>').'</p>'.LF;

        $mail->IsHTML(true);
        $mail->Body = $message_body;

        $message_body =
             sprintf(props_gettext("%s sent you the following story from %s:"), $from_email, props_getkey('config.publication.name') . ' (' . props_getkey('config.url.root').')').LF
            .$comments
            .'------------------------------------------------'.LF.LF
            .strftime('%x', strtotime(props_getkey('story.published_stamp'))).LF.LF
            .props_getkey('story.headline').LF
            .((props_getkey('story.subhead')) ? props_getkey('story.subhead').LF : '')
            .LF.props_getkey('story.abstract').LF.LF
            .sprintf(props_gettext("You can view the full story at %s."), $story_url).LF;

        $mail->AltBody = $message_body;

        $mail->AddAddress($to_email);

        if($mail->Send()) {
            props_error(sprintf(props_gettext("An email has been send to %s."), $to_email));
            props_setkey('request.cmd', 'displaystory');
        } else {
            $GLOBALS['PROPS_ERRORDESC'] = props_gettext("Error sending email.");
            trigger_error($mail->ErrorInfo, E_USER_WARNING);
        }
    }

    return;
}

/**
 * Handles POST/GET cmd=displaystory-comment
 */
function rh_displaystory_comment()
{
    // Check valid access
    if (!stories_check_access_level()) {
        return;
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

    $comment = props_getrequest('comment', VALIDATE_TEXT, 'SANITIZE,!EMPTY,MAX1024');
    $comment = props_censor_text($comment);
    $story_id = props_getrequest('story_id', VALIDATE_INT);

    if (props_getkey('story.comments_allowed') != TRUE || !user_has_priv('displaystory', 'add_comment')) {
        props_error("You cannot add comments for this poll.");
        return;
    }

    // If no errors, process request
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error("Please correct the errors first.");
        return;
    }

    // Only do the insert if it's not a dupe.
    $q  = "SELECT comment_id FROM props_stories_comments "
        . "WHERE story_id = $story_id "
        . "AND user_id = " . $_SESSION['PROPS_USER']['user_id'] . " "
        . "AND bodytext = '" . sql_escape_string($comment) . "'";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        // Insert the comment
        $q  = "INSERT INTO props_stories_comments "
            . "SET story_id = $story_id, "
            . "user_id = " . $_SESSION['PROPS_USER']['user_id'] . ", "
            . "timestamp = NOW(), "
            . "bodytext = '" . sql_escape_string($comment) . "'";
        sql_query($q);

        props_error("Your comment has been added.");
        props_setkey('request.comment', '');
    } else {
        props_error("You already added the same comment.");
    }

    return;
}

?>
