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
 * @version     $Id: list_bulletins.php,v 1.10 2007/11/21 07:43:35 roufneck Exp $
 */

// loadLibs
props_loadLib('users');

/**
 * Returns all available user bulletins
 *
 * @tag    {list_bulletins}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>subscribed_string</b> - this string is output in place of the %U format token if the current user is subscribed to the bulletin</li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%n - short name of bulletin</li>
 *       <li>%N - full name of bulletin</li>
 *       <li>%i - unique id # of bulletin</li>
 *       <li>%U - output 'subscribed_string' if current user is subscribed to this bulletin</li>
 *       <li>%c - output 'checked="checked"' if current user is subscribed to this bulletin</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_list_bulletins(&$params)
{
    $output = '';

    // Set parameter defaults
    if (!isset($params['format'])) $params['format'] = '%N<br />';  // default format
    if (!isset($params['subscribed_string'])) $params['subscribed_string'] = props_gettext('Yes');  // default format

    $q = "SELECT * FROM props_bulletins";
    $result = sql_query($q);

    while($row = sql_fetch_object($result)) {

        $bulletin_string = $params['format'];
        $bulletin_string = str_replace('%N', $row->bulletin_name, $bulletin_string);
        $bulletin_string = str_replace('%n', $row->bulletin_shortname, $bulletin_string);
        $bulletin_string = str_replace('%i', $row->bulletin_id, $bulletin_string);

        if ($_SESSION['PROPS_USER']['authenticated']) {

            // Find out whether current user is subscribed to this bulletin
            $q  = "SELECT * FROM props_bulletins_subscriptions "
                . "WHERE user_id = " . $_SESSION['PROPS_USER']['user_id'] . " "
                . "AND bulletin_id = $row->bulletin_id";
            $subscribed_query = sql_query($q);

            if (sql_num_rows($subscribed_query)) {
                $bulletin_string = str_replace('%U', $params['subscribed_string'], $bulletin_string);
                $bulletin_string = str_replace('%c', 'checked="checked"', $bulletin_string);
            } else {
                $bulletin_string = str_replace('%U', '', $bulletin_string);
            }
        }

        $output .= $bulletin_string;
    }

    return $output;
}

?>
