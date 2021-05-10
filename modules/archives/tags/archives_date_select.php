<?php
/**
 * Tag function
 *
 * @package     tags
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
 * @version     $Id: archives_date_select.php,v 1.11 2007/10/04 12:07:41 roufneck Exp $
 */

/**
 * Returns a <select> menu listing possible date range constraints
 *
 * @tag    {archives_date_select}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_archives_date_select(&$params)
{
    // Set defaults
    if (props_getkey('archives.daterange_selected')) {
        $archives_daterange_selected = props_getkey('archives.daterange_selected');
    } else {
        $archives_daterange_selected = 'R 30';
    }

    // Get year of first story in DB
    $q  = "SELECT YEAR(published_stamp) AS year "
        . "FROM props_stories "
        . "WHERE publication_status_id IN (" . PUBSTATUS_PUBLISHED . "," . PUBSTATUS_ARCHIVED . ") "
        . "AND YEAR(published_stamp) > 0 "
        . "ORDER BY published_stamp ASC LIMIT 1";
    $result = sql_query($q);
    $row = sql_fetch_object($result);
    $start_year = $row->year;
    $current_year = date("Y");

    $options = array(
        'R 0/'.props_gettext("Today"),
        'R 1/'.props_gettext("Today or Yesterday"),
        'R 7/'.sprintf(props_gettext("In the past %s days"), 7),
        'R 30/'.sprintf(props_gettext("In the past %s days"), 30),
        'R 90/'.sprintf(props_gettext("In the past %s days"), 90),
        'R 180/'.sprintf(props_gettext("In the past %s days"), 180),
        'R 365/'.sprintf(props_gettext("In the past %s days"), 365),
        '/----------');
    for ($year = $start_year; $year <= $current_year; $year++)
        $options[] = 'Y $year/'.props_gettext("In").' '.$year;
    $options[] = '/----------';
    $options[] = 'E 0/'.props_gettext("Ever");

    $output = '<select class="large" name="date_range">'.LF;
    foreach ($options as $option) {

        list($value, $name) = split("/", $option);

        if (($value == $archives_daterange_selected) && ($value != "")) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        $output .= '<option ' . $selected . ' value="' . $value . '">' . $name . '</option>'.LF;
    }
    $output .= '</select>'.LF;

    return $output;
}

?>
