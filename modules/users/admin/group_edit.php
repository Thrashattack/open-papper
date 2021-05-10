<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  users
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
 * @version     $Id: group_edit.php,v 1.6 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Edit user group
 * @adminnav    0
 * @adminprivs  group_delete  Delete user group
 * @return  string  admin screen html content
 */
function admin_group_edit()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'groups_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'group_add');

    // Get the needed posted vars here.
    $group_id = props_getrequest('group_id', VALIDATE_INT);
    $group_name = props_getrequest('group_name', VALIDATE_TEXT, '!EMPTY,SANITIZE');
    $group_desc = props_getrequest('group_desc', VALIDATE_TEXT);
    $user_type = props_getrequest('user_type', VALIDATE_INT);

    // Get details from DB.
    $q  = "SELECT * FROM props_users_groups WHERE group_id = $group_id";
    $result = sql_query($q);
    $group = sql_fetch_object($result);

    if (!sql_num_rows($result)) {
        props_error("Invalid ID.");
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
        exit;
    }

    if (!isset($_POST['permission_array'])) $_POST['permission_array'] = array();

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Delete"):

            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'group_delete')) {
                props_error("You do not have permission to perform the selected action.", PROPS_E_WARNING);
                break;
            }

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If there are users assigned to this group, we can't delete it
            $q  = "SELECT Count(*) AS user_count FROM props_users WHERE group_id = $group_id";
            $result = sql_query($q);
            $row = sql_fetch_object($result);

            if ($row->user_count > 0) {
                props_error("Group cannot be deleted because it contains users. First delete or reassign the users before deleting this group.");
                return '<p><a href="javascript:history.go(-1)">' . props_gettext("Go back") . '</a></p>';
                exit;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // Remove them from admin_groups table
                sql_query("DELETE FROM props_users_groups WHERE group_id = $group_id");

                // Remove privileges for members of this group
                sql_query("DELETE FROM props_users_groupprivs WHERE group_id = $group_id");

                // Redirect
                props_redirect(TRUE);
            }
            break;

        case props_gettext("Save"):
            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // Extra validation of post variables
            if (sql_num_rows(sql_query("SELECT * FROM props_users_groups WHERE group_name = '" . sql_escape_string($group_name) . "' AND group_id != $group_id"))) {
                $GLOBALS['PROPS_ERRORSTACK']['group_name']['message'] = props_gettext("A group with this name already exists.");
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                if (!empty($user_type)) {
                    // There can be only 1...
                    $q  = "UPDATE props_users_groups SET "
                        . "  default_user_type = '' "
                        . "WHERE default_user_type = $user_type";
                    sql_query($q);
                }

                // Change group name
                $q  = "UPDATE props_users_groups SET "
                    . "  group_name = '" . sql_escape_string($group_name) . "', "
                    . "  group_desc = '" . sql_escape_string($group_desc) . "', "
                    . "  default_user_type = '$user_type' "
                    . "WHERE group_id = $group_id";
                sql_query($q);

                // Delete this group's permissions from group_privs table
                sql_query("DELETE FROM props_users_groupprivs WHERE group_id = $group_id");

                // Loop through checked permissions and add them
                foreach ($_POST['permission_array'] as $key => $priv_id) {
                    $q  = "INSERT INTO props_users_groupprivs SET "
                        . "group_id = $group_id, "
                        . "priv_id = " . (int) $priv_id . " ";
                    sql_query($q);
                }

                // Redirect
                props_redirect(TRUE);
            }
            break;

        default:
            $group_name = $group->group_name;
            $group_desc = $group->group_desc;
            $user_type = $group->default_user_type;

            // Stuff permissions into an array
            $_POST['permission_array'] = array();
            $result = sql_query("SELECT * FROM props_users_groupprivs WHERE group_id = $group_id");
            while ($row = sql_fetch_object($result)) {
                $_POST['permission_array'][] = $row->priv_id;
            }
            break;

    } // END switch

    $GLOBALS['PROPS_FUNCTION_INFO'] =
         '<h2>' . props_gettext("Default user type") . '</h2>'.LF
        .'<p>' . props_gettext("If users are not assigned to a group, they will get the default group for their user type, if available.") . '</p>'.LF
        .'<p>' . props_gettext("You can set the group as default for guests, users and administrators. Founders have always all available privs.") . '</p>'.LF;

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<input name="group_id" type="hidden" value="' . $group_id . '" />'.LF

        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Group details") . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Group name") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="group_name" name="group_name" value="' . htmlspecialchars($group_name) . '" /></dd>'.LF
        .((props_geterror('group_name')) ? '<dd>' . props_geterror('group_name') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Description") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="group_desc" name="group_desc" value="' . htmlspecialchars($group_desc) . '" /></dd>'.LF
        .((props_geterror('group_desc')) ? '<dd>' . props_geterror('group_desc') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Default user type") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" name="user_type">'.LF
        .'        <option value="">' . props_gettext("No default group") . '</option>'.LF
        .'        <option ' .(($user_type == PROPS_USERTYPE_GUEST) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_GUEST . '">' . props_gettext("Guest") . '</option>'.LF
        .'        <option ' .(($user_type == PROPS_USERTYPE_USER) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_USER . '">' . props_gettext("User") . '</option>'.LF
        .'        <option ' .(($user_type == PROPS_USERTYPE_ADMIN) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_ADMIN . '">' . props_gettext("Administrator") . '</option>'.LF
        .'      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('user_type')) ? '<dd>' . props_geterror('user_type') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'</fieldset>'.LF
        .'<br />'.LF

    // Admin privs
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Administrator privileges") . '</legend>'.LF;

    // Get all functions
    $q  = "SELECT * FROM props_users_privs WHERE type = ".PROPS_PRIVTYPE_ADMIN." "
        . "ORDER BY module ASC, function ASC";
    $result = sql_query($q);

    $currentmod = '';
    $row_num = 1;
    while ($row = sql_fetch_object($result)) {
        // New module
        if ($currentmod != $row->module) {
            if ($currentmod) {
                // Close table
                $output .=
                     '    </tbody>'.LF
                    .'  </table>'.LF;
            }
            $output .=
                 '  <table style="border: 0px;">'.LF
                .'    <thead>'.LF
                .'      <tr>'.LF
                .'        <th style="width: 1em;" class="checkbox"><input class="checkbox" type="checkbox" /></th>'.LF
                .'        <th style="cursor: pointer;" onclick="javascript:toggleTableRows(\'ap_'.$row->module.'_body\')">' . props_gettext('.'.$row->module) . '</th>'.LF
                .'      </tr>'.LF
                .'    </thead>'.LF
                .'    <tbody id="ap_'.$row->module.'_body">'.LF;
            $currentmod = $row->module;
        }

        // Figure out whether it should be checked
        $checked_string = in_array($row->priv_id, $_POST['permission_array']) ? 'checked="checked" ': '';

        // Function
        $output .=
             '      <tr class="row'.$row_num.'">'.LF
            .'        <td><input class="checkbox" type="checkbox" name="permission_array[]" value="' . $row->priv_id . '" ' . $checked_string . '/></td>'.LF
            .'        <td>' . props_gettext('.'.$row->module.'.'.$row->function) . '</td>'.LF
            .'      </tr>'.LF;
        // Update row number
        $row_num = ($row_num == 1) ? 2 : 1;
    }

    if ($currentmod) {
        // Close table
        $output .=
             '    </tbody>'.LF
            .'  </table>'.LF;
    }

    $output .=
         '</fieldset>'.LF
        .'<br />'.LF

    // User privs
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("User privileges") . '</legend>'.LF;

    // Get all functions
    $q  = "SELECT * FROM props_users_privs WHERE type = ".PROPS_PRIVTYPE_USER." "
        . "ORDER BY module ASC, function ASC";
    $result = sql_query($q);

    $currentmod = '';
    $row_num = 1;
    while ($row = sql_fetch_object($result)) {
        // New module
        if ($currentmod != $row->module) {
            if ($currentmod) {
                // Close table
                $output .=
                     '    </tbody>'.LF
                    .'  </table>'.LF;
            }
            $output .=
                 '  <table style="border: 0px;">'.LF
                .'    <thead>'.LF
                .'      <tr>'.LF
                .'        <th style="width: 1em;" class="checkbox"><input class="checkbox" type="checkbox" /></th>'.LF
                .'        <th style="cursor: pointer;" onclick="javascript:toggleTableRows(\'up_'.$row->module.'_body\')">' . props_gettext('.'.$row->module) . '</th>'.LF
                .'      </tr>'.LF
                .'    </thead>'.LF
                .'    <tbody id="up_'.$row->module.'_body">'.LF;
            $currentmod = $row->module;
        }

        // Figure out whether it should be checked
        $checked_string = in_array($row->priv_id, $_POST['permission_array']) ? 'checked="checked" ': '';

        // Function
        $output .=
             '      <tr class="row'.$row_num.'">'.LF
            .'        <td><input class="checkbox" type="checkbox" name="permission_array[]" value="' . $row->priv_id . '" ' . $checked_string . '/></td>'.LF
            .'        <td>' . props_gettext('+'.$row->module.'.'.$row->function) . '</td>'.LF
            .'      </tr>'.LF;
        // Update row number
        $row_num = ($row_num == 1) ? 2 : 1;
    }

    if ($currentmod) {
        // Close table
        $output .=
             '    </tbody>'.LF
            .'  </table>'.LF;
    }

    $output .=
         '</fieldset>'.LF
        .'<br />'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF;
    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'group_delete')) {
        $output .= '  <input class="button" name="op" type="submit" value="' . props_gettext("Delete") . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');" />&nbsp;&nbsp;'.LF;
    }
    $output .=
         '  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
