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
 * @version     $Id: archives_search_results.php,v 1.14 2007/11/13 13:10:23 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * Returns search results
 *
 * @tag    {archives_search_results}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>paid_item_indicator</b> - set the string which is output for the
 *      %P format token when an user has to pay for a story. Default is
 *      'Requires credits.'.
 *   </li>
 *   <li><b>registration_indicator</b> - set the string which is output for the
 *      %P format token when an user has to register/login for a story. Default
 *      is 'Requires registration.'. This will only be displayed when an user
 *      is not logged in.
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
 *       <li>%d - story date / time</li>
 *       <li>%z - short (directory) name of section this story appears in</li>
 *       <li>%Z - Long (description) name of section this story appears in</li>
 *       <li>%P - outputs an indication if this article is a paid archives item</li>
 *     </ul>
 *   </li>
 *   <li><b>dateformat</b> - see PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_archives_search_results(&$params)
{
    $output = '';

    //  Set default parameters
    if (!isset($params['format']))
        $params['format'] = '<p>&middot; <b><a href="%u">%h</a></b> (%n/%j/%y) %P<br /><blockquote>%a</blockquote><br /></p>';
    if (!isset($params['altoutput'])) $params['altoutput'] = props_gettext('No results found.');
    if (!isset($params['paid_item_indicator'])) $params['paid_item_indicator'] = props_gettext('Requires credits.');
    if (!isset($params['paid_item_indicator'])) $params['registration_indicator'] = props_gettext('Requires registration.');
    if (!isset($params['dateformat'])) $params['dateformat'] = '%c';

    // Output the search results
    while ($row = sql_fetch_object(props_getkey('archives.query_handle'))) {

        // Start out with the format string
        $match_string = $params['format'];
        $match_string = str_replace('%h', $row->headline, $match_string);
        $match_string = str_replace('%s', $row->subhead, $match_string);
        $urlargs = array ('cmd' => 'displaystory', 'story_id' => $row->story_id);
        $match_string = str_replace('%u', genurl($urlargs), $match_string);
        $match_string = str_replace('%p', $row->byline_prefix, $match_string);
        $match_string = str_replace('%b', $row->byline_name, $match_string);
        $match_string = str_replace('%x', $row->byline_suffix, $match_string);
        $match_string = str_replace('%a', $row->abstract, $match_string);
        $match_string = str_replace('%z', section_shortname($row->section_id), $match_string);
        $match_string = str_replace('%Z', section_fullname($row->section_id), $match_string);
        $match_string = str_replace('%d', strftime($params['dateformat'], strtotime($row->published_stamp)), $match_string);

        if (props_getkey('config.archives.paid') && ($row->access_level == ACCESS_PAID_ARCHIVES)) {
            $match_string = str_replace('%P', $params['paid_item_indicator'], $match_string);
        } elseif (($row->access_level == ACCESS_REG_REQUIRED) && !user_is_logged_in()) {
            $match_string = str_replace('%P', $params['registration_indicator'], $match_string);
        } else {
            $match_string = str_replace('%P', '', $match_string);
        }

        $output .= $match_string.LF;
    }

    return $output;
}

?>
