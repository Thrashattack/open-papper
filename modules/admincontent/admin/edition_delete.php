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
 * @version     $Id: edition_delete.php,v 1.7 2007/09/17 14:27:36 roufneck Exp $
 */

/**
 * @admintitle  Delete edition
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_edition_delete()
{
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    $pageID = props_getrequest('pageID');
    $output = '';

    // Check pageID
    if (empty($pageID) || ($pageID != $_SESSION['pageID'])) {
        props_error("Invalid page referer. Please submit this form again.");
    }

    // If this edition contains stories, it can't be deleted
    $q  = "SELECT Count(*) AS story_count "
        . "FROM props_stories "
        . "WHERE edition_id = $edition_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    if ($row->story_count) {
        props_error("This edition contains stories. Please first delete or reassign the stories.");
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

    $q  = "DELETE FROM props_editions WHERE edition_id = $edition_id";
    sql_query($q);

    // Redirect
    props_redirect(TRUE);
}

?>
