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
 * @version     $Id: manage_threadcodes.php,v 1.12 2007/12/11 15:46:29 roufneck Exp $
 */

/**
 * @admintitle  Thread codes
 * @adminprivs  threadcodes_delete  Delete thread codes
 * @adminnav    5
 * @return  string  admin screen html content
 */
function admin_manage_threadcodes()
{
    $threadcode_id = props_getrequest('threadcode_id', VALIDATE_INT);
    $tag = props_getrequest('tag', VALIDATE_TEXT);
    $position = props_getrequest('position', VALIDATE_INT);

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_GET['op'])) ? $_GET['op'] : '';
    switch($op) {

        case 'delete':
            // Check pageID. An extra check against URL hacking.
            if (!isset($_GET['pageID']) || ($_GET['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'threadcodes_delete')) {
                props_error("You do not have permission to perform the selected action.", PROPS_E_WARNING);
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                sql_query("DELETE FROM props_threadcodes_stories_xref WHERE threadcode_id = $threadcode_id");
                sql_query("DELETE FROM props_threadcodes WHERE threadcode_id = $threadcode_id");

                props_error('Threadcode deleted.');
            }
            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<p>'.LF
        .'<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '">'.props_gettext("All").'</a>'.LF;

    // Construct alphabet list
    $search_array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    foreach ($search_array as $key => $value) {
        $output .=
             '&nbsp;<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;tag='.$value.'">'.ucfirst($value).'</a>'.LF;
    }

    $q  = "SELECT t1.*, COUNT(t2.story_id) AS story_count FROM props_threadcodes AS t1 "
        . "  LEFT JOIN props_threadcodes_stories_xref AS t2 "
        . "  ON t1.threadcode_id = t2.threadcode_id ";

    if ($tag) {
        // Search in username and email_address
        $q .= "WHERE threadcode LIKE '" . sql_escape_string($tag) . "%' ";
    }

    $q .= "GROUP BY t1.threadcode_id ";

    // Get total results
    $result = sql_query($q);
    $result_rows = sql_num_rows($result);
    if (!$position) {
        $position = 0;
    }

    // Construct page navigation
    $pagination = props_pagination($result_rows, $position, 25, './?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;tag='.$tag);

    // Get results for this page
    $q .= "LIMIT $position, 25";
    $result = sql_query($q);

    $output .=
         '&nbsp;<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '">#</a>'.LF
        .'</p>'.LF
        .'<p>'.sprintf(props_gettext("%s results found"), $result_rows).' - '.$pagination.'</p>'.LF

        .'<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Thread code") . '</th>'.LF
        .'    <th>' . props_gettext("Stories") . '</th>'.LF
        .'    <th>' . props_gettext("Actions") . '</th>'.LF
        .'  </tr>'.LF;

    if (!$result_rows) {
        $output .= '  <tr class="row1"><td colspan="5" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
    } else {
        $row_num = 1;
        while ($row = sql_fetch_object($result)) {
            $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td>' . htmlspecialchars($row->threadcode) . '</td>'.LF
                .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=storysearch&amp;include[]=threadcode&amp;threadcode[]=' . $row->threadcode . '&amp;op=search" title="' . props_gettext("View stories") . '">' . $row->story_count . '</a></td>'.LF
                .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;op=delete&amp;threadcode_id=' . $row->threadcode_id . '&amp;pageID=' . props_pageID() . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');" >[' . props_gettext("Delete") . ']</a></td>'.LF
                .'  </tr>'.LF;
             $row_num = ($row_num == 1) ? 2 : 1;
         }
    }

    $output .=
         '</table>'.LF;

    return $output;
}

?>
