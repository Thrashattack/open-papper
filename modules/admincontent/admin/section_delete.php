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
 * @version     $Id: section_delete.php,v 1.8 2007/09/17 12:07:23 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  Delete section
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_section_delete()
{
    // Get the needed posted vars here.
    $section_id = props_getrequest('section_id', VALIDATE_INT);

    if (!section_is_valid($section_id)) {
        props_error("Invalid ID.");
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
        exit;
    }

    // Get section detail
    $shortname = section_shortname($section_id);
    $sortorder = section_sortorder($section_id);
    $parent_id = section_parent_id($section_id);

    // Validate the deletion
    if (section_num_childs($section_id)) {
        props_error("This section cannot be deleted because it contains sub-sections. You must first move or delete the sub-sections before you can delete this section.");
    }

    if (section_num_stories($section_id)) {
        props_error("This section cannot be deleted because stories are assigned to it. You must first delete or reassign the stories before you can delete this section.");
    }

    if ($section_id == FRONTPAGE_SECTION_ID) {
        props_error("It is strictly forbidden to delete the front page section.");
    }

    // Trap errors
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        return '<p style="text-align: center;"><a class="button" href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=section_edit&amp;section_id=' . $section_id . '">' . props_gettext("Go back") . '</a></p>';
        exit;
    }

    // Delete the section's template subdirectory if that config option is enabled
    if (props_getkey('config.dir.auto_create')) {
        @rmdir(section_template_directory($section_id));
    }

    // All is well, delete that sucker
    sql_query("DELETE FROM props_sections WHERE section_id = $section_id");

    // Redirect
    props_redirect(TRUE);
}

?>
