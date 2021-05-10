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
 * @version     $Id: openid.php,v 1.3 2007/11/14 11:19:03 roufneck Exp $
 */

/**
 * Defines
 */
define('Auth_OpenID_RAND_SOURCE', props_getkey('config.openid.rand_source'));

/**
 * Require the OpenID consumer code.
 */
require_once "Auth/OpenID/Consumer.php";

/**
 * Require the Simple Registration extension API.
 */
//require_once "Auth/OpenID/SReg.php";

/**
 * Require the "file store" module, which we'll need to store OpenID
 * information.
 */
require_once "Auth/OpenID/FileStore.php";

/**
 * OpenID store
 */
function props_openid_getStore()
{
    $store_path = props_getkey('config.dir.cache').'openid/';

    if (!file_exists($store_path) && !mkdir($store_path)) {
        trigger_error('Cannot write to the cache directory.', E_USER_ERROR);
    }

    return new Auth_OpenID_FileStore($store_path);
}

/**
 * Create a consumer object
 */
function props_openid_getConsumer()
{
    $store = props_openid_getStore();
    return new Auth_OpenID_Consumer($store);
}

function props_openid_getTrustRoot()
{
    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
        $scheme .= 's';
    }

    return sprintf("$scheme://%s%s/?cmd=openid_verify",
                    $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));

    // Force frontpage url
    //return props_getkey('config.url.root');
}

function props_openid_getReturnTo()
{
    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
        $scheme .= 's';
    }

    return sprintf("$scheme://%s%s/?cmd=openid_verify",
                    $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
}

?>
