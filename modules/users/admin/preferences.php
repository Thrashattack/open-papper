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
 * @version     $Id: preferences.php,v 1.9 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Preferences
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_preferences()
{
    // Set sitebar
    admin_sidebar_add('adminmain', 'mainmenu');

    $fullname = props_getrequest('fullname', VALIDATE_NAME);
    $language = props_getrequest('language', 'a-zA-Z_');
    $email_address = props_getrequest('email_address', VALIDATE_EMAIL);
    $email_address2 = props_getrequest('email_address2', VALIDATE_EMAIL);
    $password = props_getrequest('password', VALIDATE_TEXT);
    $password2 = props_getrequest('password2', VALIDATE_TEXT);
    $bookmarks = props_getrequest('bookmarks', VALIDATE_ARRAY);

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // Assemble SQL to update user record
            $q  = "UPDATE props_users SET "
                . "  fullname = '" . sql_escape_string($fullname) . "'"
                . ", language = '" . sql_escape_string($language) . "'";

            // If changing email address
            if (!empty($email_address2)) {
                // Check for unique address
                if (sql_num_rows(sql_query("SELECT email_address FROM props_users WHERE email_address = '".sql_escape_string($email_address)."' AND user_id != ".$_SESSION['PROPS_USER']['user_id']))) {
                    $GLOBALS['PROPS_ERRORSTACK']['email_address']['message'] = props_gettext("This email address is already registered.");
                }

                // Compare two supplied addresses
                if ($email_address == $email_address2) {
                    $q .= ", email_address = '" . sql_escape_string($email_address) . "'";
                } else {
                    $GLOBALS['PROPS_ERRORSTACK']['email_address2']['message'] = props_gettext("Supplied email adresses do not match.");
                }
            }

            // If changing password
            if (!empty($password)) {
                // Compare two supplied passwords
                if ($password == $password2) {
                    $q .= ", password_md5 = '" . md5($password) . "'";
                } else {
                    $GLOBALS['PROPS_ERRORSTACK']['password2']['message'] = props_gettext("Supplied passwords do not match.");
                }
            }

            $q .= " WHERE user_id = '" . $_SESSION['PROPS_USER']['user_id'] . "'";

            // If no errors, do update, otherwise drop through and display error
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                sql_query($q);

                // Update session vars
                $_SESSION['PROPS_USER']['fullname'] = $fullname;
                $_SESSION['PROPS_USER']['email_address'] = $email_address;
                $_SESSION['PROPS_USER']['language'] = $language;
                $expire = time()+60*60*24*360; // 360 days
                setcookie('PROPS_LANGUAGE', $language, $expire);

                $bookmark_list= '';
                foreach($bookmarks as $key) {
                    $bookmark_list .= ($bookmark_list) ? ', '.$key : $key;
                }

                // Update bookmarks
                if ($bookmark_list) {
                    sql_query("DELETE FROM props_users_bookmarks WHERE user_id = ".$_SESSION['PROPS_USER']['user_id']." AND bookmark_id NOT IN ($bookmark_list)");
                } elseif (empty($bookmarks)) {
                    sql_query("DELETE FROM props_users_bookmarks WHERE user_id = ".$_SESSION['PROPS_USER']['user_id']);
                }

                // Redirect
                props_redirect(TRUE);
            }
            break;

        default:
            // Set the initial form field values on initial page call
            $fullname = $_SESSION['PROPS_USER']['fullname'];
            $email_address = $_SESSION['PROPS_USER']['email_address'];
            $language = $_SESSION['PROPS_USER']['language'];
            break;

    } // END switch

    switch ($_SESSION['PROPS_USER']['user_type']) {
        case PROPS_USERTYPE_FOUNDER: $usertype = '<span class="usertype_founder">'.props_gettext("Founder").'</span>'; break;
        case PROPS_USERTYPE_ADMIN: $usertype = '<span class="usertype_admin">'.props_gettext("Administrator").'</span>'; break;
        case PROPS_USERTYPE_USER: $usertype = '<span class="usertype_user">'.props_gettext("User").'</span>'; break;
        case PROPS_USERTYPE_BOT: $usertype = '<span class="usertype_bot">'.props_gettext("Bot").'</span>'; break;
        case PROPS_USERTYPE_CLOSED: $usertype = '<span class="usertype_closed">'.props_gettext("Closed").'</span>'; break;
        default: $usertype = '<span class="usertype_guest">'.props_gettext("Guest").'</span>'; break;
    }

    $result = sql_query("SELECT * FROM props_users_groups WHERE group_id = '" . $_SESSION['PROPS_USER']['group_id'] . "'");
    $row = sql_fetch_object($result);
    if ($row) {
        $group =
             '<label>'.htmlspecialchars($row->group_name) . '</label><br/>'
            .htmlspecialchars($row->group_desc);
    } else {
        $group = '';
    }

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']) . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Username") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($_SESSION['PROPS_USER']['username']) . '</dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Full name") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="fullname" name="fullname" value="' . htmlspecialchars($fullname) . '" /></dd>'.LF
        .((props_geterror('fullname')) ? '<dd>' . props_geterror('fullname') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Type") . '</label></dt>'.LF
        .'    <dd>' . $usertype . '</dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Group") . '</label></dt>'.LF
        .'    <dd>' . $group . '</dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Registered") . '</label></dt>'.LF
        .'    <dd>' . (($_SESSION['PROPS_USER']['registered']) ? strftime('%x %X', strtotime($_SESSION['PROPS_USER']['registered'])) : '&nbsp;') . '</dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Language") . '</label><br />' . props_gettext("Change the admin control panel language.") . '</dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" name="language">'.LF
        .'        <option value="">' . props_gettext("Default") . '</option>'.LF;
    if ($dh = opendir(PROPS_ROOT.'locale/')) {
        while (($file = readdir($dh)) !== false ) {
            $file = explode('.', $file, 2);
            if (isset($file[1]) && $file[1] == 'php') {
                if ($file[0] != 'default') {
                    $selected = ($file[0] == $language) ? 'selected="selected"' : '';
                    $output .= '        <option ' . $selected . ' value="' . $file[0] . '">' . htmlspecialchars($file[0]) . '</option>'.LF;
                }
            }
        }
        closedir($dh);
    }
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('language')) ? '<dd>' . props_geterror('language') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Email address") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="email_address" name="email_address" value="' . htmlspecialchars($email_address) . '" /></dd>'.LF
        .((props_geterror('email_address')) ? '<dd>' . props_geterror('email_address') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Retype email address") . '</label><br /><span>' . props_gettext("You only need to specify this if you are changing the users e-mail address.") . '</span></dt>'.LF
        .'    <dd><input class="large" type="text" id="email_address2" name="email_address2" value="' . htmlspecialchars($email_address2) . '" /></dd>'.LF
        .((props_geterror('email_address2')) ? '<dd>' . props_geterror('email_address2') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("New password") . '</label></dt>'.LF
        .'    <dd><input class="large" type="password" id="password" name="password" value="" /></dd>'.LF
        .((props_geterror('password')) ? '<dd>' . props_geterror('password') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Retype password") . '</label><br /><span>' . props_gettext("You only need to confirm the password if you entered a new one.") . '</span></dt>'.LF
        .'    <dd><input class="large" type="password" id="password2" name="password2" value="" /></dd>'.LF
        .((props_geterror('password2')) ? '<dd>' . props_geterror('password2') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Bookmarks") . '</label><br />' . props_gettext("Unselect for removal.") . '</dt>'.LF;

    $q = "SELECT * from props_users_bookmarks WHERE user_id = ".$_SESSION['PROPS_USER']['user_id']." ORDER BY bookmark_name";
    $result = sql_query($q);
    while ($row = sql_fetch_object($result)) {
        $output .= '    <dd><input class="checkbox" type="checkbox" name="bookmarks[]" value="' . $row->bookmark_id . '" checked="checked" />&nbsp;' . $row->bookmark_name . '</dd>'.LF;
    }

    $output .=
         '  </dl>'.LF

        .'</fieldset>'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
