<?php
/**
 * Tag function
 *
 * @package     tags
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
 * @version     $Id: bookmarks.php,v 1.2 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * Returns all user bookmarks
 *
 * @tag    {bookmarks}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%n - bookmark name</li>
 *       <li>%u - bookmark url</li>
 *       <li>%i - bookmark id</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_bookmarks(&$params)
{
    if (!$_SESSION['PROPS_USER']['authenticated'] || $_SESSION['PROPS_USER']['user_id'] < 1) {
        return FALSE;
    }

    $output = '';

    // Set parameter defaults
    if (!isset($params['format'])) $params['format'] = '<a href="%u">%n</a><br />';  // default format

    $q = "SELECT * from props_users_bookmarks WHERE user_id = ".$_SESSION['PROPS_USER']['user_id']." ORDER BY bookmark_name";
    $result = sql_query($q);
    while ($row = sql_fetch_object($result)) {

        $bookmark = $params['format'];
        $bookmark = str_replace('%i', $row->bookmark_id, $bookmark);
        $bookmark = str_replace('%u', htmlspecialchars($row->bookmark_url), $bookmark);
        $bookmark = str_replace('%n', htmlspecialchars($row->bookmark_name), $bookmark);

        $output .= $bookmark;
    }

    return $output;
}

?>
