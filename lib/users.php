<?php
/**
 * Lib - users functions
 *
 * @package     api
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
 * @version     $Id: users.php,v 1.40 2007/11/16 09:52:34 roufneck Exp $
 */

/**
 * Defines
 */
define('PROPS_USERTYPE_FOUNDER',  1); // Has all the rights anytime
define('PROPS_USERTYPE_ADMIN',    2); // Has access to admin control panel
define('PROPS_USERTYPE_USER',     4); // Is a registered frontpage user
define('PROPS_USERTYPE_GUEST',    8); // Non registered frontpage guest
define('PROPS_USERTYPE_BOT',     16); // Search bot
define('PROPS_USERTYPE_CLOSED',  32); // Closed account

define('PROPS_PRIVTYPE_ADMIN',    1); // Privs in the admin control panel
define('PROPS_PRIVTYPE_USER',     2); // Privs in the frontpage
define('PROPS_PRIVTYPE_TEMPLATE', 4); // Privs from templates

// Reset privs
unset($GLOBALS['PROPS_USERPRIVS']);

/**
 * Initialize an user and catch user login commands
 *
 * Possible user commands (triggered by POST/GET 'cmd' request):
 * - login
 * - logout
 * - activate
 */
function user_init()
{
    // Try to login with cookies
    if (!isset($_SESSION['PROPS_USER']['authenticated'])) {
        if (!empty($_COOKIE['PROPS_OPENID'])) {
            return user_login_openid($_COOKIE['PROPS_OPENID']);
        } elseif (!empty($_COOKIE['PROPS_USER']) && !empty($_COOKIE['PROPS_PW_MD5'])) {
            return user_login_basic($_COOKIE['PROPS_USER'], $_COOKIE['PROPS_PW_MD5']);
        } else {
            // Set to default guest user
            $_SESSION['PROPS_USER']['user_id'] = ANONYMOUS_USER_ID;
            $_SESSION['PROPS_USER']['user_type'] = PROPS_USERTYPE_GUEST;
        }
    }

    switch (props_getkey('request.cmd')) {

        case 'login':
            // Get login redirects onsuccess, onfailure
            $onsuccess = props_getrequest('onsuccess', '-_a-zA-Z0-9', 'SANITIZE');
            $onfailure = props_getrequest('onfailure', '-_a-zA-Z0-9', 'SANITIZE');
            static $result;

            if (!empty($_POST['openid_url'])) {
                // Login via posted form
                $result = user_login_openid($_POST['openid_url']);
            } elseif (!empty($_POST['username'])) {
                if (!empty($_POST['password_md5'])) {
                    // Login via posted form with md5 password
                    $result = user_login_basic($_POST['username'], $_POST['password_md5']);
                } elseif (!empty($_POST['password'])) {
                    // Login via posted form
                    $result = user_login_basic($_POST['username'], md5($_POST['password']));
                } else {
                    // Basic login without a password???
                    $result = FALSE;
                    props_error("You must enter a password.");
                }
            } else {
                $result = FALSE;
                props_error("Login request detected without login details.");
            }

            if ($result === FALSE) {
                if ($onfailure) {
                    props_setkey('request.cmd', $onfailure);
                }
                break;
            } elseif ($result === TRUE) {
                if ($onsuccess) {
                    props_setkey('request.cmd', $onsuccess);
                }
                break;
            }

            break;

        case 'logout':
            return user_logout();
            break;

        case 'activate':
            return user_activate();
            break;

        case 'openid_verify':
            return user_openid_verify();
            break;
    }
}

/**
 * Login user basic
 *
 * On success these session variables are available:
 * - <b>$_SESSION['PROPS_USER']['authenticated']</b>
 * - <b>$_SESSION['PROPS_USER']['user_id']</b>
 * - <b>$_SESSION['PROPS_USER']['username']</b>
 * - <b>$_SESSION['PROPS_USER']['email_address']</b>
 * - <b>$_SESSION['PROPS_USER']['credits']</b>
 *
 * Use 'PersistentCookie' to remember logins.
 *
 * Example:
 * <code>
 * <form name="login" action="./" method="post">
 *   <input type="hidden" name="op" value="login" />
 *   Username: <input type="text" size="12" name="username" />
 *   Password: <input type="password" size="12" name="password" />
 *   Remember me: <input type="checkbox" name="PersistentCookie" value="1" />
 *   <input type="submit" value="Log In" />
 *   <a href="{gen_url module='users' cmd='registrationform'}">Create FREE account</a>&nbsp;|&nbsp;
 *   <a href="{gen_url module='users' cmd='forgotpassword'}">Forgot password?</a>
 * </form>
 * </code>
 *
 * @param   string  $username
 * @param   string  $password_md5  md5 password
 * @return  bool    TRUE on success, FALSE on failure
 */
