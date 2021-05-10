<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  archives
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
 * @version     $Id: search_log.php,v 1.5 2007/10/26 08:23:08 roufneck Exp $
 */

/**
 * @admintitle  Search log
 * @adminnav    1
 * @return  string  admin screen html content
 */
function admin_search_log()
{
    $output =
         // Top 20 search strings and keywords
         '<h1>' . props_gettext("Most popular searches in the past 90 days") . '</h1>'.LF
        .'<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Top 20 search strings") . '</th>'.LF
        .'    <th>' . props_gettext("Top 20 search keywords") . '</th>'.LF
        .'  </tr>'.LF
        .'  <tr>'.LF
        .'    <td>'.LF;

    // Get list of top 20 searches
    $q  = "SELECT search_string, count(search_string) AS score "
        . "FROM props_archives_searchlog_strings "
        . "GROUP BY search_string "
        . "ORDER BY score DESC "
        . "LIMIT 20";
    $result = sql_query($q);
    if (sql_num_rows($result)) {
        $output .= '      <ol style="margin-left: 2.00em;">'.LF;
        while ($row = sql_fetch_object($result)) {
            $output .= '<li>' . $row->search_string . ' (' . $row->score . ')</li>'.LF;
        }
        $output .= '      </ol>'.LF;
    } else {
        $output .= '<p>' . props_gettext("No results found.") . '</p>'.LF;
    }

    $output .=
         '    </td>'.LF
        .'    <td>'.LF;

    // Get list of top 20 search keywords
    $q  = "SELECT keyword, COUNT(keyword) AS score "
        . "FROM props_archives_searchlog_keywords "
        . "GROUP BY keyword "
        . "ORDER BY score DESC "
        . "LIMIT 20";
    $result = sql_query($q);
    if (sql_num_rows($result)) {
        $output .= '      <ol style="margin-left: 2.00em;">'.LF;
        while ($row = sql_fetch_object($result)) {
            $output .= '<li>' . $row->keyword . ' (' . $row->score . ')</li>'.LF;
        }
        $output .= '      </ol>'.LF;
    } else {
        $output .= '<p>' . props_gettext("No results found.") . '</p>'.LF;
    }

    $output .=
         '    </td>'.LF
        .'  </tr>'.LF
        .'</table>'.LF;

    return $output;
}

?>
