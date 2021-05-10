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
 * @version     $Id: story_revision_history.php,v 1.5 2007/09/20 08:49:47 roufneck Exp $
 */

/**
 * @admintitle  View revision history
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_story_revision_history()
{
    $story_id = props_getrequest('story_id', VALIDATE_INT);

    $output =
         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.LF
        .'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en">'.LF
        .'<head>'.LF
        .'  <title>PROPS Admin - ' . props_gettext('.' . $GLOBALS['PROPS_MODULE'] . '.story_revision_history') . '</title>'.LF
        .'  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.LF
        .'  <meta http-equiv="Content-Language" content="en" />'.LF
        .'  <meta name="robots" content="noindex,nofollow" />'.LF
        .'  <meta name="keywords" content="PROPS - Open Source News Publishing Platform" />'.LF
        .'  <link rel="stylesheet" type="text/css" media="all" href="props.admin.css" />'.LF
        .'  <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="props.iefix.css" /><!{endif]-->'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'props.admin.js"></script>'.LF
        .'</head>'.LF
        .'<body>'.LF
        .'  <h1 class="center">' . props_gettext('.' . $GLOBALS['PROPS_MODULE'] . '.story_revision_history') . '</h1>'.LF
        .'<p class="center"><a class="button" href="javascript:this.window.close();">' . props_gettext("Close") . '</a></p>'.LF;

    // Get details about current version of story
    $result = sql_query("SELECT revision, modified_stamp, modified_by, revision_description FROM props_stories WHERE story_id = $story_id");
    $row = sql_fetch_object($result);

    // Output info on current story
    $output .=
         '  <p>'.LF
        .'    <b>' . props_gettext("Version") . ':</b> ' . $row->revision . '<br />'.LF
        .'    <b>' . props_gettext("Revision date") . ':</b> ' . $row->modified_stamp . '<br />'.LF
        .'    <b>' . props_gettext("Revision by") . ':</b> ' . $row->modified_by . '<br />'.LF
        .'    <b>' . props_gettext("Description") . ':</b> ' . $row->revision_description . '<br />'.LF
        .'  </p>'.LF
        .'  <hr>'.LF;

    // Now loop through previous versions
    $result = sql_query("SELECT revision, modified_stamp, modified_by, revision_description FROM props_stories_previous_versions WHERE story_id = $story_id ORDER BY revision DESC");
    while ($row = sql_fetch_object($result)) {
        $output .=
             '  <p>'.LF
            .'    <b>' . props_gettext("Version") . ':</b> ' . $row->revision . '<br />'.LF
            .'    <b>' . props_gettext("Revision date") . ':</b> ' . $row->modified_stamp . '<br />'.LF
            .'    <b>' . props_gettext("Revision by") . ':</b> ' . $row->modified_by . '<br />'.LF
            .'    <b>' . props_gettext("Description") . ':</b> ' . $row->revision_description . '<br />'.LF
            .'  </p>'.LF
            .'  <hr>'.LF;
    }

    $output .=
         '<p class="center"><a class="button" href="javascript:this.window.close();">' . props_gettext("Close") . '</a></p>'.LF
        .'</body>'.LF
        .'</html>'.LF;

    echo $output;
    exit;
}

?>
