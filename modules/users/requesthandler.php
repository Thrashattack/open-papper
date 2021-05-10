<?php
/**
 * Module functions
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
 * @version     $Id: requesthandler.php,v 1.33 2007/11/21 07:53:36 roufneck Exp $
 */

// Trap 'command' request parameter
switch (props_getkey('request.cmd')) {

    case 'users-register':
    case 'users-register_openid':
        rh_users_register();
        break;

    case 'users-passwordrecovery':
        rh_users_passwordrecovery();
        break;

    case 'users-passwordreset':
        rh_users_passwordreset();
        break;

    case 'users-preferences':

        if (!isset($_SESSION['PROPS_USER']['authenticated'])) {
            props_error("You must be logged in to view this page.");
            props_setkey('request.cmd', 'users-login');
        } else {
            rh_users_preferences();
        }
        break;
}

/**
 * Handles POST/GET cmd=users-register
 */
function rh_users_register()
{
    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    // Handle form vars
    $username = props_getrequest('username', VALIDATE_USERNAME, 'SANITIZE,MIN2,MAX14,!EMPTY');
    $email_address = props_getrequest('email_address', VALIDATE_EMAIL, 'MAX128,!EMPTY');
    $email_address2 = props_getrequest('email_address2', VALIDATE_EMAIL, 'MAX128,!EMPTY');
    $bulletins = props_getrequest('bulletins', VALIDATE_ARRAY);

    if ($email_address != $email_address2) {
        $GLOBALS['PROPS_ERRORSTACK']['email_address2']['message'] = props_gettext("Supplied email adresses do not match.");
        // Reset email_address2
        props_setkey('op.email_address2', '');
    }

    switch (props_getkey('request.cmd')) {
        case 'users-register':
            $password = props_getrequest('password', VALIDATE_TEXT, 'MIN5,!EMPTY');
            $password2 = props_getrequest('password2', VALIDATE_TEXT, '!EMPTY');
            if ($password != $password2) {
                $GLOBALS['PROPS_ERRORSTACK']['password2']['message'] = props_gettext("Supplied passwords do not match.");
            }
            $openid_url = NULL;
            break;

        case 'users-register_openid':
            if (!isset($_SESSION['PROPS_USER']['openid_url']) || $_SESSION['PROPS_USER']['openid_verified'] !== TRUE) {
                props_error("You must login with your OpenID URL first. After that you will be redirected to the register form.");
                return;
            }

            $password = NULL;
            $openid_url = $_SESSION['PROPS_USER']['openid_url'];
            break;
    }

    // If no errors, process request
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error("Please correct the errors first.");
        return;
    }

    // Register the user
    $user_id = user_register($username, $password, $email_address, $openid_url);

    if (!$user_id) {
        props_error("Please correct the errors first.");
        return;
    }

    // Delete any existing bulletin subscriptions for this user_id.
    // This query shouldn't ever match on any rows, but nothing riles
    // people up like unsolicited email, so let's do it anyway just
    // in case of inconsistent db data
    sql_query("DELETE FROM props_bulletins_subscriptions WHERE user_id = $user_id");

    // Now add bulletin subscriptions for this user
    foreach($bulletins as $bulletin_id) {
        $bulletin_id = intval($bulletin_id);

        $q  = "INSERT INTO props_bulletins_subscriptions SET ";
        $q .= "user_id = $user_id, ";
        $q .= "bulletin_id = $bulletin_id";
        sql_query($q);
    }

    // Redirect to frontpage
    props_setkey('request.template', 'displaysection');

    return;
}

/**
 * Handles POST/GET cmd=users-passwordrecovery
 */
