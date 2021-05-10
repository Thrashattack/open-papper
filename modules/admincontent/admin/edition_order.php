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
 * @version     $Id: edition_order.php,v 1.10 2007/12/11 15:46:28 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  Order edition
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_edition_order()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'editions_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'edition_add');

    // Get the needed posted vars here.
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    $stories = props_getrequest('stories', VALIDATE_ARRAY);

    props_redirect(FALSE, 'set');

    // Get details from DB.
    $q  = "SELECT label FROM props_editions  "
        . "WHERE edition_id = $edition_id ";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    if (!sql_num_rows($result)) {
        props_error("Invalid ID.");
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
        exit;
    }

    // Page title.
    $GLOBALS['PROPS_PAGETITLE'] = props_gettext("Order sections for edition") . ' #'.$edition_id;
    if ($row->label) {
        $GLOBALS['PROPS_PAGETITLE'] = " ($row->label)";
    }

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
        case props_gettext("Done"):
            // Redirect
            props_redirect('goto', array('function'=>'editions_manage'));
            break;

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                foreach($stories as $story_id => $weight) {
                    $q  = "UPDATE props_stories ";
                    $q .= "SET weight = " . intval($weight) . " ";
                    $q .= "WHERE story_id = $story_id ";
                    $q .= "AND edition_id = $edition_id";
                    sql_query($q);
                }

                //  This flag indicates that the "Done" button should be output
                //  in place of the "Cancel" button
                $use_done_button = TRUE;
            }
            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<p>' . props_gettext("Assign a weight to each story below (100 = hot story, 10 = low priority), then click update.") . '</p>'.LF
        .'<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<input name="edition_id" type="hidden" value="' . $edition_id . '" />'.LF
        .'<table>'.LF
        . display_section_hierarchy()
        .'</table>'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . ((isset($use_done_button)) ? props_gettext("Done") : props_gettext("Cancel")) . '" />&nbsp;&nbsp;'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    return $output;
}

/**
 * This function recurses through all sections in tree order,
 * displaying table rows for all stories within each section
 * @access  private
 */
function display_section_hierarchy($current_parent = 0, $current_depth = 0)
{
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);

    static $hierarchy;

    foreach ($GLOBALS['PROPS_SECTIONS'] as $sid => $value) {
        if ($GLOBALS['PROPS_SECTIONS'][$sid]['parent_id'] == $current_parent) {
            // Output heading row
            $hierarchy .= '<tr><th colspan="2">' . section_name_path($sid) . '</th></tr>'.LF;

            // Get a list of all stories in this section
            $q  = "SELECT story_id, headline, weight FROM props_stories "
                . "WHERE edition_id = $edition_id "
                . "AND section_id = $sid "
                . "ORDER BY weight DESC";
            $result = sql_query($q);

            if (!sql_num_rows($result)) {
                $hierarchy .= '<tr class="row1"><td style="width: 2em;">&nbsp;</td><td><em>' . props_gettext("No results found.") . '</em></td></tr>'.LF;
            } else {
                // Else output stories and weight
                $row_num = 1;
                while ($row = sql_fetch_object($result)) {
                    $hierarchy .=
                          '  <tr class="row'.$row_num.'">'.LF
                        . '  <td style="width: 2em;">'.LF
                        . '    <select name="stories[' . $row->story_id . ']">'.LF;

                    $w = 100;
                    do {
                        $hierarchy .= '      <option ' . ($w == $row->weight ? 'selected="selected"' : '') . ' value="' . $w . '">' . $w . '</option>'.LF;
                        $w = $w - 10;
                    } while ($w);

                    $hierarchy .=
                          '    </select>'.LF
                        . '  </td>'.LF
                        . '  <td><a href="./?module=admincontent&amp;function=story_view&amp;story_id=' . $row->story_id . '">' . $row->headline . '</a></td>'.LF
                        . '</tr>'.LF;
                    $row_num = ($row_num == 1) ? 2 : 1;
                }
            }
            $hierarchy = display_section_hierarchy($sid, $current_depth+1);
        }
    }
    return $hierarchy;
}

?>