function user_login_basic($username, $password_md5)
{
    // Sanitize supplied username and password to prevent against
    // passing of malicious SQL statements.
    $username = preg_replace('|[^'.VALIDATE_USERNAME.']|', '', $username, TRUE);
    $password_md5 = preg_replace('|[^'.VALIDATE_MD5.']|', '', $password_md5, TRUE);

    // Check submitted login/password against db
    $q  = "SELECT * FROM props_users "
        . "WHERE username = '" . sql_escape_string($username) . "' "
        . "AND password_md5 = '$password_md5'";
    $result = sql_query($q);
    if (sql_num_rows($result) == 1) {
        $row = sql_fetch_object($result);

        if ($row->user_type == PROPS_USERTYPE_CLOSED) {
            props_error("This account is closed. Please contact a site administrator for more information.");
            return FALSE;
        }

        if (!empty($row->activation_key)) {
            props_error("You must activate this account first.");
            return FALSE;
        }

        // Set initial values registered
        // Using an array in stead of an object to prevent STRICT error messages
        $_SESSION['PROPS_USER']['authenticated'] = TRUE;
        $_SESSION['PROPS_USER']['user_id'] = $row->user_id;
        $_SESSION['PROPS_USER']['user_type'] = (!empty($row->user_type)) ? $row->user_type : PROPS_USERTYPE_GUEST;
        $_SESSION['PROPS_USER']['group_id'] = $row->group_id;
        $_SESSION['PROPS_USER']['username'] = $row->username;
        $_SESSION['PROPS_USER']['fullname'] = $row->fullname;
        $_SESSION['PROPS_USER']['email_address'] = $row->email_address;
        $_SESSION['PROPS_USER']['language'] = $row->language;
        $_SESSION['PROPS_USER']['registered'] = $row->registered;
        $_SESSION['PROPS_USER']['last_login'] = $row->last_login;

        // Cookie expire time
        $expire = time()+60*60*24*360; // 360 days

        // Remember me option
        if (isset($_POST['PersistentCookie']) || (isset($_COOKIE['PROPS_USER']) && isset($_COOKIE['PROPS_PW_MD5']))) {
            setcookie('PROPS_USER', $username, $expire);
            setcookie('PROPS_PW_MD5', $password_md5, $expire);
            props_error("You are logged in with cookies.");
        } else {
            props_error("You are logged in.");
        }

        if ($_SESSION['PROPS_USER']['username'] == 'admin' && $password_md5 == '5f4dcc3b5aa765d61d8327deb882cf99') {
            props_error("Default admin account settings detected. For security reasons, please change the default password.", PROPS_E_WARNING);
        }

        // Update language
        setcookie('PROPS_LANGUAGE', $_SESSION['PROPS_USER']['language'], $expire);

        // Update user record
        $q  = "UPDATE props_users SET "
            . "last_login = NOW(), "
            . "last_ip = '" . props_get_ipaddress() . "' "
            . "WHERE user_id = " . $_SESSION['PROPS_USER']['user_id'] . "";
        sql_query($q);

        // Regenerate session_id for security
        props_session::regenerate_id();

        return TRUE;
    } else {
        props_error("Invalid login. Please check your username and password.");

        // Delete cookies to prevent login loops
        setcookie('PROPS_USER', '', time()-3600);
        setcookie('PROPS_PW_MD5', '', time()-3600);

        unset($_SESSION['PROPS_USER']['authenticated']);
        // Set to default guest user
        $_SESSION['PROPS_USER']['user_id'] = ANONYMOUS_USER_ID;
        $_SESSION['PROPS_USER']['user_type'] = PROPS_USERTYPE_GUEST;

        return FALSE;
    }
}

