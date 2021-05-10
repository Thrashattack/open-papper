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
 * @version     $Id: bulletin_add.php,v 1.13 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Add bulletin
 * @adminprivs  wysiswg_editor  Use WYSIWYG editor
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_bulletin_add()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletins_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletin_add');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletin_send');

    // Get the needed posted vars here.
    $bulletin_name = props_getrequest('bulletin_name', VALIDATE_TEXT, '!EMPTY');
    $bulletin_shortname = props_getrequest('bulletin_shortname', VALIDATE_ALPHA_LOWER.VALIDATE_NUM, '!EMPTY');
    $from_name = props_getrequest('from_name', VALIDATE_TEXT, '!EMPTY');
    $from_email = props_getrequest('from_email', VALIDATE_EMAIL, '!EMPTY');
    $bounce_email = props_getrequest('bounce_email', VALIDATE_EMAIL, '!EMPTY');
    $subject = props_getrequest('subject', VALIDATE_TEXT);
    $html_template = props_getrequest('html_template', VALIDATE_HTML);
    $plaintext_template = props_getrequest('plaintext_template', VALIDATE_TEXT);

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

            // Make sure it doesn't already exist
            if (sql_num_rows(sql_query("SELECT * FROM props_bulletins WHERE bulletin_name = '" . sql_escape_string($bulletin_name) . "' "))) {
                $GLOBALS['PROPS_ERRORSTACK']['bulletin_name']['message'] = props_gettext("This name already exists.");
            }

            if (sql_num_rows(sql_query("SELECT * FROM props_bulletins WHERE bulletin_shortname = '" . sql_escape_string($bulletin_shortname) . "' "))) {
                $GLOBALS['PROPS_ERRORSTACK']['bulletin_shortname']['message'] = props_gettext("This name already exists.");
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                $q  = "INSERT INTO props_bulletins SET "
                    . "bulletin_name = '" . sql_escape_string($bulletin_name) . "', "
                    . "bulletin_shortname = '" . sql_escape_string($bulletin_shortname) . "', "
                    . "from_name = '" . sql_escape_string($from_name) . "', "
                    . "from_email = '" . sql_escape_string($from_email) . "', "
                    . "bounce_email = '" . sql_escape_string($bounce_email) . "', "
                    . "subject = '" . sql_escape_string($subject) . "', "
                    . "html_template = '" . sql_escape_string($html_template) . "', "
                    . "plaintext_template = '" . sql_escape_string($plaintext_template) . "'";
                sql_query($q);

                // Redirect
                props_redirect(TRUE);
            }
            break;

        default:
            $from_name = props_getkey('config.mail.from_name');
            $from_email = props_getkey('config.mail.from_address');
            $bounce_email = props_getkey('config.mail.bounce_address');
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

        // Setup tab
         .'  <fieldset class="tabbed" id="c:story:0">'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Bulletin name") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="bulletin_name" name="bulletin_name" value="' . htmlspecialchars($bulletin_name) . '" /></dd>'.LF
        .((props_geterror('bulletin_name')) ? '      <dd>' . props_geterror('bulletin_name') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Bulletin shortname") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="bulletin_shortname" name="bulletin_shortname" value="' . htmlspecialchars($bulletin_shortname) . '" /></dd>'.LF
        .((props_geterror('bulletin_shortname')) ? '      <dd>' . props_geterror('bulletin_shortname') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("From name") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="from_name" name="from_name" value="' . htmlspecialchars($from_name) . '" /></dd>'.LF
        .((props_geterror('from_name')) ? '      <dd>' . props_geterror('from_name') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("From email") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="from_email" name="from_email" value="' . htmlspecialchars($from_email) . '" /></dd>'.LF
        .((props_geterror('from_email')) ? '      <dd>' . props_geterror('from_email') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Bounce email") . '</label><br />' . props_gettext("Email address where bounced emails will return. Can be the same as from email.") . '</dt>'.LF
        .'      <dd><input class="large" type="text" id="bounce_email" name="bounce_email" value="' . htmlspecialchars($bounce_email) . '" /></dd>'.LF
        .((props_geterror('bounce_email')) ? '      <dd>' . props_geterror('bounce_email') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Subject") . '</label><br />' . sprintf(props_gettext("Subject can contain %s format."), '<a href="http://www.php.net/strftime" target="_blank">php strftime()</a>') . '<br />Ex.: "Bulletin %e %B %Y"<br /> => "' . strftime('Bulletin %e %B %Y') . '</dt>'.LF
        .'      <dd><input class="large" type="text" id="subject" name="subject" value="' . htmlspecialchars($subject) . '" /></dd>'.LF
        .((props_geterror('subject')) ? '      <dd>' . props_geterror('subject') . '</dd>'.LF : '')
        .'    </dl>'.LF
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
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'  </p>'.LF

        .'</form>'.LF;

    return $output;
}

?>
