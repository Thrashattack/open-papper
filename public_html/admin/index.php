<?php
/**
 * Admin index file
 *
 * @package     PROPS
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
 * @version     $Id: index.php,v 1.17 2008/01/07 16:17:07 roufneck Exp $
 */

// Set admin control panel to true
define('PROPS_ACP', TRUE);

//  Load common functions
include_once('../../lib/common.php');

// Load admin lib
props_loadLib('admin');

$javascript = '';
$mainnav = '';
$subnav = '';
$output = 'Unknown error.';

// If user is unauthenticated at this point display login screen
if (!isset($_SESSION['PROPS_USER']['authenticated'])) {
    $output = admin_login_screen();
} elseif (!((PROPS_USERTYPE_FOUNDER|PROPS_USERTYPE_ADMIN) & $_SESSION['PROPS_USER']['user_type'])) {
    $output = admin_restricted_area(TRUE);
    exit;
} else {
    $GLOBALS['PROPS_MODULE'] = props_getrequest('module');
    $GLOBALS['PROPS_FUNCTION'] = props_getrequest('function');

    // If set module and function
    if (empty($GLOBALS['PROPS_MODULE']) || empty($GLOBALS['PROPS_FUNCTION'])) {
        $GLOBALS['PROPS_MODULE'] = 'adminmain';
        $GLOBALS['PROPS_FUNCTION'] = 'mainmenu';
    }

    if (admin_has_priv($GLOBALS['PROPS_MODULE'], $GLOBALS['PROPS_FUNCTION'])) {
        require_once(PROPS_ROOT . 'modules/' . $GLOBALS['PROPS_MODULE'] . '/admin/' . $GLOBALS['PROPS_FUNCTION'] . '.php');
        $admin_function = 'admin_'.$GLOBALS['PROPS_FUNCTION'];
        $output = $admin_function();

        // Future???
        //static $output;
        //$GLOBALS['PROPS_FUNCTION'](&$output);
    } else {
        $output = admin_restricted_area();
    }
}

// Set default page title
if (!isset($GLOBALS['PROPS_PAGETITLE'])) {
    $GLOBALS['PROPS_PAGETITLE'] = props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']);
}

// Output headers
header('X-PUBLISHER: PROPS ' . PROPS_VERSION . ' - Open Source News Publishing Platform');
header('X-PUBLISHER-URL: http://props.sourceforge.net/');
if (props_getkey('db.charset') == 'utf8') {
    header('Content-Type: text/html; charset=UTF-8');
} else {
    header('Content-Type: text/html; charset=ISO-8859-1');
}

// Start output
ob_start();

// Display admin page
admin_display_screen($output);

ob_end_flush();

// Call once to props_pageID()to update the pageID
props_pageID();
// Shutdown props
props_shutdown();

?>