function rh_users_passwordrecovery()
{
    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // No form post, do nothing
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    // Handle form vars
    $email_address = props_getrequest('email_address', VALIDATE_EMAIL, 'MAX128,!EMPTY');

    // Check submitted login/password against db
    $q  = "SELECT * FROM props_users "
        . "WHERE email_address = '$email_address'";
    $result = sql_query($q);
    if (sql_num_rows($result) == 1) {
        $user = sql_fetch_object($result);

        if (!empty($user->activation_key)) {
            props_error("You must activate this account first.");
            return FALSE;
        }

        $recoverpw_key = date('YmdHis');
        $recoverkey = md5($recoverpw_key.$user->user_id.$user->password_md5);

        // Update user record
        $q  = "UPDATE props_users SET "
            . "recoverpw_key = '$recoverpw_key' "
            . "WHERE user_id = " . $user->user_id . "";
        sql_query($q);

        // If errors, exit
        if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
            return FALSE;
        }

        // Load libs
        props_loadLib('sendmail,url');

        // Generate url
        $newpassword_url = genurl(array('cmd'=>'users-passwordreset', 'uid'=>$user->user_id, 'key'=>$recoverkey), FALSE);
        $newpassword_page = genurl(array('cmd'=>'users-passwordreset'), FALSE);

        // Assemble the email
        $mail = new props_sendmail();

        // Set headers and body text
        $mail->Sender   = props_getkey('config.mail.bounce_address');
        $mail->From     = props_getkey('config.mail.from_address');
        $mail->FromName = props_getkey('config.mail.from_name');
        $mail->Subject  = props_gettext("Password recovery information from").' '.props_getkey('config.publication.name');
        $mail->AddReplyTo(props_getkey('config.mail.from_address'), props_getkey('config.mail.from_name'));
        $mail->AddAddress($email_address, $user->username);
        $mail->Body  =
              $user->username .",".LF
            . sprintf(props_gettext("This email has been sent from %s."), props_getkey('config.url.root')).LF.LF
            . sprintf(props_gettext("You have received this email because an user account password recovery was instigated on %s."), props_getkey('config.publication.name')).LF.LF
            . "------------------------------------------------".LF
            . props_gettext("IMPORTANT!").LF
            . "------------------------------------------------".LF.LF
            . props_gettext("If you did not request this password change, please IGNORE and DELETE this email immediately. Only continue if you wish your password to be reset!").LF.LF
            . "------------------------------------------------".LF
            . props_gettext("Password recovery instructions").LF
            . "------------------------------------------------".LF.LF
            . props_gettext("We require that you validate your password recovery to ensure that you instigated this action."). ' '
            . props_gettext("Simply click on the link below and complete the rest of the form:").LF.LF
            . $newpassword_url.LF.LF
            . "------------------------------------------------".LF
            . props_gettext("Not working?").LF
            . "------------------------------------------------".LF.LF
            . props_gettext("If you could not validate your action by clicking on the link, please visit this page:").LF.LF
            . $newpassword_page.LF.LF
            . props_gettext("It will ask you for an user id number and your key. These are shown below:").LF.LF
            . props_gettext("User ID").': '.$user->user_id.LF.LF
            . props_gettext("Key").': '.$recoverkey.LF.LF
            . props_gettext("Please cut and paste, or type those numbers into the corresponding fields in the form.").LF.LF
            . props_gettext("If you still experience problems, please contact an administrator to rectify the problem.").LF.LF
            . sprintf(props_gettext("IP address of sender: %s."), props_get_ipaddress()).LF.LF
            . props_gettext("Regards").','.LF.LF
            . props_getkey('config.mail.from_name').LF
            . props_getkey('config.url.root').LF;
        $mail->WordWrap = '72';

        if($mail->Send()) {
            props_error(sprintf(props_gettext("An email with password recovery instructions has been send to %s."), $email_address));
            // Redirect
            props_setkey('request.cmd', 'users');
        } else {
            trigger_error(props_gettext("Error sending email") . ': ' . $mail->ErrorInfo, E_USER_WARNING);
        }

        return TRUE;
    } else {
        props_error("This email address is not registered.");
        return FALSE;
    }

    return;
}

/**
 * Handles POST/GET cmd=users-passwordreset
 */
