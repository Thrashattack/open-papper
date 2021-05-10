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
 * @version     $Id: user_add.php,v 1.17 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Add user
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_user_add()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'users_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'user_add');

    // Get the needed posted vars here.
    $username = props_getrequest('username', VALIDATE_USERNAME, 'MIN2,MAX14,!EMPTY');
    $fullname = props_getrequest('fullname', VALIDATE_NAME);
    $user_type = props_getrequest('user_type', VALIDATE_INT);
    $group_id = props_getrequest('group_id', VALIDATE_INT);
    $email_address = props_getrequest('email_address', VALIDATE_EMAIL, '!EMPTY');
    $email_address2 = props_getrequest('email_address2', VALIDATE_EMAIL);
    $credits = props_getrequest('credits', VALIDATE_INT);
    if (empty($credits)) $credits = 0;
    $password = props_getrequest('password', VALIDATE_TEXT, '!EMPTY');
    $password2 = props_getrequest('password2', VALIDATE_TEXT);
    $expire_date = props_getrequest('expire_date', VALIDATE_DATE);
    $bulletin_subscriptions = props_getrequest('bulletin_subscriptions', VALIDATE_ARRAY);
    $open_url = props_getrequest('open_url', VALIDATE_TEXT);

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

            if ($user_type == PROPS_USERTYPE_FOUNDER && $_SESSION['PROPS_USER']['user_type'] != PROPS_USERTYPE_FOUNDER) {
                props_error("You do not have permission to demote or promote founders.", PROPS_E_WARNING);
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // Do this check only when there are no errors, to prevent SQL errors
                if (sql_num_rows(sql_query("SELECT username FROM props_users WHERE username = '".sql_escape_string($username)."'"))) {
                    $GLOBALS['PROPS_ERRORSTACK']['username']['message'] = props_gettext("This username already exists.");
                }

                // Check for unique address
                if (sql_num_rows(sql_query("SELECT email_address FROM props_users WHERE email_address = '".sql_escape_string($email_address)."'"))) {
                    $GLOBALS['PROPS_ERRORSTACK']['email_address']['message'] = props_gettext("This email address is already registered.");
                }

                // Compare two supplied addresses
                if ($email_address != $email_address2) {
                    $GLOBALS['PROPS_ERRORSTACK']['email_address2']['message'] = props_gettext("Supplied email adresses do not match.");
                }

                // Check for unique openid_url
                if (!empty($open_url) && sql_num_rows(sql_query("SELECT email_address FROM props_users WHERE openid_url = '".sql_escape_string($open_url)."'"))) {
                    $GLOBALS['PROPS_ERRORSTACK']['open_url']['message'] = props_gettext("This OpenID is already registered.");
                }

                // Compare two supplied passwords
                if ($password != $password2) {
                    $GLOBALS['PROPS_ERRORSTACK']['password2']['message'] = props_gettext("Supplied passwords do not match.");
                }

                // Catch errors
                if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
                    break;
                }

                // Assemble SQL. Use sql_escape_string for all vars except integers to prevent DB hacking.
                $q  = "INSERT INTO props_users SET "
                    . "  username = '" . sql_escape_string($username) . "', "
                    . "  fullname = '" . sql_escape_string($fullname) . "', "
                    . "  user_type = $user_type, "
                    . "  group_id = '$group_id', "
                    . "  password_md5 = '" . md5($password) . "', "
                    . "  email_address = '" . sql_escape_string($email_address) . "', "
                    . "  openid_url = '" . sql_escape_string($open_url) . "', "
                    . "  registered = NOW() ";
                $user_id = sql_identity_insert($q);

                // Update archives credit info
                if (props_getkey('config.archives.paid') == 1) {
                    sql_query("DELETE FROM props_users_archive_credits WHERE user_id = $user_id"); // just in case
                    $q  = "INSERT INTO props_users_archive_credits SET "
                        . "user_id = $user_id, "
                        . "credits = $credits, "
                        . "expire = '$expire_date'";
                    sql_query($q);
                }

                // Update bulletin subscriptions
                sql_query("DELETE FROM props_bulletins_subscriptions WHERE user_id = $user_id"); // just in case
                foreach($bulletin_subscriptions as $bulletin_id) {
                    $q  = "INSERT INTO props_bulletins_subscriptions SET "
                        . "user_id = $user_id, "
                        . "bulletin_id = $bulletin_id";
                    sql_query($q);
                }

                // Redirect
                props_redirect('goto', array('function'=>'users_manage'));
            }
            break;

        default:
            $credits = 0;
            break;

    } // END switch

    // Set to empty to prevent problems with javascript
    if (substr($expire_date, 0, 10) == '0000-00-00') $expire_date = '';

    $GLOBALS['JavaScript'] =
         '  <link rel="stylesheet" type="text/css" media="screen" href="' . props_getkey('config.url.scripts') . 'calendar.css" />'.LF
        .'  <script type="text/javascript">'.LF
        .'    var languageCode = \'en\';'.LF
        .'    var pathToImages = \'' . props_getkey('config.url.scripts') . 'images/\';'.LF
        .'  </script>'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'calendar.js"></script>'.LF;

    $GLOBALS['PROPS_FUNCTION_INFO'] =
         '<h2>' . props_gettext("User types") . '</h2>'.LF
        .'<p>'.LF
        .'  <b>' . props_gettext("User") . '</b> - ' . props_gettext("registered user with access to user/frontpage privs") . '<br />'.LF
        .'  <b>' . props_gettext("Administrator") . '</b> - ' . props_gettext("user with access to user/frontpage and admin privs") . '<br />'.LF
        .'  <b>' . props_gettext("Founder") . '</b> - ' . props_gettext("user with access to all available privs, even when not assigned") . '<br />'.LF
        .'  <b>' . props_gettext("Closed") . '</b> - ' . props_gettext("account is blocked and not accessible") . '<br />'.LF
        .'</p>'.LF
        .'<h2>' . props_gettext("User groups") . '</h2>'.LF
        .'<p>' . props_gettext("If users are not assigned to a group, they will get the default group for their user type, if available.") . '</p>'.LF;

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("User details") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Username") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="username" name="username" value="' . htmlspecialchars($username) . '" /></dd>'.LF
        .((props_geterror('username')) ? '<dd>' . props_geterror('username') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Full name") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="fullname" name="fullname" value="' . htmlspecialchars($fullname) . '" /></dd>'.LF
        .((props_geterror('fullname')) ? '<dd>' . props_geterror('fullname') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Type") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" name="user_type">'.LF
        .'        <option ' .(($user_type == PROPS_USERTYPE_USER) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_USER . '">' . props_gettext("User") . '</option>'.LF
        .'        <option ' .(($user_type == PROPS_USERTYPE_ADMIN) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_ADMIN . '">' . props_gettext("Administrator") . '</option>'.LF;
        if ($_SESSION['PROPS_USER']['user_type'] == PROPS_USERTYPE_FOUNDER) {
            $output .=
                '        <option ' .(($user_type == PROPS_USERTYPE_FOUNDER) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_FOUNDER . '">' . props_gettext("Founder") . '</option>'.LF;
        }
        $output .=
         '        <option ' .(($user_type == PROPS_USERTYPE_CLOSED) ? 'selected="selected"': '') . ' value="' . PROPS_USERTYPE_CLOSED . '">' . props_gettext("Closed") . '</option>'.LF
        .'      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('user_type')) ? '<dd>' . props_geterror('user_type') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Group") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" name="group_id">'.LF
        .'        <option value="">' . props_gettext("Not assigned") . '</option>'.LF;

    $result = sql_query("SELECT * FROM props_users_groups ORDER BY group_name");
    while ($row = sql_fetch_object($result)) {
        $selected = ($row->group_id == $group_id) ? 'selected="selected"' : '';
        $output .= '        <option ' . $selected . ' value="' . $row->group_id . '">' . htmlspecialchars($row->group_name) . '</option>'.LF;
    }

    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('group_id')) ? '<dd>' . props_geterror('group_id') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Email address") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="email_address" name="email_address" value="' . htmlspecialchars($email_address) . '" /></dd>'.LF
        .((props_geterror('email_address')) ? '<dd>' . props_geterror('email_address') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Retype email address") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="email_address2" name="email_address2" value="' . htmlspecialchars($email_address2) . '" /></dd>'.LF
        .((props_geterror('email_address2')) ? '<dd>' . props_geterror('email_address2') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Password") . '</label></dt>'.LF
        .'    <dd><input class="large" type="password" id="password" name="password" value="" /></dd>'.LF
        .((props_geterror('password')) ? '<dd>' . props_geterror('password') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Retype password") . '</label></dt>'.LF
        .'    <dd><input class="large" type="password" id="password2" name="password2" value="" /></dd>'.LF
        .((props_geterror('password2')) ? '<dd>' . props_geterror('password2') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>OpenID</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="open_url" name="open_url" value="' . htmlspecialchars($open_url) . '" /></dd>'.LF
        .((props_geterror('open_url')) ? '<dd>' . props_geterror('open_url') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Bulletin subscriptions") . '</label></dt>'.LF;

    $q  = "SELECT * FROM props_bulletins ";
    $result = sql_query($q);
    while ($row = sql_fetch_object($result)) {
        $checked = (in_array($row->bulletin_id, $bulletin_subscriptions)) ? 'checked="checked"' : '';
        $output .= '    <dd><input class="checkbox" type="checkbox" name="bulletin_subscriptions[]" value="' . $row->bulletin_id . '" ' . $checked . ' />&nbsp;' . $row->bulletin_name . '</dd>'.LF;
    }

    $output .=
         '  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Credits remaining") . '</label><br />' . props_gettext("Remaining credits used for purchasing paid archive stories.") . '</dt>'.LF
        .'    <dd><input class="large" type="text" id="credits" name="credits" value="' . htmlspecialchars($credits) . '" /></dd>'.LF
        .((props_geterror('credits')) ? '<dd>' . props_geterror('credits') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Credits expire date") . '</label><br />' . props_gettext("The credits will not expire when there is no date set.") . '</dt>'.LF
        .'    <dd><input class="large" type="text" id="expire_date" name="expire_date" value="' . htmlspecialchars($expire_date) . '" />'.LF
        .'      <img src="./images/button_calendar.png" style="cursor: pointer;" alt="Date selector" title="Date selector" onclick="displayCalendar(document.getElementById(\'expire_date\'),\'yyyy-mm-dd\',this)" /></dd>'.LF
        .((props_geterror('expire_date')) ? '<dd>' . props_geterror('expire_date') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'</fieldset>'.LF
        .'  <p>'.LF
        .'    <input class="button" type="submit" name="op" value="' . props_gettext("Cancel") . '" />'.LF
        .'    <input class="button" type="submit" name="op" value="' . props_gettext("Save") . '" />'.LF
        .'  </p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