/**
 * Login user with OpenID
 *
 * On success these session variables are available:
 * - <b>$_SESSION['PROPS_USER']['authenticated']</b>
 * - <b>$_SESSION['PROPS_USER']['user_id']</b>
 * - <b>$_SESSION['PROPS_USER']['username']</b>
 * - <b>$_SESSION['PROPS_USER']['email_address']</b>
 * - <b>$_SESSION['PROPS_USER']['credits']</b>
 *
 * Use 'PersistentCookie' to remember logins.
 *
 * Example:
 * <code>
 * <form name="login" action="./" method="post">
 *   <input type="hidden" name="op" value="login" />
 *   OpenID: <input class="iconopenid" type="text" size="12" name="openid_url" />
 *   Remember me: <input type="checkbox" name="PersistentCookie" value="1" />
 *   <input type="submit" value="Login" />
 *   <a href="{gen_url module='users' cmd='registrationform'}">Create FREE account</a><br />
 *   <a target='_blank' href='http://openid.net/'>What is OpenID?</a><br />
 *   <a target='_blank' href='http://www.getmyopenid.com/registration/register.html'>I want my openid url</a>
 * </form>
 * </code>
 *
 * @param   string  $username
 * @param   string  $password_md5  md5 password
 * @return  bool    TRUE on success, FALSE on failure
 */
function user_login_openid($openid_url)
{
    if (props_getkey('config.openid.enable') !== TRUE) {
        props_error(props_gettext("OpenID verification is not enabled."));
        return FALSE;
    }

    // load lib
    props_loadLib('openid');

    // Create a consumer object
    $consumer = props_openid_getConsumer();

    // Begin the OpenID authentication process.
    $auth_request = $consumer->begin($openid_url);

    // Handle failure status return values.
    if (!$auth_request) {
        props_error("OpenID authentication error.", PROPS_E_WARNING);
        return FALSE;
    }

/* Current code */
    $auth_request->addExtensionArg('sreg', 'required', 'nickname,fullname,email');

    // Redirect the user to the OpenID server for authentication. Store
    // the token for this authentication so we can verify the response.
    $redirect_url = $auth_request->redirectURL(props_openid_getTrustRoot(),
                                               props_openid_getReturnTo());
    header("Location: ".$redirect_url);
    exit;
/* End current code */

/* Beta code for the new openid libs *

    $sreg_request = Auth_OpenID_SRegRequest::build(array('nickname', 'fullname', 'email'));

    if ($sreg_request) {
        $auth_request->addExtension($sreg_request);
    }

    // Redirect the user to the OpenID server for authentication.
    // Store the token for this authentication so we can verify the
    // response.

    // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
    // form to send a POST request to the server.
    if ($auth_request->shouldSendRedirect()) {
        $redirect_url = $auth_request->redirectURL(props_openid_getTrustRoot(),
                                                   props_openid_getReturnTo());

        // If the redirect URL can't be built, display an error
        // message.
        if (Auth_OpenID::isFailure($redirect_url)) {
            props_error("Could not redirect to server: " . $redirect_url->message, PROPS_E_WARNING);
        } else {
            // Send redirect.
            header("Location: ".$redirect_url);
        }
    } else {
        // Generate form markup and render it.
        $form_id = 'openid_message';
        $form_html = $auth_request->formMarkup(props_openid_getTrustRoot(),
                                               props_openid_getReturnTo(),
                                               false, array('id' => $form_id));

        // Display an error if the form markup couldn't be generated;
        // otherwise, render the HTML.
        if (Auth_OpenID::isFailure($form_html)) {
            props_error("Could not redirect to server: " . $redirect_url->message, PROPS_E_WARNING);
        } else {
            $page_contents = array(
               "<html><head><title>",
               "OpenID transaction in progress",
               "</title></head>",
               "<body onload='document.getElementById(\"".$form_id."\").submit()'>",
               $form_html,
               "</body></html>");

            print implode("\n", $page_contents);
            exit;
        }
    }
/* End beta code */
}

