<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  globaltags
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
 * @version     $Id: editionyears.php,v 1.5 2007/11/14 11:19:04 roufneck Exp $
 */

// loadLibs
props_loadLib('editions');

/**
 * Returns a list of distinct years of published editions
 *
 * @tag     {editionyears}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%y - year</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_editionyears(&$params)
{
    $output = '';

    // Set default format string if not supplied
    if (!isset($params['format'])) $params['format'] = '<a href="?cmd=editions&year=%y">%y</a> &nbsp; ';

    // Assemble SQL query to get the list of editions
    $q  = "SELECT DISTINCT(YEAR(publish_date)) AS year "
        . "FROM props_editions "
        . "WHERE publish_date IS NOT NULL "
        . "AND closed = 1 "
        . "ORDER BY year ASC ";
    $result = sql_query($q);

    while ($row = sql_fetch_array($result)) {
        $string = $params['format'];
        $string = str_replace('%y', $erow['year'], $string);

        $output .= $string.LF;
    }

    // Output the results
    return $output;
}

?>
