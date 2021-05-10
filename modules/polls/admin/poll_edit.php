<?php
/**
 * Admin function
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
 * @version     $Id: poll_edit.php,v 1.11 2007/12/11 15:46:31 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  Edit poll
 * @adminprivs  delete_poll  Delete poll
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_poll_edit()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'polls_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'poll_add');

    // Get the needed posted vars here.
    $poll_id = props_getrequest('poll_id', VALIDATE_INT);
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    $poll_active = props_getrequest('poll_active', VALIDATE_BOOL);
    $comments_enable = props_getrequest('comments_enable', VALIDATE_BOOL);
    $poll_question = props_getrequest('poll_question', VALIDATE_TEXT, 'MAX255,!EMPTY,SANITIZE');
    $poll_option_1 = props_getrequest('poll_option_1', VALIDATE_TEXT, 'MAX255,!EMPTY,SANITIZE');
    $poll_option_2 = props_getrequest('poll_option_2', VALIDATE_TEXT, 'MAX255,!EMPTY,SANITIZE');
    $poll_option_3 = props_getrequest('poll_option_3', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_4 = props_getrequest('poll_option_4', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_5 = props_getrequest('poll_option_5', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_6 = props_getrequest('poll_option_6', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_7 = props_getrequest('poll_option_7', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_8 = props_getrequest('poll_option_8', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_9 = props_getrequest('poll_option_9', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);
    $poll_option_10 = props_getrequest('poll_option_10', VALIDATE_TEXT, 'MAX255,SANITIZE', TRUE);

    // Get details from DB.
    $q  = "SELECT * from props_polls WHERE poll_id = $poll_id";
    $result = sql_query($q);
    $poll = sql_fetch_object($result);

    if (!sql_num_rows($result)) {
        props_error("Invalid ID.");
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
        exit;
    }

    $total_votes = (
        $poll->poll_option_1_votes +
        $poll->poll_option_2_votes +
        $poll->poll_option_3_votes +
        $poll->poll_option_4_votes +
        $poll->poll_option_5_votes +
        $poll->poll_option_6_votes +
        $poll->poll_option_7_votes +
        $poll->poll_option_8_votes +
        $poll->poll_option_9_votes +
        $poll->poll_option_10_votes);

    // prevent div by zero errors
    if ($total_votes == 0) $total_votes = 1;

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Delete"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'delete_poll') && empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // Delete comments associated with this poll
                $q  = "DELETE FROM props_polls_comments WHERE poll_id = $poll_id";
                sql_query($q);

                // Delete the poll
                $q  = "DELETE FROM props_polls WHERE poll_id = $poll_id";
                sql_query($q);

                // Redirect
                props_redirect(TRUE);

            } else {
                props_error("You do not have permission to perform the selected action.", PROPS_E_WARNING);
            }

            break;

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                $q  = "UPDATE props_polls SET "
                    . "section_id = $section_id, "
                    . "poll_active = " . (($poll_active) ? '1': '0') . ", "
                    . "comments_enable = " . (($comments_enable) ? '1': '0') . ", "
                    . "poll_question = '" . sql_escape_string($poll_question) . "', "
                    . "poll_option_1 = '" . sql_escape_string($poll_option_1) . "', "
                    . "poll_option_2 = '" . sql_escape_string($poll_option_2) . "', "
                    . "poll_option_3 = '" . sql_escape_string($poll_option_3) . "', "
                    . "poll_option_4 = '" . sql_escape_string($poll_option_4) . "', "
                    . "poll_option_5 = '" . sql_escape_string($poll_option_5) . "', "
                    . "poll_option_6 = '" . sql_escape_string($poll_option_6) . "', "
                    . "poll_option_7 = '" . sql_escape_string($poll_option_7) . "', "
                    . "poll_option_8 = '" . sql_escape_string($poll_option_8) . "', "
                    . "poll_option_9 = '" . sql_escape_string($poll_option_9) . "', "
                    . "poll_option_10 = '" . sql_escape_string($poll_option_10) . "' "
                    . "WHERE poll_id = $poll_id";
                sql_query($q);

                // Redirect
                props_redirect(TRUE);
            }
            break;

        default:
            $section_id = $poll->section_id;
            $poll_active = $poll->poll_active;
            $comments_enable = $poll->comments_enable;
            $poll_question = stripslashes($poll->poll_question);
            $poll_option_1 = stripslashes($poll->poll_option_1);
            $poll_option_2 = stripslashes($poll->poll_option_2);
            $poll_option_3 = stripslashes($poll->poll_option_3);
            $poll_option_4 = stripslashes($poll->poll_option_4);
            $poll_option_5 = stripslashes($poll->poll_option_5);
            $poll_option_6 = stripslashes($poll->poll_option_6);
            $poll_option_7 = stripslashes($poll->poll_option_7);
            $poll_option_8 = stripslashes($poll->poll_option_8);
            $poll_option_9 = stripslashes($poll->poll_option_9);
            $poll_option_10 = stripslashes($poll->poll_option_10);

            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<input name="poll_id" type="hidden" value="' . $poll_id . '" />'.LF

        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Section details") . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Section") . '</label></dt>'.LF
        .'    <dd>' . section_select($section_id) . '</dd>'.LF
        .((props_geterror('section_id')) ? '<dd>' . props_geterror('section_id') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Active") . '</label></dt>'.LF
        .'    <dd><input class="checkbox" type="checkbox" id="poll_active" name="poll_active" value="1" ' . ((!empty($poll_active)) ? 'checked="checked"': '') . ' /></dd>'.LF
        .((props_geterror('poll_active')) ? '<dd>' . props_geterror('poll_active') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Enable comments") . '</label></dt>'.LF
        .'    <dd><input class="checkbox" type="checkbox" id="comments_enable" name="comments_enable" value="1" ' . ((!empty($comments_enable)) ? 'checked="checked"': '') . ' /></dd>'.LF
        .((props_geterror('comments_enable')) ? '<dd>' . props_geterror('comments_enable') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Poll question") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="poll_question" name="poll_question" value="' . htmlspecialchars($poll_question) . '" /></dd>'.LF
        .((props_geterror('poll_question')) ? '<dd>' . props_geterror('poll_question') . '</dd>'.LF : '')
        .'  </dl>'.LF;

    for ($i = 1; $i <= 10; $i++) {
        $poll_option = 'poll_option_'.$i;
        $poll_option_votes = 'poll_option_'.$i.'_votes';
        $output .=
             '  <dl>'.LF
            .'    <dt><label>' . props_gettext("Option") . ' ' . $i . '</label></dt>'.LF
            .'    <dd><input class="medium" type="text" id="' . $poll_option . '" name="' . $poll_option . '" value="' . htmlspecialchars($$poll_option) . '" />&nbsp;'.LF
            .$poll->$poll_option_votes . ' ' . props_gettext("votes") . ' (' . round(($poll->$poll_option_votes / $total_votes) * 100, 1) . '%)</dd>'.LF
            .((props_geterror($poll_option)) ? '<dd>' . props_geterror($poll_option) . '</dd>'.LF : '')
            .'  </dl>'.LF;
    }

    $output .=
         '</fieldset>'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF;
    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'delete_poll')) {
        $output .= '  <input class="button" name="op" type="submit" value="' . props_gettext("Delete") . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');" />&nbsp;&nbsp;'.LF;
    }
    $output .=
         '  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
