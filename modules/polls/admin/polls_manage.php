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
 * @version     $Id: polls_manage.php,v 1.8 2007/12/11 15:46:31 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  Manage polls
 * @adminnav    1
 * @return  string  admin screen html content
 */
function admin_polls_manage()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'polls_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'poll_add');

    // Set referer
    props_redirect(FALSE, 'set');

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("ID") . '</th>'.LF
        .'    <th>' . props_gettext("Question") . '</th>'.LF
        .'    <th>' . props_gettext("Active") . '</th>'.LF
        .'    <th>' . props_gettext("Section") . '</th>'.LF
        .'    <th>' . props_gettext("Votes") . '</th>'.LF
        .'    <th colspan="2">' . props_gettext("Comments") . '</th>'.LF
        .'    <th>' . props_gettext("Actions") . '</th>'.LF
        .'  </tr>'.LF;

    // List all polls in system
    $q  = "SELECT t1.*, "
        . "(poll_option_1_votes + poll_option_2_votes + poll_option_3_votes + "
        . " poll_option_4_votes + poll_option_5_votes + poll_option_6_votes + "
        . " poll_option_7_votes + poll_option_8_votes + poll_option_9_votes + "
        . " poll_option_10_votes) AS votes, "
        . "COUNT(t2.poll_id) AS comments "
        . "FROM props_polls AS t1 "
        . "LEFT JOIN props_polls_comments AS t2 ON t2.poll_id = t1.poll_id "
        . "GROUP BY t1.poll_id "
        . "ORDER BY t1.poll_id DESC";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        $output .= '  <tr class="row1"><td colspan="6" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
    } else {
        $row_num = 1;
        while ($row = sql_fetch_object($result)) {
            $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td style="text-align: center;">' . $row->poll_id . '</td>'.LF
                .'    <td><a href="?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=poll_edit&amp;poll_id=' . $row->poll_id . '">' . htmlspecialchars($row->poll_question) . '</a></td>'.LF
                .'    <td>' . (($row->poll_active) ? props_gettext("Yes"): props_gettext("No")) . '</td>'.LF
                .'    <td>' . htmlspecialchars(section_fullname($row->section_id)) . '</td>'.LF
                .'    <td style="text-align: center;">' . $row->votes . '</td>'.LF
                .'    <td style="text-align: center;">' . (($row->comments_enable) ? props_gettext("Enabled") : props_gettext("Disabled")) . '</td>'.LF
                .'    <td style="text-align: center;"><a href="?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=poll_edit_comments&amp;poll_id=' . $row->poll_id . '">' . $row->comments . '</a></td>'.LF
                .'    <td style="text-align: center;"><a href="?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=poll_edit&amp;poll_id=' . $row->poll_id . '">[' . props_gettext("Edit") . ']</a></td>'.LF
                .'  </tr>'.LF;
            $row_num = ($row_num == 1) ? 2 : 1;
        }
    }

    $output .=
         '</table>'.LF;

    return $output;
}

?>