function user_openid_verify()
{
    // load lib
    props_loadLib('openid');

    // Create a consumer object
    $consumer = props_openid_getConsumer();

    // Complete the authentication process.
    //$response = $consumer->complete();
    $response = $consumer->complete($_GET);

    if ($response->status == Auth_OpenID_CANCEL) {
        // This means the authentication was cancelled.
        props_error("OpenID verification cancelled.", PROPS_E_WARNING);
        // Delete cookies to prevent login loops
        setcookie('PROPS_OPENID', '', time()-3600);
        return FALSE;
    } elseif ($response->status == Auth_OpenID_FAILURE) {
        props_error("OpenID authentication failed:".' '.$response->message, PROPS_E_WARNING);
        // Delete cookies to prevent login loops
        setcookie('PROPS_OPENID', '', time()-3600);
        return FALSE;
    } elseif ($response->status == Auth_OpenID_SUCCESS) {
        // This means the authentication succeeded.
        $openid = $response->identity_url;
        $esc_identity = htmlspecialchars($openid, ENT_QUOTES);
        $esc_identity = '<a href="'.$esc_identity.'">'.$esc_identity.'</a>';
        props_error(sprintf(props_gettext("You have successfully verified %s as your identity."), $esc_identity));

        if ($response->endpoint->canonicalID && props_getkey('config.debug_mode') === TRUE) {
            props_error('XRI CanonicalID: '.$response->endpoint->canonicalID);
        }

        //$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
        //$sreg = $sreg_resp->contents();
        $sreg = $response->extensionResponse('sreg');

        $_SESSION['PROPS_USER']['openid_verified'] = TRUE;
        $_SESSION['PROPS_USER']['openid_url'] = $openid;
        $_SESSION['PROPS_USER']['openid_username'] = (isset($sreg['nickname'])) ? $sreg['nickname'] : '';
        $_SESSION['PROPS_USER']['openid_fullname'] = (isset($sreg['fullname'])) ? $sreg['fullname'] : '';
        $_SESSION['PROPS_USER']['openid_email_address'] = (isset($sreg['email'])) ? $sreg['email'] : '';

        // Check submitted login/password against db
        $q  = "SELECT * FROM props_users "
            . "WHERE openid_url = '" . sql_escape_string($openid) . "'";
        $result = sql_query($q);
        if (sql_num_rows($result) == 1) {
            // User found, get details
            $row = sql_fetch_object($result);

            if ($row->user_type == PROPS_USERTYPE_CLOSED) {
                props_error("This account is closed. Please contact a site administrator for more information.");
                return FALSE;
            }

            if (!empty($row->activation_key)) {
                props_error("You must activate this account first.", PROPS_E_WARNING);
                return FALSE;
            }

            // Set initial values registered
            // Using an array in stead of an object to prevent STRICT error messages
            $_SESSION['PROPS_USER']['authenticated'] = TRUE;
            $_SESSION['PROPS_USER']['user_id'] = $row->user_id;
            $_SESSION['PROPS_USER']['user_type'] = (!empty($row->user_type)) ? $row->user_type : PROPS_USERTYPE_GUEST;
            $_SESSION['PROPS_USER']['group_id'] = $row->group_id;
            $_SESSION['PROPS_USER']['username'] = $row->username;
            $_SESSION['PROPS_USER']['fullname'] = $row->fullname;
            $_SESSION['PROPS_USER']['email_address'] = $row->email_address;
            $_SESSION['PROPS_USER']['language'] = $row->language;
            $_SESSION['PROPS_USER']['registered'] = $row->registered;
            $_SESSION['PROPS_USER']['last_login'] = $row->last_login;

            // Cookie expire time
            $expire = time()+60*60*24*360; // 360 days

            // Remember me option
            if (isset($_POST['PersistentCookie']) || isset($_COOKIE['PROPS_OPENID'])) {
                $expire = time()+60*60*24*360; // 360 days
                setcookie('PROPS_OPENID', $openid, $expire);
                props_error("You are logged in with cookies.");
            } else {
                props_error("You are logged in.");
            }

            // Update language
            setcookie('PROPS_LANGUAGE', $_SESSION['PROPS_USER']['language'], $expire);

            // Update user record
            $q  = "UPDATE props_users SET "
                . "last_login = NOW(), "
                . "last_ip = '" . props_get_ipaddress() . "' "
                . "WHERE user_id = " . $_SESSION['PROPS_USER']['user_id'] . "";
            sql_query($q);

            // Regenerate session_id for security
            props_session::regenerate_id();

            return TRUE;
        } else {
            unset($_SESSION['PROPS_USER']['authenticated']);
            // Set to default guest user
            $_SESSION['PROPS_USER']['user_id'] = ANONYMOUS_USER_ID;
            $_SESSION['PROPS_USER']['user_type'] = PROPS_USERTYPE_GUEST;

            if (defined('PROPS_ACP')) {
                props_error("Please register first at our site.");
                $url = props_getkey('config.url.root') . '?cmd=users-register_openid&amp;username='.$_SESSION['PROPS_USER']['openid_username'].'&amp;email_address='.$_SESSION['PROPS_USER']['openid_email_address'];
                props_error('<a href="'.$url.'">'.props_gettext("Register").'</a>');
            } else {
                props_error("Please register first.");
                props_setkey('request.cmd', 'users-register_openid');
                props_setkey('request.username', $_SESSION['PROPS_USER']['openid_username']);
                props_setkey('request.email_address', $_SESSION['PROPS_USER']['openid_email_address']);
            }
            return FALSE;
        }
    }
}

