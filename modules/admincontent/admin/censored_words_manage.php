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
 * @version     $Id: censored_words_manage.php,v 1.4 2007/12/11 15:46:28 roufneck Exp $
 */

/**
 * @admintitle  Censored words
 * @adminprivs  censored_words_delete  Delete censored words
 * @adminnav    6
 * @return  string  admin screen html content
 */
function admin_censored_words_manage()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'censored_words_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'censored_words_add');

    // Set referer
    props_redirect(FALSE, 'set');

    $censored_id = props_getrequest('censored_id', VALIDATE_INT);
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

            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'censored_words_delete')) {
                props_error("You do not have permission to perform the selected action.", PROPS_E_WARNING);
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                sql_query("DELETE FROM props_censored_words WHERE censored_id = $censored_id");
                props_error('Record is deleted.');
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

    $q  = "SELECT * FROM props_censored_words ";

    if ($tag) {
        // Search in username and email_address
        $q .= "WHERE pattern LIKE '" . sql_escape_string($tag) . "%' "
            . "  OR pattern LIKE '*" . sql_escape_string($tag) . "%' ";
    }

    // Get total results
    $result = sql_query($q);
    $result_rows = sql_num_rows($result);
    if (!$position) {
        $position = 0;
    }

    // Construct page navigation
    $pagination = props_pagination($result_rows, $position, 50, './?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;tag='.$tag);

    // Get results for this page
    $q .= "LIMIT $position, 50";
    $result = sql_query($q);

    $output .=
         '&nbsp;<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '">#</a>'.LF
        .'</p>'.LF
        .'<p>'.sprintf(props_gettext("%s results found"), $result_rows).' - '.$pagination.'</p>'.LF

        .'<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Pattern") . '</th>'.LF
        .'    <th>' . props_gettext("Replacement") . '</th>'.LF
        .'    <th colspan="2">' . props_gettext("Actions") . '</th>'.LF
        .'  </tr>'.LF;

    if (!$result_rows) {
        $output .= '  <tr class="row1"><td colspan="5" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
    } else {
        $row_num = 1;
        while ($row = sql_fetch_object($result)) {
            $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td>' . htmlspecialchars($row->pattern) . '</td>'.LF
                .'    <td>' . htmlspecialchars($row->replacement) . '</td>'.LF
                .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=censored_words_edit&amp;censored_id=' . $row->censored_id . '">[' . props_gettext("Edit") . ']</a></td>'.LF
                .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;op=delete&amp;censored_id=' . $row->censored_id . '&amp;pageID=' . props_pageID() . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');">[' . props_gettext("Delete") . ']</a></td>'.LF
                .'  </tr>'.LF;
             $row_num = ($row_num == 1) ? 2 : 1;
         }
    }

    $output .=
         '</table>'.LF;

    return $output;
}

?>
