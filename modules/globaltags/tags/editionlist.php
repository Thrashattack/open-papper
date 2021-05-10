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
 * @version     $Id: editionlist.php,v 1.15 2007/12/11 15:46:30 roufneck Exp $
 */

// loadLibs
props_loadLib('url,editions,sections');

/**
 * Returns a list of editions
 *
 * If "year" request / form field is set, it will only list editions from that
 * year.
 *
 * @tag     {editionlist}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>maxshown</b> - maximum number of editions to show on the list</li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%i = edition ID #</li>
 *       <li>%l = edition label</li>
 *       <li>%d = edition date</li>
 *       <li>%u = URL pointing to edition front page</li>
 *     </ul>
 *   </li>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_editionlist(&$params)
{
    $output = '';

    // Set defaults
    if (!isset($params['format'])) $params['format'] = '<a href="%u">%l (%d)</a><br />';
    if (!isset($params['dateformat'])) $params['dateformat'] = '%x';

    // Assemble SQL query to get the list of editions
    $q = "SELECT * FROM props_editions WHERE publish_date IS NOT NULL ";

    // If 'year' formfield is set, list only editions from that year
    if (props_getrequest('year')) {
        $q .= "  AND YEAR(publish_date) = " . props_getrequest('year') . " ";
    }

    $q .= "ORDER BY publish_date ASC ";

    if (isset($params['maxshown'])) {
        $q .= "LIMIT " . $params["maxshown"];
    }

    // Run the query to retrieve the list of editions
    $result = sql_query($q);
    while ($row = sql_fetch_array($result)) {

        $urlargs = array ('cmd' => 'displaysection',
                'edition_id' => $row['edition_id'],
                'format' => props_getkey('request.format'));

        $thisline = $params['format'];
        $thisline = str_replace('%i', $row['edition_id'], $thisline);
        $thisline = str_replace('%l', htmlspecialchars($row['label']), $thisline);
        $thisline = str_replace('%d', strftime($params['dateformat'], edition_date($row['edition_id'])), $thisline);
        $thisline = str_replace('%u', genurl($urlargs), $thisline);

        $output .= $thisline.LF;
    }

    // Output the results
    return $output;
}

?>