/**
 * Logout user
 */
function user_logout()
{
    // Save the session id
    $session_id = session_id();

    // Unset all of the session variables.
    unset($_SESSION['PROPS_USER']);
    session_unregister('PROPS_USER');
    $_SESSION = array();

    // Delete the session cookie.
    if (isset($_COOKIE[session_name()])) {
       setcookie(session_name(), '', time()-42000, '/');
    }

    // Delete cookies to prevent login loops
    setcookie('PROPS_USER', '', time()-3600);
    setcookie('PROPS_PW_MD5', '', time()-3600);
    setcookie('PROPS_OPENID', '', time()-3600);

    // Finally, destroy the session.
    session_destroy();
    props_session::destroy($session_id);

    // Set to default guest user
    $_SESSION['PROPS_USER']['user_id'] = ANONYMOUS_USER_ID;
    $_SESSION['PROPS_USER']['user_type'] = PROPS_USERTYPE_GUEST;

    props_error("You are now logged out.");
    return '';
}

/**
 * Checks if user is logged in
 * @return  bool  TRUE on success, FALSE on failure
 */
function user_is_logged_in()
{
    if (isset($_SESSION['PROPS_USER']['authenticated'])) {
        // User logged in
        return TRUE;
    }

    // No user logged in
    return FALSE;
}

/**
 * Returns user details
 *
 * Possible $var options:
 * - <b>authenticated</b>
 * - <b>user_id</b>
 * - <b>username</b>
 * - <b>email_address</b>
 * - <b>credits</b>
 *
 * @return  bool  TRUE on success, FALSE on failure
 */
function user_get_details($var)
{
    if (isset($_SESSION['PROPS_USER'][$var])) {
        // Return value
        return $_SESSION['PROPS_USER'][$var];
    }

    return '';
}

/**
 * Update archive credits
 *
 * @param   int   $amount  credit amount to add / deduct
 * @return  bool  TRUE on success, FALSE on failure
 */
function user_update_credit($amount)
{
    // +/- amount
    // Update $_SESSION['PROPS_USER']['credits']
}

/**
 * Register user
 *
 * @param   string  $username
 * @param   string  $password  md5
 * @param   string  $email_address
 * @return  mixed   user_id or FALSE on failure
 */
