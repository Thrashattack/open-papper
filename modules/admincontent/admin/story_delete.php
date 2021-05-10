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
 * @version     $Id: story_delete.php,v 1.9 2007/10/19 16:05:20 roufneck Exp $
 */

/**
 * @admintitle  Delete story
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_story_delete()
{
    $story_id = props_getrequest('story_id', VALIDATE_INT);
    $pageID = props_getrequest('pageID');
    $output = '';

    // Check pageID
    if (empty($pageID) || ($pageID != $_SESSION['pageID'])) {
        props_error("Invalid page referer. Please submit this form again.");
    }

    // Show errors
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        if (isset($GLOBALS['PROPS_ERRORSTACK']['story_id'])) {
            $output .= '<p class="error">'.$GLOBALS['PROPS_ERRORSTACK']['story_id']['message'].'</p>';
        }
        $output .= '<p style="text-align: center;"><a href="javascript:history.go(-1)">' . props_gettext("Go back") . '</a></p>';
        return $output;
        exit;
    }

    sql_query("DELETE FROM props_stories_previous_versions WHERE story_id = $story_id");
    sql_query("DELETE FROM props_threadcodes_stories_xref WHERE story_id = $story_id");
    sql_query("DELETE FROM props_media_story_xref WHERE story_id = $story_id");
    sql_query("DELETE FROM props_stories WHERE story_id = $story_id");

    header('Location: '.$_SESSION['REFERER']);
    exit;
}

?>
