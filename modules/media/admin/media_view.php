<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  media
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
 * @version     $Id: media_view.php,v 1.1 2007/09/21 12:23:38 roufneck Exp $
 */

// loadLibs
props_loadLib('media,sections');

/**
 * @admintitle  View original media
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_media_view()
{
    // Get the needed posted vars here
    $media_id = props_getrequest('media_id', VALIDATE_INT);

    // Get details from DB.
    $q  = "SELECT * FROM props_media "
        . "WHERE media_id = $media_id ";
    $result = sql_query($q);
    $media = sql_fetch_assoc($result);

    if (!sql_num_rows($result)) {
        props_error("Invalid ID.");
        return '<p class="center"><a class="button" href="javascript:this.window.close();">' . props_gettext("Close") . '</a></p>';
        exit;
    }

    media_get_details($media, '', '', '');

    $output =
         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.LF
        .'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en">'.LF
        .'<head>'.LF
        .'  <title>PROPS Admin - ' . props_gettext('.' . $GLOBALS['PROPS_MODULE'] . '.' . $GLOBALS['PROPS_FUNCTION']) . '</title>'.LF
        .'  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.LF
        .'  <meta http-equiv="Content-Language" content="en" />'.LF
        .'  <meta name="robots" content="noindex,nofollow" />'.LF
        .'  <meta name="keywords" content="PROPS - Open Source News Publishing Platform" />'.LF
        .'  <link rel="stylesheet" type="text/css" media="all" href="props.admin.css" />'.LF
        .'  <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="props.iefix.css" /><!{endif]-->'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'props.admin.js"></script>'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'swfobject.js"></script>'.LF
        .'</head>'.LF
        .'<body>'.LF
        .'  <h1 class="center">' . props_gettext('.' . $GLOBALS['PROPS_MODULE'] . '.' . $GLOBALS['PROPS_FUNCTION']) . '</h1>'.LF
        .'<p  style="text-align: center;"><a class="button" href="javascript:this.window.close();">' . props_gettext("Close") . '</a></p>'.LF
        .'<div style="text-align: center;">'.$media['embedded'].'</div>'.LF
        .'<p  style="text-align: center;"><a class="button" href="javascript:this.window.close();">' . props_gettext("Close") . '</a></p>'.LF
        .'</body>'.LF
        .'</html>'.LF;

    echo $output;
    exit;
}

?>