function user_register($username, $password, $email_address, $openid_url)
{
    // Check if username is free
    if (sql_num_rows(sql_query("SELECT user_id FROM props_users WHERE email_address = '" . sql_escape_string($email_address) . "'"))) {
        $GLOBALS['PROPS_ERRORSTACK']['email_address']['message'] = props_gettext("This email address is already registered.");
    }
    // Check if email adres is free
    if (sql_num_rows(sql_query("SELECT user_id FROM props_users WHERE username = '" . sql_escape_string($username) . "'"))) {
        $GLOBALS['PROPS_ERRORSTACK']['username']['message'] = props_gettext("This username is already taken.");
    }

    // If errors, exit
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        return FALSE;
    }

    $activation_key = md5(props_random_password(10));
    if (!empty($openid_url)) {
        // Register user with OpenID
        $openid_url = sql_escape_string($openid_url);
        $password_md5 = FALSE;
    } else {
        // Register user
        $openid_url = NULL;
        $password_md5 = md5($password);
    }

    // Add user
    $q  = "INSERT INTO props_users SET "
        . "username = '" . sql_escape_string($username) . "', "
        . "user_type = " . PROPS_USERTYPE_GUEST . ", "
        . "password_md5 = '$password_md5', "
        . "openid_url = '$openid_url', "
        . "email_address = '" . sql_escape_string($email_address) . "', "
        . "registered = NOW(), "
        . "activation_key = '$activation_key'";
    $user_id = sql_identity_insert($q);

    // Load PHPmailer
    props_loadLib('sendmail,url');

    // Generate activation url
    $activation_url = genurl(array('cmd'=>'activate', 'uid'=>$user_id, 'key'=>$activation_key), FALSE);
    // Generate activation url
    $activation_page = genurl(array('cmd'=>'users-activate'), FALSE);

    // Assemble the email
    $mail = new props_sendmail();

    // Set headers and body text
    $mail->Sender   = props_getkey('config.mail.bounce_address');
    $mail->From     = props_getkey('config.mail.from_address');
    $mail->FromName = props_getkey('config.mail.from_name');
    $mail->Subject  = props_gettext("Registration information from").' '.props_getkey('config.publication.name');
    $mail->AddReplyTo(props_getkey('config.mail.from_address'), props_getkey('config.mail.from_name'));
    $mail->AddAddress($email_address, $username);
    $mail->Body  =
          $username .",".LF
        . sprintf(props_gettext("This email has been sent from %s."), props_getkey('config.url.root')).LF.LF
        . sprintf(props_gettext("You have received this email because this email address was used during registration for %s."), props_getkey('config.publication.name')).LF.LF
        . "------------------------------------------------".LF
        . props_gettext("IMPORTANT!").LF
        . "------------------------------------------------".LF.LF
        . props_gettext("If you did not register at our site, please disregard this email. You do not need to unsubscribe or take any further action.").LF.LF
        . "------------------------------------------------".LF
        . props_gettext("Activation instructions").LF
        . "------------------------------------------------".LF.LF
        . props_gettext("We require that you validate your registration to ensure that the email address you entered was correct."). ' '
        . props_gettext("This protects against unwanted spam and malicious abuse.").LF.LF
        . props_gettext("Simply click on the link below and complete the rest of the form:").LF.LF
        . $activation_url.LF.LF
        . "------------------------------------------------".LF
        . props_gettext("Not working?").LF
        . "------------------------------------------------".LF.LF
        . props_gettext("If you could not validate your action by clicking on the link, please visit this page:").LF.LF
        . $activation_page.LF.LF
        . props_gettext("It will ask you for an user id number and your key. These are shown below:").LF.LF
        . props_gettext("User ID").': '.$user_id.LF.LF
        . props_gettext("Key").': '.$activation_key.LF.LF
        . props_gettext("Please cut and paste, or type those numbers into the corresponding fields in the form.").LF.LF
        . props_gettext("If you still experience problems, please contact an administrator to rectify the problem.").LF.LF
        . props_gettext("Thank you for registering and enjoy your stay!").LF.LF
        . props_gettext("Regards").','.LF.LF
        . props_getkey('config.mail.from_name').LF
        . props_getkey('config.url.root').LF;
    $mail->WordWrap = '72';

    if($mail->Send()) {
        props_error(sprintf(props_gettext("An email with activation instructions has been send to %s."), $email_address));
    } else {
        $GLOBALS['PROPS_ERRORDESC'] = props_gettext("Error sending email.");
        trigger_error($mail->ErrorInfo, E_USER_WARNING);
    }

    return $user_id;
}

/**
 * Activate user
 *
 * @return  bool  TRUE on success, FALSE on failure
 */
function user_activate()
{
    // Get vars
    $user_id = props_getrequest('uid', VALIDATE_INT);
    $activation_key = props_getrequest('key', VALIDATE_MD5);

    // If errors, exit
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error("There was an error activating this account.", PROPS_E_WARNING);
        return FALSE;
    }

    // Check submitted details against db
    $q  = "SELECT * FROM props_users "
        . "WHERE user_id = $user_id "
        . "AND activation_key = '$activation_key'";
    $result = sql_query($q);
    if (sql_num_rows($result) != 1) {
        props_error("This account cannot be activated.", PROPS_E_WARNING);
        props_setkey('request.cmd', 'users-activate');
        return FALSE;
    }

    // Check user type
    $row = sql_fetch_object($result);

    // Activate user
    $q  = "UPDATE props_users SET "
        . "activation_key = NULL, ";

    // Upgrade to registerd user
    if ($row->user_type == PROPS_USERTYPE_GUEST) {
        $q .= "user_type = " . PROPS_USERTYPE_USER . " ";
    }

    $q .= "WHERE user_id = " . $user_id . "";
    $result = mysql_query($q);

    if ($result) {
        props_error("The account is now activated and can be used.");
        props_setkey('request.cmd', 'users-login');
        return TRUE;
    } else {
        trigger_error("There was an error activating this account.", E_USER_ERROR);
        props_setkey('request.cmd', 'users-activate');
        return FALSE;
    }
}

