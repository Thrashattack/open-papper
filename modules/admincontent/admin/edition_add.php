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
 * @version     $Id: edition_add.php,v 1.12 2007/12/11 15:46:28 roufneck Exp $
 */

/**
 * @admintitle  Add edition
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_edition_add()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'editions_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'edition_add');

    // Get the needed posted vars here.
    $label = props_getrequest('label', VALIDATE_TEXT, 'SANITIZE');

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

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                $q  = "INSERT INTO props_editions "
                    . "SET label = '" . sql_escape_string($label) . "'";
                sql_query($q);

                // Redirect
                props_redirect(TRUE);
            }
            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Settings") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Label") . '</label><br />' . props_gettext("Optional") . '</dt>'.LF
        .'    <dd><input class="large" type="text" id="label" name="label" value="' . htmlspecialchars($label) . '" /></dd>'.LF
        .((props_geterror('label')) ? '      <dd>' . props_geterror('label') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'</fieldset>'.LF
        .'  <p>'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'  </p>'.LF
        .'</form>'.LF;

    return $output;

}

?>
