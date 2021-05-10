<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  archives
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
 * @version     $Id: subscription_plan_add.php,v 1.9 2007/12/11 15:46:29 roufneck Exp $
 */

/**
 * @admintitle  Add subscription plan
 * @adminnav    3
 * @return  string  admin screen html content
 */
function admin_subscription_plan_add()
{
    // Get the needed posted vars here.
    $description = props_getrequest('description', VALIDATE_TEXT, '!EMPTY,MAX128', TRUE);
    $credits = props_getrequest('credits', '-'.VALIDATE_NUM, '!EMPTY,MAX5', TRUE);
    $days_until_expire = props_getrequest('days_until_expire', VALIDATE_NUM, '!EMPTY,MAX4');
    $amount = props_getrequest('amount', VALIDATE_CURRENCY, '!EMPTY,MAX7');

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE, array('function'=>'subscription_plans_manage'));
            break;

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // Assemble SQL. Use sql_escape_string for all vars except integers to prevent DB hacking.
                $q  = "INSERT INTO props_archives_subscription_plans SET "
                    . "description = '" . sql_escape_string($description) . "', "
                    . "credits = '" . $credits . "', "
                    . "days_until_expire = '" . $days_until_expire . "', "
                    . "amount = '" . $amount . "' ";
                sql_query($q);

                // Redirect
                props_redirect(TRUE, array('function'=>'subscription_plans_manage'));
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
        .'  <legend>' . props_gettext("Details") . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Plan description") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="description" name="description" value="' . htmlspecialchars($description) . '" /></dd>'.LF
        .((props_geterror('description')) ? '<dd>' . props_geterror('description') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Number of credits") . '</label><br />' . props_gettext("Set to -1 for unlimited credits.") . '</dt>'.LF
        .'    <dd><input class="large" type="text" id="credits" name="credits" value="' . htmlspecialchars($credits) . '" /></dd>'.LF
        .((props_geterror('credits')) ? '<dd>' . props_geterror('credits') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Days until expire") . '</label><br />' . props_gettext("Set to 0 for no expiration date.") . '</dt>'.LF
        .'    <dd><input class="large" type="text" id="days_until_expire" name="days_until_expire" value="' . htmlspecialchars($days_until_expire) . '" /></dd>'.LF
        .((props_geterror('days_until_expire')) ? '<dd>' . props_geterror('days_until_expire') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Amount") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="amount" name="amount" value="' . htmlspecialchars($amount) . '" /></dd>'.LF
        .((props_geterror('amount')) ? '<dd>' . props_geterror('amount') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'</fieldset>'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
