<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  adminmain
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
 * @version     $Id: update.php,v 1.1 2007/10/19 16:24:15 roufneck Exp $
 */

/**
 * @admintitle  System update
 * @adminprivs  update_tags   Update template tags
 * @adminprivs  update_privs  Update user privs
 * @adminprivs  update_i18n   Update translation strings
 * @adminnav    3
 * @return  string  admin screen html content
 */
function admin_update()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'info');
    admin_sidebar_add('adminmain', 'about');

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext('.system.update_tags'):
            // If no errors, do update, otherwise drop through and display errors
            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'update_tags')) {
                props_error("You do not have permission to perform the selected action.");
            } elseif (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                props_loadLib('system');

                // Scan dir
                system_scan(PROPS_ROOT.'modules');

                // Sort arrays
                ksort($GLOBALS['PROPS_SYSTEM_TAGS']);
                ksort($GLOBALS['PROPS_SYSTEM_PRIVS']);
                ksort($GLOBALS['PROPS_SYSTEM_STRINGS']);

                // Update
                $debug_info = system_update_tags();
            }
            break;

        case props_gettext('.system.update_privs'):
            // If no errors, do update, otherwise drop through and display errors
            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'update_privs')) {
                props_error("You do not have permission to perform the selected action.");
            } if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                props_loadLib('system');

                // Scan dir
                system_scan(PROPS_ROOT.'modules');

                // Sort arrays
                ksort($GLOBALS['PROPS_SYSTEM_TAGS']);
                ksort($GLOBALS['PROPS_SYSTEM_PRIVS']);
                ksort($GLOBALS['PROPS_SYSTEM_STRINGS']);

                // Update
                $debug_info = system_update_privs();
            }
            break;

        case props_gettext('.system.update_i18n'):
            // If no errors, do update, otherwise drop through and display errors
            if (!admin_has_priv($GLOBALS['PROPS_MODULE'], 'update_i18n')) {
                props_error("You do not have permission to perform the selected action.");
            } if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                props_loadLib('system');

                // Scan dir
                system_scan(PROPS_ROOT.'lib');
                system_scan(PROPS_ROOT.'modules');

                // Sort arrays
                ksort($GLOBALS['PROPS_SYSTEM_TAGS']);
                ksort($GLOBALS['PROPS_SYSTEM_PRIVS']);
                ksort($GLOBALS['PROPS_SYSTEM_STRINGS']);

                // Update
                $debug_info = system_update_locales(PROPS_ROOT.'locale');
                $debug_info .= 'Updated locale files.'.BR;
            }
            break;

    } // END switch

    $output =
         '<fieldset>'.LF
        .'  <legend>' . props_gettext('.system.update') . '</legend>'.LF
        .'<form action="./" method="post">'.LF
        .'  <input name="module" type="hidden" value="system" />'.LF
        .'  <input name="function" type="hidden" value="update" />'.LF;

    if (admin_has_priv('system', 'update_tags')) {
        $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext('.system.update_tags') . '</label><br />' . props_gettext("Tags are used in the templates.") . '</dt>'.LF
        .'    <dd><input class="button" name="op" type="submit" value="' . props_gettext('.system.update_tags') . '" /></dd>'.LF
        .'  </dl>'.LF;
    }

    if (admin_has_priv('system', 'update_privs')) {
        $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext('.system.update_privs') . '</label><br />' . props_gettext("Update all user privs (frontpage and admin control panel).") . '</dt>'.LF
        .'    <dd><input class="button" name="op" type="submit" value="' . props_gettext('.system.update_privs') . '" /></dd>'.LF
        .'  </dl>'.LF;
    }

    if (admin_has_priv('system', 'update_i18n')) {
        $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext('.system.update_i18n') . '</label><br />' . props_gettext("Update the locales with all the detected strings.") . '</dt>'.LF
        .'    <dd><input class="button" name="op" type="submit" value="' . props_gettext('.system.update_i18n') . '" /></dd>'.LF
        .'  </dl>'.LF;
    }

    $output .=
         '</form>'.LF
        .'</fieldset>'.LF;

    if (isset($debug_info)) {
        $output .= $debug_info;
    }

    return $output;
}

?>
