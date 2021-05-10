<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  stats
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
 * @version     $Id: most_popular_stories.php,v 1.2 2008/01/08 14:18:50 roufneck Exp $
 */

/**
 * Returns a list of the most popular stories in a given period.
 *
 * @tag    {most_popular_stories}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>startrow</b> - start listing stories at this row</li>
 *   <li><b>endrow</b> - stop listing stories at this row</li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%h - headline of story</li>
 *       <li>%s - subhead of story</li>
 *       <li>%u - URL of story</li>
 *       <li>%p - byline prefix of story</li>
 *       <li>%b - byline name of story</li>
 *       <li>%x - byline suffix of story</li>
 *       <li>%a - abstract of story</li>
 *       <li>%d - story published date</li>
 *       <li>%z - short (directory) name of section this story appears in</li>
 *       <li>%Z - Long (description) name of section this story appears in</li>
 *     </ul>
 *   </li>
 *   <li><b>days</b> - Number of days in the past. 0 means today. 1 means today and yesterday. etc...</li>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_most_popular_stories(&$params)
{
    $output = '';

    // Set default parameters
    if (!isset($params['format'])) $params['format'] = '&middot; <a href="%u">%h</a><br />';
    if (!isset($params['startrow'])) $params['startrow'] = 1;
    if (!isset($params['endrow'])) $params['endrow'] = 10;
    if (!isset($params['days'])) $params['days'] = 0;

    $log_stamp = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-$params['days'], date("Y")));

    // Assemble SQL query to get the list of stories
    $q  = "SELECT *, SUM(hits) as sum_hits FROM props_stats_log "
        . "LEFT JOIN props_stories ON props_stats_log.id = props_stories.story_id "
        . "WHERE props_stats_log.command = 'displaystory' "
        . "AND props_stats_log.log_stamp >= '" . $log_stamp . "' "
        . "GROUP BY props_stats_log.command, props_stats_log.id "
        . "ORDER BY sum_hits DESC "
        . "LIMIT " . ($params['startrow'] - 1) . ", " . $params['endrow'];

    // Run the query to retrieve the list of stories
    $result = sql_query($q);

    while ($row = sql_fetch_array($result)) {

        // start out with the format string and replace tokens with story parameters
        $storystring = $params['format'];
        $storystring = str_replace('%h', $row['headline'], $storystring);
        $storystring = str_replace('%s', $row['subhead'], $storystring);
        $urlargs = array ('cmd' => 'displaystory', 'story_id' => $row['story_id']);
        $storystring = str_replace('%u', genurl($urlargs), $storystring);
        $storystring = str_replace('%p', $row['byline_prefix'], $storystring);
        $storystring = str_replace('%b', $row['byline_name'], $storystring);
        $storystring = str_replace('%x', $row['byline_suffix'], $storystring);
        $storystring = str_replace('%a', $row['abstract'], $storystring);
        $storystring = str_replace('%z', section_shortname($row['section_id']), $storystring);
        $storystring = str_replace('%Z', section_fullname($row['section_id']), $storystring);
        $storystring = str_replace('%d', strftime($params['dateformat'], strtotime($row['published_stamp'])), $storystring);

        $output .= $storystring.LF;
    }

    // Free mem
    sql_free_result($result);

    return $output;
}

?>
