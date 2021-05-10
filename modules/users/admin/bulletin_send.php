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
 * @version     $Id: bulletin_send.php,v 1.19 2008/03/05 22:42:15 greenie2600 Exp $
 */

/**
 * @admintitle  Send bulletin
 * @adminprivs  wysiswg_editor  Use WYSIWYG editor
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_bulletin_send()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletins_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletin_add');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletin_send');

    // Get the needed posted vars here.
    $bulletin_id = props_getrequest('bulletin_id', VALIDATE_INT, '!EMPTY');
    $subject = props_getrequest('subject', VALIDATE_TEXT, '!EMPTY');
    $preview_email = props_getrequest('preview_email', VALIDATE_EMAIL);
    $html_template = props_getrequest('html_template', VALIDATE_HTML);
    $plaintext_template = props_getrequest('plaintext_template', VALIDATE_TEXT);

    if ($bulletin_id > 0) {
        // Get details from DB.
        $q  = "SELECT props_bulletins.*, COUNT(user_id) AS count "
            . "FROM props_bulletins "
            . "LEFT JOIN props_bulletins_subscriptions ON props_bulletins.bulletin_id = props_bulletins_subscriptions.bulletin_id "
            . "WHERE props_bulletins.bulletin_id = $bulletin_id "
            . "GROUP BY props_bulletins.bulletin_id";
        $result = sql_query($q);
        $bulletin = mysql_fetch_object($result);

        if (!sql_num_rows($result)) {
            props_error("Invalid ID.");
            unset($bulletin);
        }
    }

    if (!isset($bulletin)) {
        // $q  = "SELECT * FROM props_bulletins ORDER BY bulletin_name";
        $q  = "SELECT props_bulletins.bulletin_id, bulletin_name, bulletin_shortname, COUNT(user_id) AS count "
            . "FROM props_bulletins "
            . "LEFT JOIN props_bulletins_subscriptions ON props_bulletins.bulletin_id = props_bulletins_subscriptions.bulletin_id "
            . "GROUP BY props_bulletins.bulletin_id";
        $result = sql_query($q);

        // Redirect to manage bulletins screen
        if (!sql_num_rows($result)) {
            props_error("There are no bulletins. Please create one first.");

            $output =
                 '<form action="./" method="post">'.LF
                .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
                .'<input name="function" type="hidden" value="bulletins_manage" />'.LF
                .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
                .'<p>'.LF
                .'  <input class="button" name="op" type="submit" value="' . props_gettext("OK") . '" />'.LF
                .'</p>'.LF
                .'</form>'.LF;

            return $output;
            exit;
        }

        // List bulletins
        $output =
             '<form action="./" method="post">'.LF
            .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
            .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
            .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
            .'<table class="hairline"><tr><td>'.LF
            .'  <select class="frm" name="bulletin_id">'.LF
            .'    <option value="">' . props_gettext("Select a bulletin to send") . '...</option>'.LF;

        $q  = "SELECT props_bulletins.bulletin_id, bulletin_name, COUNT(user_id) AS count "
            . "FROM props_bulletins "
            . "LEFT JOIN props_bulletins_subscriptions ON props_bulletins.bulletin_id = props_bulletins_subscriptions.bulletin_id "
            . "GROUP BY props_bulletins.bulletin_id";
        $result = sql_query($q);
        while ($row = sql_fetch_object($result)) {
            $select = ($row->bulletin_id == $bulletin_id) ? 'selected="selected"': '';
            $output .= '    <option ' . $select . ' value="' . $row->bulletin_id . '">' . htmlspecialchars($row->bulletin_name) . '&nbsp;&nbsp;&nbsp;(' . $row->count . ' subscribers)</option>'.LF;
        }

        $output .=
             '  </select>' . props_geterror('bulletin_id') . ''.LF
            .'</td></tr></table>'.LF
            .'<p>'.LF
            .'  <input class="button" name="op" type="submit" value="' . props_gettext("OK") . '" />'.LF
            .'</p>'.LF
            .'</form>'.LF;

        return $output;
    }

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Send preview"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // Extra validation of post variables
            if (empty($html_template) && empty($plaintext_template)) {
                props_error("You must enter a HTML or plaintext message, or both.");
            }

            // Extra validation of post variables
            $preview_email = props_getrequest('preview_email', VALIDATE_EMAIL, '!EMPTY');

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                // Load PHPmailer
                props_loadLib('sendmail');

                // Assemble the email
                $mail = new props_sendmail();

                // Set headers and body text
                $mail->Sender   = $bulletin->bounce_email;
                $mail->From     = $bulletin->from_email;
                $mail->FromName = $bulletin->from_name;
                $mail->Subject  = props_gettext("Bulletin preview").': '.$subject;
                $mail->AddReplyTo($bulletin->from_email, $bulletin->from_name);

                if (!empty($html_template)) {
                    $mail->IsHTML(true);
                    $mail->Body = $html_template;
                    $mail->AltBody = $plaintext_template;
                } else {
                    $mail->Body = $plaintext_template;
                }

                $mail->AddAddress($preview_email);

                if($mail->Send()) {
                    props_error(sprintf(props_gettext("A preview bulletin has been sent to %s."), $preview_email));
                } else {
                    $GLOBALS['PROPS_ERRORDESC'] = props_gettext("Error sending bulletin.");
                    trigger_error($mail->ErrorInfo, E_USER_WARNING);
                }
            }

            break;

        case props_gettext("Send bulletin"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // Extra validation of post variables
            if (empty($html_template) && empty($plaintext_template)) {
                props_error("You must enter a HTML or plaintext message, or both.");
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // No script timeout
                set_time_limit(0);

                // Load PHPmailer
                props_loadLib('sendmail');

                // Assemble the email
                $mail = new props_sendmail();

                // Set headers and body text
                $mail->Sender   = $bulletin->bounce_email;
                $mail->From     = $bulletin->from_email;
                $mail->FromName = $bulletin->from_name;
                $mail->Subject  = $subject;
                $mail->AddReplyTo($bulletin->from_email, $bulletin->from_name);

                if (!empty($html_template)) {
                    $mail->IsHTML(true);
                    $mail->Body = $html_template;
                    $mail->AltBody = $plaintext_template;
                } else {
                    $mail->Body = $plaintext_template;
                }

                $q  = "SELECT t2.username, t2.email_address FROM props_bulletins_subscriptions AS t1 "
                    . "LEFT JOIN props_users AS t2  ON t1.user_id = t2.user_id "
                    . "WHERE t1.bulletin_id = $bulletin_id "
                    . "AND activation_key = 0";
                $result = sql_query($q);

                while ($row = sql_fetch_object($result)) {
                    $mail->AddBCC($row->email_address, $row->username);
                    //echo __LINE__.": $row->username - $row->email_address".BR;
                }

                $count = sql_num_rows($result);
                if ($count) {
                    // Send mail
                    if($mail->Send()) {
                        props_error(sprintf(props_gettext("Bulletin has been send to %s subscribers."), $count), E_USER_NOTICE);
                    } else {
                        $GLOBALS['PROPS_ERRORDESC'] = props_gettext("Error sending email.");
                        trigger_error($mail->ErrorInfo, E_USER_WARNING);
                    }
                } else {
                    props_error("There are no subscribers for this bulletin.");
                }
            }
            break;

        default:
            $subject = strftime($bulletin->subject);
            $html_template = $bulletin->html_template;
            $plaintext_template = $bulletin->plaintext_template;
            break;

    } // END switch

    // Activate WYSIWYG-editor for body_content field
    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'wysiswg_editor')) {
        // Setup tiny_mce
        $GLOBALS['PROPS_WYSIWYG'] = 'html_template';
        props_setkey('wysiwyg.tiny_mce.buttons1', 'bold,italic,underline,strikethrough,separator,sup,sub,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent');
        props_setkey('wysiwyg.tiny_mce.buttons2', 'cut,copy,paste,pastetext,pasteword,separator,undo,redo,separator,forecolor,backcolor,separator,hr,link,unlink,anchor,image,separator,charmap,code');
        props_setkey('wysiwyg.tiny_mce.plugins', 'paste,advimage');
    }

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
        // Tab menu
         '<div id="tab:story">'.LF
        .'  <span id="t:story:0" class="tab-select" onclick="selectTab(this);">' . props_gettext("Settings") . '</span>'.LF
        .'  <span id="t:story:1" class="tab" onclick="selectTab(this);">' . props_gettext("HTML message") . '</span>'.LF
        .'  <span id="t:story:2" class="tab" onclick="selectTab(this);">' . props_gettext("Plaintext message") . '</span>'.LF
        .'</div>'.LF

        .'<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<input name="bulletin_id" type="hidden" value="' . $bulletin_id . '" />'.LF

        // Setup tab
         .'  <fieldset class="tabbed" id="c:story:0">'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Bulletin name") . '</label></dt>'.LF
        .'      <dd>' . htmlspecialchars($bulletin->bulletin_name) . '</dd>'.LF
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Bulletin shortname") . '</label></dt>'.LF
        .'      <dd>' . htmlspecialchars($bulletin->bulletin_shortname) . '</dd>'.LF
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("From name") . '</label></dt>'.LF
        .'      <dd>' . htmlspecialchars($bulletin->from_name) . '</dd>'.LF
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("From email") . '</label></dt>'.LF
        .'      <dd>' . htmlspecialchars($bulletin->from_email) . '</dd>'.LF
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Bounce email") . '</label><br />' . props_gettext("Email address where bounced emails will return. Can be the same as from email.") . '</dt>'.LF
        .'      <dd>' . htmlspecialchars($bulletin->from_email) . '</dd>'.LF
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Subject") . '</label><br />' . sprintf(props_gettext("Subject can contain %s format."), '<a href="http://www.php.net/strftime" target="_blank">php strftime()</a>') . '<br />Ex.: "Bulletin %e %B %Y"<br /> => "' . strftime('Bulletin %e %B %Y') . '</dt>'.LF
        .'      <dd><input class="large" type="text" id="subject" name="subject" value="' . htmlspecialchars($subject) . '" /></dd>'.LF
        .((props_geterror('subject')) ? '      <dd>' . props_geterror('subject') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Preview email address") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="preview_email" name="preview_email" value="' . htmlspecialchars($preview_email) . '" /></dd>'.LF
        .((props_geterror('preview_email')) ? '      <dd>' . props_geterror('preview_email') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <p style="clear: both; text-align: left; color: red;">' . props_gettext("NOTE: Clicking the 'Send Bulletin' button will immediately transmit the message to all subscribers. This operation CANNOT BE UNDONE. Please make sure your message is complete and accurate before sending.") . '</p>'.LF
        .'  </fieldset>'.LF

        // HTML tab
        .'  <fieldset class="tabbed" id="c:story:1" style="display: none;">'.LF
        .'    <p>' . props_gettext("Message displayed when an email client can view HTML.") . '</p>'.LF
        .'    <p><textarea style="width: 97%; height: 300px;" id="html_template" name="html_template" rows="20" cols="80">' . htmlspecialchars($html_template) . '</textarea></p>'.LF
        .'  </fieldset>'.LF

        // Plaintext tab
        .'  <fieldset class="tabbed" id="c:story:2" style="display: none;">'.LF
        .'    <p>' . props_gettext("Message displayed when an email client can not view HTML.") . '</p>'.LF
        .'    <p><textarea style="width: 97%; height: 300px;" id="plaintext_template" name="plaintext_template" rows="20" cols="80">' . htmlspecialchars($plaintext_template) . '</textarea></p>'.LF
        .'  </fieldset>'.LF

        .'  <p style="clear: both;">'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Send preview") . '" />&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Send bulletin") . '" />'.LF
        .'  </p>'.LF

        .'</form>'.LF;

    return $output;
}

?>