function rh_users_passwordreset()
{
    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // No form post, do nothing
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    // Get vars
    $user_id = props_getrequest('uid', VALIDATE_INT, '!EMPTY');
    $key = props_getrequest('key', VALIDATE_MD5, '!EMPTY');
    $password = props_getrequest('password', VALIDATE_TEXT, 'MIN5,!EMPTY');
    $password2 = props_getrequest('password2', VALIDATE_TEXT, '!EMPTY');

    if ($password != $password2) {
        $GLOBALS['PROPS_ERRORSTACK']['password2']['message'] = props_gettext("Supplied passwords do not match.");
    }

    // If errors, exit
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        return FALSE;
    }

    // Check submitted details against db
    $q  = "SELECT * FROM props_users "
        . "WHERE user_id = $user_id ";
    $result = sql_query($q);
    $user = sql_fetch_object($result);
    if (sql_num_rows($result) != 1 || $user->recoverpw_key == 0) {
        props_error("There is no valid password recovery request pending for this account.");
        return FALSE;
    }

    $recoverkey = md5($user->recoverpw_key.$user->user_id.$user->password_md5);

    if ($key != $recoverkey) {
        props_error("Invalid key.");
        return FALSE;
    } else {
        // Update user record
        $q  = "UPDATE props_users SET "
            . "password_md5 = '" . md5($password) . "', "
            . "recoverpw_key = 0 "
            . "WHERE user_id = " . $user->user_id . "";
        sql_query($q);

        props_error("Your password is changed.");

        // Redirect
        props_setkey('request.cmd', 'users');

        return TRUE;
    }

    return;
}

/**
 * Handles POST/GET cmd=users-preferences
 */
function rh_users_preferences()
{
    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // Set defaults
        $email_address = (props_getrequest('email_address')) ? props_getrequest('email_address') : $_SESSION['PROPS_USER']['email_address'];
        props_setkey('request.email_address', $email_address);
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    // Handle form vars
    $password = props_getrequest('password', VALIDATE_TEXT, 'MIN5');
    $password2 = props_getrequest('password2', VALIDATE_TEXT);
    $email_address = props_getrequest('email_address', VALIDATE_EMAIL, 'MAX128');
    $email_address2 = props_getrequest('email_address2', VALIDATE_EMAIL, 'MAX128');
    $bulletins = props_getrequest('bulletins', VALIDATE_ARRAY);

    // Construct query
    $q  = "UPDATE props_users SET last_login = NOW() ";

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
    } else {
        props_setkey('request.email_address', $_SESSION['PROPS_USER']['email_address']);
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

    // If no errors, process submitted form
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error("Please correct the errors first.");
        return;
    }

    $q .= "  WHERE user_id = " . $_SESSION['PROPS_USER']['user_id'] . " ";
    sql_query($q);

    // Update session vars
    if (!empty($email_address2)) {
        $_SESSION['PROPS_USER']['email_address'] = $email_address;
        props_setkey('request.email_address', $email_address);
        props_setkey('request.email_address2', '');
    }

    // Remember me option
    if (!empty($password) && (isset($_COOKIE['PROPS_USER']) && isset($_COOKIE['PROPS_USER_MD5']))) {
        $expire = time()+60*60*24*360; // 360 days
        setcookie('PROPS_USER', $username, $expire);
        setcookie('PROPS_USER_MD5', md5($password), $expire);
    }

    // Delete any existing bulletin subscriptions for this user
    sql_query("DELETE FROM props_bulletins_subscriptions WHERE user_id = " . $_SESSION['PROPS_USER']['user_id']);

    // Now add bulletin subscriptions for this user
    foreach($bulletins as $bulletin_id) {
        $bulletin_id = intval($bulletin_id);

        $q  = "INSERT INTO props_bulletins_subscriptions SET ";
        $q .= "user_id = " . $_SESSION['PROPS_USER']['user_id'] . ", ";
        $q .= "bulletin_id = $bulletin_id";
        sql_query($q);
    }

    props_error("Your preferences are updated.");

    return;
}

?>
