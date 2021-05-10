<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  admincontent
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
 * @version     $Id: story_edit_comments.php,v 1.9 2007/12/11 15:46:29 roufneck Exp $
 */

/**
 * @admintitle  Edit story comments
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_story_edit_comments()
{
    $story_id = props_getrequest('story_id', VALIDATE_INT);
    $comment_id = props_getrequest('comment_id', VALIDATE_INT);
    $op = props_getrequest('op');
    $pageID = props_getrequest('pageID');

    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'storysearch');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'story_edit', '&amp;story_id='.$story_id);

    // Handle form submissions here
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case 'delete_comment':

            // Check pageID. An extra check against URL hacking.
            if ($pageID != $_SESSION['pageID']) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                $q  = "UPDATE props_stories_comments "
                    . "SET deleted = 1, "
                    . "timestamp = timestamp "
                    . "WHERE story_id = $story_id "
                    . "AND comment_id = $comment_id";
                sql_query($q);
            }
            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Username") . '</th>'.LF
        .'    <th>' . props_gettext("Comment") . '</th>'.LF
        .'    <th>' . props_gettext("Actions") . '</th>'.LF
        .'  </tr>'.LF;

    // Output a list of comments for the specified poll
    $q  = "SELECT t1.*, t2.username, t2.user_type FROM props_stories_comments AS t1 "
        . "LEFT JOIN props_users AS t2 "
        . "ON t1.user_id = t2.user_id "
        . "WHERE story_id = $story_id "
        . "AND (t1.user_id = t2.user_id OR t1.user_id = 0) "
        . "ORDER BY comment_id ASC";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
            $output .= '  <tr class="row1"><td style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
    } else {
         $row_num = 1;
         while ($row = sql_fetch_object($result)) {
            if ($row->user_id == 0) {
                $username = '<span class="usertype_guest">'.props_gettext("Guest").'</span>';
            } else {
                switch ($row->user_type) {
                    case PROPS_USERTYPE_FOUNDER: $class = 'usertype_founder'; break;
                    case PROPS_USERTYPE_ADMIN: $usertype = 'usertype_admin'; break;
                    case PROPS_USERTYPE_USER: $usertype = 'usertype_user'; break;
                    case PROPS_USERTYPE_BOT: $usertype = 'usertype_bot'; break;
                    case PROPS_USERTYPE_CLOSED: $usertype = 'usertype_closed'; break;
                    default: $usertype = 'usertype_guest'; break;
                }
                $username =
                     '<span class="'.$class.'">'
                    .'<a href="./?module=users&amp;function=user_edit&amp;user_id=' . $row->user_id . '">' . htmlspecialchars($row->username) . '</a>'
                    .'</span>';
            }
            $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td>' . $username . '</td>'.LF
                .'    <td ' . (($row->deleted) ? 'style="color:red; text-decoration: line-through;"' : '') . '>' . htmlspecialchars($row->bodytext) . '</td>'.LF
                .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;op=delete_comment&amp;story_id=' . $story_id . '&amp;comment_id=' . $row->comment_id . '&amp;pageID=' . props_pageID() . '">[' . props_gettext("Delete") . ']</td>'.LF
                .'  </tr>'.LF;
            $row_num = ($row_num == 1) ? 2 : 1;
        }
    }

    $output .=
         '</table>'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
