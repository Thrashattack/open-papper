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
 * @version     $Id: archives_stories_purchased_list.php,v 1.10 2007/11/24 04:35:51 phutureboy Exp $
 */

// loadLibs
props_loadLib('url,archives,users,stories');

/**
 * Returns a list of stories the current user has purchased.
 *
 * @tag    {archives_stories_purchased_list}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>startrow</b> - start listing stories at this row</li>
 *   <li><b>endrow</b> - stop listing stories at this row</li>
 *   <li><b>order</b> - order by field:
 *     <ul>
 *       <li>headline</li>
 *       <li>subhead</li>
 *       <li>byline</li>
 *       <li>story_id</li>
 *       <li>section_shortname</li>
 *     </ul>
 *     Stories can be sorted by multiple criteria. Just place them in a
 *     comma-delmited list from most significant to least.
 *
 *     Example:
 *     <code>
 *     {storylist order="section_shortname,headline"}
 *     </code>
 *   </li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%h - headline of story</li>
 *       <li>%s - subhead of story</li>
 *       <li>%u - URL of story</li>
 *       <li>%p - byline prefix of story</li>
 *       <li>%b - byline name of story</li>
 *       <li>%x - byline suffix of story</li>
 *       <li>%a - abstract of story</li>
 *       <li>%B - body content of story</li>
 *       <li>%e - end content of story</li>
 *       <li>%d - date / time</li>
 *       <li>%z - short (directory) name of section this story appears in</li>
 *       <li>%Z - Long (description) name of section this story appears in</li>
 *     </ul>
 *   </li>
 *   <li><b>dateformat</b> - see PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_archives_stories_purchased_list(&$params)
{
    if (!isset($_SESSION['PROPS_USER']['authenticated'])) {
        return;
    }

    $output = '';

    // Set default parameters
    if (!isset($params['format'])) $params['format'] = '&middot; <a href="%u">%h</a><br />';
    if (!isset($params['order'])) $params['order'] = 'expire';
    if (!isset($params['dateformat'])) $params['dateformat'] = '%c';

    // Assemble SQL query to get the list of stories
    $q  = "SELECT * FROM props_users_archive_stories_purchased, props_stories "
        . "WHERE props_users_archive_stories_purchased.story_id = props_stories.story_id "
        . "AND user_id = " . $_SESSION['PROPS_USER']['user_id'] . " ";

    // If a sortorder was specified, add it to the query
    if (isset($params['order'])) {
        $q .= " ORDER BY " . $params['order'];
    }

    // Add startrow / end row limiters
    // if startrow is not set, but endrow is
    if (!isset($params['startrow']) && isset($params['endrow'])) {
        $q .= " LIMIT 0, " . $params['endrow'];
    }
    // If startrow is set, but endrow isn't
    if ((isset($params['startrow'])) && (!isset($params['endrow']))) {
        $q .= " LIMIT " . ($params['startrow'] - 1). ", 99999";
    }
    // If both startrow and endrow are set
    if (isset($params['startrow']) && isset($params['endrow'])) {
        $q .= " LIMIT " . ($params['startrow'] - 1). "," . ($params['endrow'] - $params['startrow'] + 1);
    }

    // Run the query to retrieve the list of stories
    $result = sql_query($q);
    while ($row = sql_fetch_array($result)) {

        // Start out with the format string and replace tokens with story parameters
        $storystring = $params['format'];
        $storystring = str_replace('%h', $row['headline'], $storystring);
        $storystring = str_replace('%s', $row['subhead'], $storystring);
        $urlargs = array ('module' => 'displaystory',
            'story_id' => $row['story_id']);
        $storystring = str_replace('%u', genurl($urlargs), $storystring);
        $storystring = str_replace('%p', $row['byline_prefix'], $storystring);
        $storystring = str_replace('%b', $row['byline_name'], $storystring);
        $storystring = str_replace('%x', $row['byline_suffix'], $storystring);
        $storystring = str_replace('%a', $row['abstract'], $storystring);
        $storystring = str_replace('%B', $row['body_content'], $storystring);
        $storystring = str_replace('%e', $row['end_content'], $storystring);
        $storystring = str_replace('%z', section_shortname($row['section_id']), $storystring);
        $storystring = str_replace('%Z', section_fullname($row['section_id']), $storystring);
        $storystring = str_replace('%d', strftime($params['dateformat'], strtotime($row['published_stamp'])), $storystring);
        $output .= $storystring.LF;
    }

    return $output;
}

?>
