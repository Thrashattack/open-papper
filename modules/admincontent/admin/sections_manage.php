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
 * @version     $Id: sections_manage.php,v 1.11 2007/09/23 09:12:44 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  Section management
 * @adminprivs  sections_reorder  Reorder sections
 * @adminnav    4
 * @return  string  admin screen html content
 *
 * @todo  Fix add section as a child to its own
 */
function admin_sections_manage()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'sections_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'section_add');

    // Set referer
    props_redirect(FALSE, 'set');

    // Get the needed posted vars here.
    $section_tree = props_getrequest('section_tree');

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'sections_reorder')) {
                props_error("You do not have permission to perform the selected action.");
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                $items = explode(",", $section_tree);
                for($sortorder = 0; $sortorder < count($items) ; $sortorder++) {
                    $tokens = explode("-",$items[$sortorder]);

                    $q  = "UPDATE props_sections SET "
                        . "parent_id = " . $tokens[1] . ", "
                        . "sortorder = " . $sortorder . " "
                        . "WHERE section_id = " . $tokens[0];
                    sql_query($q);
                }

                sections_reload();
            }
            break;

    } // END switch

    $GLOBALS['JavaScript'] =
         '<script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'drag-drop-folder-tree.js"></script>'.LF
        .'<script type="text/javascript">'.LF
        .'  function save_section_tree()'.LF
        .'  {'.LF
        .'    document.docForm.elements[\'section_tree\'].value = treeObj.getNodeOrders();'.LF
        //.'    document.docForm.submit();'.LF
        .'  }'.LF
        .'</script>'.LF;

    // Generate the function output
    $output =
         '<table class="hairline">'.LF
        .'  <tr>'.LF
        .'  <td style="width: 200px;">'.LF
        .'  <ul>'.LF
        .'    <li>'.props_gettext("Click on a section name to edit it.") . '</li>'.LF
        .'    <li>'.props_gettext("Click and drag a folder icon onto a section name to add it to that section.") . '</li>'.LF
        .'    <li>'.props_gettext("Click and drag a folder icon onto another folder to append it below that folder.") . '</li>'.LF
        .'  </ul>'.LF
        .'  </td>'.LF
        .'  <td style="width: 400px;">'.LF
        .'    <ul id="dhtml_tree" class="dhtml_tree">'.LF
        . sections_draw_hierarchy()
        .'    </ul>'.LF
        .'  </td>'.LF
        .'  </tr>'.LF
        .'</table>'.LF;

    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'sections_reorder')) {
        $output .=
             '<form name="docForm" id="docForm" action="./" method="post" onsubmit="save_section_tree();">'.LF
            .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
            .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
            .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
            .'<input name="section_tree" type="hidden" />'.LF
            .'<p>'.LF
            //.'  <input name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
            .'  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
            .'</p>'.LF
            .'</form>'.LF;
    }

    // THIS MUST BE AT THE END !!!
    // Oterwise IE starts complaining
    $GLOBALS['JavaScriptPageEnd'] =
         '<script type="text/javascript">'.LF
        .'  treeObj = new JSDragDropTree();'.LF
        .'  treeObj.setTreeId(\'dhtml_tree\');'.LF
        .'  treeObj.setFolderImage(\'button_folder.gif\');'.LF
        .'  treeObj.setPlusImage (\'button_expand.png\');'.LF
        .'  treeObj.setMinusImage (\'button_expanded.png\');'.LF
        .'  treeObj.initTree();'.LF
        .'  treeObj.expandAll();'.LF
        .'</script>'.LF;

    return $output;
}

?>