/**
 * Checks if an user has a given privilege
 *
 * @param   string  $module    module
 * @param   string  $function  function
 * @return  bool    TRUE on success, FALSE on failure
 */
function user_has_priv($module, $function)
{
    return user_check_priv($module, $function, PROPS_PRIVTYPE_USER);
}

/**
 * Checks if an admin has a given privilege
 *
 * @param   string  $module    module
 * @param   string  $function  function
 * @return  bool    TRUE on success, FALSE on failure
 */
function admin_has_priv($module, $function)
{
    return user_check_priv($module, $function, PROPS_PRIVTYPE_ADMIN);
}

/**
 * Checks if an user has a given privilege
 *
 * @param   string  $module    module
 * @param   string  $function  function
 * @return  bool    TRUE on success, FALSE on failure
 * @private
 */
function user_check_priv($module, $function, $type)
{
    // Only allow administrators admin rights
    if ($type == PROPS_PRIVTYPE_ADMIN && (!((PROPS_USERTYPE_FOUNDER|PROPS_USERTYPE_ADMIN) & $_SESSION['PROPS_USER']['user_type'] ))) {
        return FALSE;
    }

    if (!isset($GLOBALS['PROPS_USERPRIVS'])) {
        $GLOBALS['PROPS_USERPRIVS'] = array();
        $group_id = (isset($_SESSION['PROPS_USER']['group_id'])
              && !empty($_SESSION['PROPS_USER']['group_id']))
              ? $_SESSION['PROPS_USER']['group_id'] : NULL;

        if ($_SESSION['PROPS_USER']['user_type']  == PROPS_USERTYPE_FOUNDER) {
            // Founder, get all rights
            $q  = "SELECT * FROM props_users_privs "
                . "ORDER BY module ASC, in_menu ASC, function ASC";
        } elseif (!empty($group_id)) {
            // If group id, select privs from group id
            $q  = "SELECT p.* FROM props_users_privs AS p, props_users_groupprivs AS gp "
                . "WHERE gp.group_id = " . $group_id . " "
                . "  AND gp.priv_id = p.priv_id "
                . "ORDER BY module ASC, in_menu ASC, function ASC";
        } else {
            // Fallback to default priv group for user type
            $q  = "SELECT p.* FROM props_users_privs AS p, props_users_groupprivs AS gp, "
                . "  props_users_groups AS g "
                . "WHERE g.default_user_type = " . $_SESSION['PROPS_USER']['user_type']  . " "
                . "  AND g.group_id = gp.group_id "
                . "  AND gp.priv_id = p.priv_id "
                . "ORDER BY module ASC, in_menu ASC, function ASC";
        }

        $result = sql_query($q);

        while($row = sql_fetch_object($result)) {
            $GLOBALS['PROPS_USERPRIVS'][$row->type][$row->module][$row->function] = $row->in_menu;
        }

        // Free result memory
        sql_free_result($result);
    }

    // Give default access for admins and founders
    if ($type == PROPS_PRIVTYPE_ADMIN && $module == 'adminmain' && $function == 'mainmenu') {
        return TRUE;
    } elseif ($type == PROPS_PRIVTYPE_ADMIN && $module == 'system' && $function == 'update' && $_SESSION['PROPS_USER']['user_type'] == PROPS_USERTYPE_FOUNDER) {
        return TRUE;
    } elseif ($type == PROPS_PRIVTYPE_ADMIN && $module == 'system' && $function == 'update_tags' && $_SESSION['PROPS_USER']['user_type'] == PROPS_USERTYPE_FOUNDER) {
        return TRUE;
    } elseif ($type == PROPS_PRIVTYPE_ADMIN && $module == 'system' && $function == 'update_privs' && $_SESSION['PROPS_USER']['user_type'] == PROPS_USERTYPE_FOUNDER) {
        return TRUE;
    } elseif (isset($GLOBALS['PROPS_USERPRIVS'][$type][$module][$function])) {
        return TRUE;
    } else {
        return FALSE;
    }
}

?>
