<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  displaystory
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
 * @version     $Id: related_storylist.php,v 1.21 2007/12/17 07:43:20 roufneck Exp $
 */

// loadLibs
props_loadLib('url,media,sections');

/**
 * Returns a list of related stories
 *
 * Returns a list of stories which have one or more of the same thread codes as
 * the current one, if any thread codes are set. Subject to various formatting
 * and selection parameters.
 *
 * @tag    {related_storylist}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>abstractmaxchars</b> - Used in combination with the %a format token
 *      below. This indicates that no more than 'x' characters of the abstract
 *      should be displayed. Output of the abstract will be cut off at a word
 *      boundary rather than mid-word. So, the number of characters output will
 *      vary but never exceed the limit specified here.
 *   </li>
 *   <li><b>startrow</b> - start listing stories at this row</li>
 *   <li><b>endrow</b> - stop listing stories at this row</li>
 *   <li><b>order</b> - comma-delimited list to order search:
 *     <ul>
 *       <li>section_shortname</li>
 *       <li>headline</li>
 *       <li>subhead</li>
 *       <li>byline</li>
 *       <li>story_id</li>
 *     </ul>
 *     Example:
 *     <code>
 *     {storylist order="section_shortname,headline"}
 *     </code>
 *   </li>
 *   <li><b>minweight (default = 0)</b> - stories below this weight will not be listed</li>
 *   <li><b>maxweight (defaults = 100)</b> - stories above this weight will not be listed<br />
 *     Example:
 *     <code>
 *     // list stories whose weight is 70, 80 or 90
 *     {related_storylist minweight="70" maxweight="90"} will only list
 *     </code>
 *   </li>
 *   <li><b>includearchives</b> - if set to yes, stories from the archives will also be listed</li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%h - headline of story</li>
 *       <li>%s - subhead of story</li>
 *       <li>%u - URL of story</li>
 *       <li>%p - byline prefix of story</li>
 *       <li>%b - byline name of story</li>
 *       <li>%x - byline suffix of story</li>
 *       <li>%a - abstract of story</li>
 *       <li>%h - first photo associated with story</li>
 *       <li>%B - body content of story</li>
 *       <li>%e - end content of story</li>
 *       <li>%d - story published date</li>
 *       <li>%z - short (directory) name of section this story appears in</li>
 *       <li>%Z - Long (description) name of section this story appears in</li>
 *       <li>%c - outputs appropriate accesslevel_xxx string, depending on
 *          whether story access is free, registration required, or paid archives
 *          (see accesslevel attributes below)</li>
 *     </ul>
 *   </li>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 *   <li><b>accesslevel_free_format</b> - string to be output in place of %c format token when story access is free</li>
 *   <li><b>accesslevel_reg_required_format</b> - string to be output in place of %c formattoken when story access requires user registration</li>
 *   <li><b>accesslevel_paidarchives_format</b> - string to be output in place of %c format token when story access requires paid archives subscription</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_related_storylist(&$params)
{
    $output = '';

    // Get a list of threadcodes associated with this story
    $q  = "SELECT threadcode_id "
        . "FROM props_threadcodes_stories_xref "
        . "WHERE story_id = " . props_getkey('request.story_id');
    $result = sql_query($q);

    // If this story has no thread codes, return now
    if (sql_num_rows($result) == 0) {
        return '';
    }

    // Assemble threadcodes into a comma-delimited list
    $threadcodes_array = array();
    while ($row = sql_fetch_object($result)) {
        $threadcodes_array[] = $row->threadcode_id;
    }

    $threadcodes_list = implode(',', $threadcodes_array);

    // Free mem
    sql_free_result($result);

    // Set default parameters
    if (!isset($params['format'])) $params['format'] = '&middot; <a href="%u">%h</a><br />';
    if (!isset($params['order'])) $params['order'] = 'modified_stamp DESC,weight DESC';
    if (!isset($params['minweight'])) $params['minweight'] = 0;
    if (!isset($params['maxweight'])) $params['maxweight'] = 100;
    if (!isset($params['photowidth'])) $params['photowidth'] = 120;
    if (!isset($params['photoheight'])) $params['photoheight'] = 120;
    if (!isset($params['photoalign'])) $params['photoalign'] = 'left';
    if (!isset($params['photohspace'])) $params['photohspace'] = 10;
    if (!isset($params['photovspace'])) $params['photovspace'] = 10;
    if (!isset($params['accesslevel_free'])) $params['accesslevel_free'] = props_gettext("Free");
    if (!isset($params['accesslevel_reg_required'])) $params['accesslevel_reg_required'] = props_gettext("Registration required");
    if (!isset($params['accesslevel_paid_archives'])) $params['accesslevel_paid_archives'] = props_gettext("Requires archives purchase");
    if (!isset($params['dateformat'])) $params['dateformat'] = '%c';
    if (!isset($params['includearchives'])) $params['includearchives'] = '';

    //  Assemble SQL query to get the list of stories
    $q  = "SELECT * FROM props_stories, props_threadcodes_stories_xref "
        . "WHERE props_threadcodes_stories_xref.story_id = props_stories.story_id "
        // Exclude current story
        . "AND props_stories.story_id != " . props_getkey('story.story_id') . " "
        . "AND threadcode_id IN ($threadcodes_list) "
        . "AND weight >= " . $params['minweight'] . " "
        . "AND weight <= " . $params['maxweight'] . " ";

    if ($params['includearchives'] != 'yes') {
        $q .= "AND edition_id = " . props_getkey('request.edition_id') . " ";
    }

    // Group stories
    $q .= " GROUP BY props_stories.story_id ";
    
    // If a sortorder was specified, add it to the query
    if (!empty($params['order'])) {
        $q .= " ORDER BY " . $params['order'];
    }
   
    // Add startrow / end row limiters
    // if startrow is not set, but endrow is
    if (!isset($params['startrow']) && isset($params['endrow'])) {
        $q .= ' LIMIT 0, ' . $params['endrow'];
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
        // If abstractmaxchars is set, truncate the abstract at a word boundary
        if (isset($params['abstractmaxchars'])) {
            for ($i = $params['abstractmaxchars']; $i > 0; $i--) {
                if ($row['abstract'][$i] == ' ') {
                    $row['abstract'] = substr($row['abstract'], 0, $i);
                    break;
                }
            }
        }

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
        $storystring = str_replace('%B', $row['body_content'], $storystring);
        $storystring = str_replace('%e', $row['end_content'], $storystring);
        $storystring = str_replace('%z', section_shortname($row['section_id']), $storystring);
        $storystring = str_replace('%Z', section_fullname($row['section_id']), $storystring);
        $storystring = str_replace('%d', strftime($params['dateformat'], strtotime($row['published_stamp'])), $storystring);
        if (strstr($storystring, '%c')) {

            switch($row['access_level']) {

                case ACCESS_FREE:
                    $storystring = str_replace('%c', $params['accesslevel_free'], $storystring);
                    break;

                case ACCESS_REG_REQUIRED:
                    $storystring = str_replace('%c', $params['accesslevel_reg_required'], $storystring);
                    break;

                case ACCESS_PAID_ARCHIVES:
                    $storystring = str_replace('%c', $params['accesslevel_paid_archives'], $storystring);
                    break;
            }
        }

        if (strstr($storystring, '%P')) {
            // Get media
            $media = array();
            $qm = "SELECT * FROM props_media_story_xref, props_media "
                . "WHERE story_id = " . $row['story_id'] . " "
                . "AND props_media_story_xref.media_id = props_media.media_id "
                . "ORDER BY position ASC LIMIT 1";
            $resultm = sql_query($qm);
            $media = sql_fetch_assoc($resultm);
            if ($media) {
                $storystring = str_replace('%P', '<img hspace="' . $params['photohspace'] . '"vspace="' . $params['photovspace'] . '" align="'. $params['photoalign'] . '" src="' . media_url($media, $params['photowidth'], $params['photoheight']) . '" alt="' . htmlspecialchars($media['caption']) . '" />', $storystring);
            } else {
                $storystring = str_replace('%P', '', $storystring);
            }
        }
        $output .= $storystring.LF;
    }

    // Free mem
    sql_free_result($result);

    return $output;
}

?>
