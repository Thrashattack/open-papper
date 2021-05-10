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
 * @version     $Id: section_edit.php,v 1.14 2007/12/11 15:46:29 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  Edit section
 * @adminprivs  wysiswg_editor  Use WYSIWYG editor
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_section_edit()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'sections_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'section_add');

    // Get the needed posted vars here.
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    $parent_id = props_getrequest('parent_id', VALIDATE_INT);
    $shortname = props_getrequest('shortname', '_'.VALIDATE_ALPHA_LOWER.VALIDATE_NUM, '!EMPTY,MAX64,SANITIZE');
    $fullname = props_getrequest('fullname', VALIDATE_TEXT, '!EMPTY,MAX64');
    $static_content = props_getrequest('static_content', VALIDATE_HTML);
    $auto_archive_enabled = props_getrequest('auto_archive_enabled', VALIDATE_BOOL);
    $auto_archive_access_level = props_getrequest('auto_archive_access_level', VALIDATE_INT);
    $auto_archive_days = props_getrequest('auto_archive_days', VALIDATE_INT);

    // Get details from DB.
    $q  = "SELECT * FROM props_sections "
        . "WHERE section_id = $section_id";
    $result = sql_query($q);
    $section = sql_fetch_object($result);

    if (!sql_num_rows($result)) {
        props_error("Invalid ID.");
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
        exit;
    }

    // Force root section to stay in root
    if ($section->parent_id == 0) $parent_id = 0;

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Delete"):
            // Redirect
            props_redirect('goto', array('function'=>'section_delete', 'section_id'=>$section_id));
            break;

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                if (sql_num_rows(sql_query("SELECT * FROM props_sections WHERE shortname = '$shortname' AND section_id != $section_id"))) {
                    $GLOBALS['PROPS_ERRORSTACK']['shortname']['message'] = sprintf(props_gettext("Short directory name '%s' is already in use by another section."), $shortname);
                    break;
                }

                if (sql_num_rows(sql_query("SELECT * FROM props_sections WHERE fullname = '$fullname' AND parent_id = $parent_id AND section_id != $section_id"))) {
                    $GLOBALS['PROPS_ERRORSTACK']['fullname']['message'] = sprintf(props_gettext("There is already another section named '%s' below the selected parent section."), $fullname);
                    break;
                }

                // If section is being reparented, adjust the sortorder of the old siblings, and
                // set the new sortorder to the highest value among the new siblings
                if ($parent_id != section_parent_id($section_id)) {
                    // Give it the highest sortorder
                    $sortorder = section_get_new_sortorder();
                } else {
                    // We're not reparenting, so just stick with the current sortorder
                    $sortorder = section_sortorder($section_id);
                }

                // Save old template path for possible post-update use
                $old_template_path = section_template_directory($section_id);

                $auto_archive_access_level = (!empty($auto_archive_access_level)) ? $auto_archive_access_level : 0;
                $auto_archive_days = (!empty($auto_archive_days)) ? $auto_archive_days : 0;

                // Force root section to stay in root
                if ($section->parent_id == 0) $parent_id = 0;

                // Update record
                $q  = "UPDATE props_sections SET "
                    . "parent_id = $parent_id, "
                    . "fullname = '" . sql_escape_string($fullname) . "', "
                    . "shortname = '" . sql_escape_string($shortname) . "', "
                    . "sortorder = $sortorder, "
                    . "auto_archive_enabled = $auto_archive_enabled, "
                    . "auto_archive_days = $auto_archive_days, "
                    . "auto_archive_access_level = $auto_archive_access_level, "
                    . "static_content = '" . sql_escape_string($static_content) . "' "
                    . "WHERE section_id = $section_id";
                sql_query($q);

                // Reload the sections_array
                sections_reload();

                // Rename the section's template subdirectory if that config option is enabled and the shortname has changed
                if ((props_getkey('config.dir.auto_create'))  && ($shortname != $section->shortname)) {
                    @rename($old_template_path, section_template_directory($section_id));
                }

                // Redirect
                props_redirect(TRUE);
            }
            break;

        default:
            // Initially populate form fields with values from db
            $parent_id = section_parent_id($section_id);
            $shortname = section_shortname($section_id);
            $shortname = section_shortname($section_id);
            $fullname = section_fullname($section_id);
            $static_content = $section->static_content;
            $auto_archive_enabled = $section->auto_archive_enabled;
            $auto_archive_access_level = $section->auto_archive_access_level;
            $auto_archive_days = $section->auto_archive_days;

            break;

    } // END switch

    // Activate WYSIWYG-editor for body_content field
    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'wysiswg_editor')) {
        $GLOBALS['PROPS_WYSIWYG'] = 'static_content';
    }

    // Force root section to stay in root
    $parent = ($section->parent_id == 0) ? '<input name="parent_id" type="hidden" value="0" />'.props_gettext("You cannot move a root section.") : section_select($parent_id, 'parent_id');

    // Generate auto_archive_access_level
    $aaal =
         '      <select style="width: auto;" id="auto_archive_access_level" name="auto_archive_access_level">'.LF
        .'        <option value="' . ACCESS_FREE . '" ' . (($auto_archive_access_level == ACCESS_FREE) ? 'selected="selected"' : '') . '>' . props_gettext("Free") . '</option>'.LF
        .'        <option value="' . ACCESS_REG_REQUIRED . '" ' . (($auto_archive_access_level == ACCESS_REG_REQUIRED) ? 'selected="selected"' : '') . '>' . props_gettext("Registration required") . '</option>'.LF;
    if (props_getkey('config.archives.paid')) {
        $aaal .= '        <option value="' . ACCESS_PAID_ARCHIVES . '" ' . (($auto_archive_access_level == ACCESS_PAID_ARCHIVES) ? 'selected="selected"' : '') . '>' . props_gettext("Paid archives") . '</option>'.LF;
    }
    $aaal .=
         '      </select>'.LF;

    // Generate auto_archive_days
    $aad =
         '      <select style="width: auto;" id="auto_archive_days" name="auto_archive_days">'.LF;
    $daychoices = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,30,45,60,90,120,180,365,547,730);
    foreach ($daychoices as $daychoice) {
        $aad .= '        <option value="' . $daychoice . '" ' . (($daychoice == $auto_archive_days) ? 'selected="selected"' : '') . '>' . $daychoice . '</option>'.LF;
    }
    $aad .=
         '      </select>'.LF;

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form id="sectionform" name="sectionform" action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<input name="section_id" type="hidden" value="' . $section_id . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Settings") . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Parent section") . '</label></dt>'.LF
        .'    <dd>' . $parent . '</dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Full section name") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="fullname" name="fullname" value="' . htmlspecialchars($fullname) . '" /></dd>'.LF
        .((props_geterror('fullname')) ? '    <dd>' . props_geterror('fullname') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Short directory name") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="shortname" name="shortname" value="' . htmlspecialchars($shortname) . '" /></dd>'.LF
        .((props_geterror('shortname')) ? '    <dd>' . props_geterror('shortname') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Auto archiving") . '</label></dt>'.LF
        .'    <dd>' . props_gettext("Enable") . ' / ' . props_gettext("Disable") . ' ' . strtolower(props_gettext("Auto archiving")) . ' <input class="checkbox" type="checkbox" id="auto_archive_enabled" name="auto_archive_enabled" value="1" onclick="disableFormOptions(\'auto_archive_enabled\', \'auto_archive_access_level,auto_archive_days\');" ' . (($auto_archive_enabled) ? 'checked="checked"' : '') . ' /></dd>'.LF
        .'    <dd>' . sprintf(props_gettext("Auto change the access level of published stories in this section to %s after %s days."), $aaal, $aad) . '</dd>'.LF
        .((props_geterror('auto_archive_enabled')) ? '     <dd>' . props_geterror('auto_archive_enabled') . '</dd>'.LF : '')
        .((props_geterror('auto_archive_access_level')) ? '    <dd>' . props_geterror('auto_archive_access_level') . '</dd>'.LF : '')
        .((props_geterror('auto_archive_days')) ? '    <dd>' . props_geterror('auto_archive_days') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <p>'.LF
        .'    <label class="label" style="text-align: left;">' . props_gettext("Static page content") . '</label>&nbsp;&nbsp;&nbsp;<span class="desc">(' . props_gettext("Optional content which will appear on the section front") . ')</span><br />'.LF
        .'    <textarea class="full" style="height: 300px;" id="static_content" name="static_content" rows="20" cols="80">' . htmlspecialchars($static_content) . '</textarea>'.LF
        .'  </p>'.LF
        .'  <p>'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Delete") . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');" />&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'  </p>'.LF
        .'</fieldset>'.LF
        .'</form>'.LF

        .'<script type="text/javascript">'.LF
        // Run once to set the pulldowns disabled/enabled as appropriate
        .'  disableFormOptions(\'auto_archive_enabled\', \'auto_archive_access_level,auto_archive_days\');'.LF
        .'</script>'.LF;

    return $output;
}

?>
