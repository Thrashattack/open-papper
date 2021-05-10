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
 * @version     $Id: info.php,v 1.2 2007/10/20 09:10:08 roufneck Exp $
 */

/**
 * @admintitle  System info
 * @adminnav    1
 * @return  string  admin screen html content
 */
function admin_info()
{
    props_loadLib('system');

    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'info');
    admin_sidebar_add('adminmain', 'about');

    $d = dir(PROPS_ROOT . 'modules');
    while($entry = $d->read()) {
        if ($entry != '.' && $entry != '..' && $entry != 'CVS') {
            if (is_dir(PROPS_ROOT . 'modules/' . $entry)) {
                if (!isset($modules_list)) $modules_list = $entry;
                else $modules_list .= ', '.$entry;
            }
        }
    }
    $d->close();

    if(empty($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = props_gettext("no value");

    $sum_media_size = system_get_dir_size(props_getkey('config.dir.media'));
    list($formatted_media_size, $media_size_unit) = props_formatByteDown($sum_media_size, 3, ($sum_media_size > 0) ? 1 : 0);

    $output =
         '<fieldset>'.LF
        .'  <legend>' . props_gettext("Server info") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Operating system") . '</label></dt>'.LF
        .'    <dd>' . php_uname() . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Server") . '</label></dt>'.LF
        .'    <dd>' . $_SERVER['SERVER_SOFTWARE'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Server name") . '</label></dt>'.LF
        .'    <dd>' . $_SERVER['SERVER_NAME'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Server address") . '</label></dt>'.LF
        .'    <dd>' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>PHP</label></dt>'.LF
        .'    <dd>' . phpversion() . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Database") . '</label></dt>'.LF
        .'    <dd>' . props_getkey('config.db.type') . ' ' . sql_server_version() . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Media files size") . '</label><br />' . props_getkey('config.dir.media') . '</dt>'.LF
        .'    <dd>'.$formatted_media_size.' '.$media_size_unit.'</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>PROPS</label></dt>'.LF
        .'    <dd>' . PROPS_VERSION . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Modules") . '</label></dt>'.LF
        .'    <dd>' . $modules_list . '</dd>'.LF
        .'  </dl>'.LF
        .'</fieldset>'.LF

        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Browser info") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>HTTP User Agent</label></dt>'.LF
        .'    <dd>' . $_SERVER['HTTP_USER_AGENT'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>IP address</label></dt>'.LF
        .'    <dd>' . props_get_ipaddress() . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>HTTP Accept</label></dt>'.LF
        .'    <dd>' . $_SERVER['HTTP_ACCEPT'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>HTTP Accept Language</label></dt>'.LF
        .'    <dd>' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>HTTP Accept Encoding</label></dt>'.LF
        .'    <dd>' . $_SERVER['HTTP_ACCEPT_ENCODING'] . '</dd>'.LF
        .'  </dl>'.LF
        .'</fieldset>'.LF;

    return $output;
}

?>
