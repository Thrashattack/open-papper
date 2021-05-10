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
 * @version     $Id: storylist.php,v 1.31 2007/12/11 15:46:30 roufneck Exp $
 */

// Make sure libs are loaded
props_loadLib('url,media,sections');

/**
 * Returns a list of stories
 *
 * @tag     {storylist}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>abstractmaxchars</b> - Used in combination with the %a format token
 *      below. This indicates that no more than 'x' characters of the abstract
 *      should be displayed. Output of the abstract will be cut off at a word
 *      boundary rather than mid-word. So, the number of characters output will
 *      vary but never exceed the limit specified here.
 *   </li>
 *   <li><b>startrow</b> - start listing stories at this row</li>
 *   <li><b>endrow</b> - stop listing stories at this row</li>
 *   <li><b>section (default = all sections)</b> - '%C' for current section or
 *      comma-delimited list of sections from which to pull stories<br />
 *      Example:
 *      <code>
 *      // list stories in sections local and tri-state
 *      {storylist section="local,tri-state"}
 *      // list stories in current sections
 *      {storylist section="%C"}
 *      </code>
 *   </li>
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
 *       <li>%U - URL of story (forces HTML format)</li>
 *       <li>%p - byline prefix of story</li>
 *       <li>%b - byline name of story</li>
 *       <li>%x - byline suffix of story</li>
 *       <li>%a - abstract of story</li>
 *       <li>%M - first media thumbnail html associated with story inculding &lt;img /&gt;</li>
 *       <li>%m - first media thumbnail url media url associated with story</li>
 *       <li>%B - body content of story</li>
 *       <li>%e - end content of story</li>
 *       <li>%d - story published date</li>
 *       <li>%z - short (directory) name of section this story appears in</li>
 *       <li>%Z - Long (description) name of section this story appears in</li>
 *       <li>%I - outputs audiostring, if audio is associated with this story</li>
 *       <li>%i - outputs videostring, if video is associated with this story</li>
 *       <li>%c - outputs appropriate accesslevel_xxx string, depending on
 *          whether story access is free, registration required, or paid archives
 *          (see accesslevel attributes below)</li>
 *     </ul>
 *   </li>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 *   <li><b>accesslevel_free_format</b> - string to be output in place of %c format token when story access is free</li>
 *   <li><b>accesslevel_reg_required_format</b> - string to be output in place of %c formattoken when story access requires user registration</li>
 *   <li><b>accesslevel_paidarchives_format</b> - string to be output in place of %c format token when story access requires paid archives subscription</li>
 *   <li><b>audiostring</b> - string to be output in place of %I format token</li>
 *   <li><b>videostring</b> - string to be output in place of %I format token</li>
 *   <li><b>mediawidth</b> - Width of story media thumbnail</li>
 *   <li><b>mediaheight</b> - Height of story media thumbnail</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_storylist(&$params)
{
    $output = '';

    // Set default parameters
    if (!isset($params['format'])) $params['format'] = '&middot; <a href="%u">%h</a><br />';
    if (!isset($params['minweight'])) $params['minweight'] = 0;
    if (!isset($params['maxweight'])) $params['maxweight'] = 100;
    if (!isset($params['mediawidth'])) $params['mediawidth'] = 110;
    if (!isset($params['mediaheight'])) $params['mediaheight'] = 110;
    if (!isset($params['accesslevel_free'])) $params['accesslevel_free'] = props_gettext("Free");
    if (!isset($params['accesslevel_reg_required'])) $params['accesslevel_reg_required'] = props_gettext("Registration required");
    if (!isset($params['accesslevel_paid_archives'])) $params['accesslevel_paid_archives'] = props_gettext("Requires archives purchase");
    if (!isset($params['dateformat'])) $params['dateformat'] = '%c';

    // Assemble SQL query to get the list of stories
    $q  = "SELECT * FROM props_stories "
        . "WHERE weight >= " . $params['minweight'] . " "
        . "AND approved = 1 "
        . "AND weight <= " . $params['maxweight'] . " ";

    if (props_getkey('request.preview') != TRUE) {
        $q .= "AND props_stories.publication_status_id IN (" . PUBSTATUS_PUBLISHED . "," . PUBSTATUS_ARCHIVED . ") ";
    }

    if (!isset($params['includearchives']) || $params['includearchives'] != 'yes') {
        $q .= "AND edition_id = " . props_getkey('request.edition_id') . " ";
    }

    if (isset($params['rssfeed'])) {
        $q .= "AND rss_feed = 1 ";
    }

    // If threadcode was set, restrict the query to a list of stories with
    // that threadcode
    if (isset($params['threadcode'])) {

        props_loadLib('stories');
        $threadcode_id = get_threadcode_id($params['threadcode']);

        // If threadcode is invalid, search on an invalid story_id
        // so we don't return any results
        if ($threadcode_id == FALSE) {
            $q .= "AND props_stories.story_id IN (-999) ";
        } else {
            $result = sql_query("SELECT story_id FROM props_threadcodes_stories_xref WHERE threadcode_id = $threadcode_id");
            $story_id_array = array();
            while ($row = sql_fetch_object($result)) {
                $story_id_array[] = $row->story_id;
            }

            if (sizeof($story_id_array) == 0) {
                $story_id_list = "-998";
            } else {
                $story_id_list = implode(",", $story_id_array);
            }

            $q .= "AND props_stories.story_id IN ($story_id_list) ";
        }
    }

    // Only add the stories from the current section
    if ((isset($params['section'])) && $params['section'] == '%C') {
        $q .= " AND ( ";
        $q .= "section_id = '" . props_getkey('request.section_id') . "') ";
    } elseif (isset($params["section"])) {
        // Restrict to certain sections if so directed
        // Iterate over list of sections and add them to the query
        $q .= " AND ( ";
        $count = 0;
        foreach(split(',', $params['section']) AS $section) {
            if ($count++) {
                $q .= " OR ";
            }
            $q .= "section_id = '" . section_id_of_shortname($section) . "'";
        }
        $q .= ") ";
    }

    // If a sortorder was specified, add it to the query
    if (isset($params['order'])) {
        $q .= ' ORDER BY ' . $params["order"];
    }

    // Add startrow / end row limiters if startrow is not set, but endrow is
    if (!isset($params['startrow']) && isset($params['endrow'])) {
        $q .= " LIMIT 0, " . $params['endrow'];
    }

    // If startrow is set, but endrow isn't
    if ((isset($params['startrow'])) && (!isset($params['endrow']))) {
        $q .= " LIMIT " . ($params['startrow'] - 1). ", 99999";
    }

    // We don't want every revision of the same story
    // $q .= " GROUP BY stories.story_id";

    //  if both startrow and endrow are set
    if (isset($params['startrow']) && isset($params['endrow'])) {
        $q .= " LIMIT " . ($params['startrow'] - 1). ", " . ($params['endrow'] - $params['startrow'] + 1);
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

        // Start out with the format string and replace tokens with story parameters
        $storystring = $params['format'];
        $storystring = str_replace('%h', htmlspecialchars($row['headline']), $storystring);
        $storystring = str_replace('%s', htmlspecialchars($row['subhead']), $storystring);

        $urlargs = array('cmd' => 'displaystory', 'story_id' => $row['story_id']);
        $urlargs_html = array('cmd' => 'displaystory', 'story_id' => $row['story_id'], 'format' => 'html');
        $storystring = str_replace('%u', genurl($urlargs), $storystring);
        $storystring = str_replace('%U', genurl($urlargs_html), $storystring);

        $storystring = str_replace('%p', htmlspecialchars($row['byline_prefix']), $storystring);
        $storystring = str_replace('%b', htmlspecialchars($row['byline_name']), $storystring);
        $storystring = str_replace('%x', htmlspecialchars($row['byline_suffix']), $storystring);
        $storystring = str_replace('%a', htmlspecialchars($row['abstract']), $storystring);
        $storystring = str_replace('%B', $row['body_content'], $storystring);
        $storystring = str_replace('%e', htmlspecialchars($row['end_content']), $storystring);
        $storystring = str_replace('%z', htmlspecialchars(section_shortname($row['section_id'])), $storystring);
        $storystring = str_replace('%Z', htmlspecialchars(section_fullname($row['section_id'])), $storystring);
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

        if (stristr($storystring, '%i')) {
            if (story_has_video($row['story_id'])) {
                $storystring = str_replace("%i", $params['videostring'], $storystring);
            } else {
                $storystring = str_replace("%i", '', $storystring);
            }
        }
        
        if (stristr($storystring, '%I')) {
            if (story_has_audio($row['story_id'])) {
                $storystring = str_replace("%I", $params['audiostring'], $storystring);
            } else {
                $storystring = str_replace("%I", '', $storystring);
            }
        }

        if (stristr($storystring, '%M')) {
            // Get media
            $media = array();
            $qm = "SELECT * FROM props_media_story_xref, props_media "
                . "WHERE story_id = " . $row['story_id'] . " "
                . "AND props_media_story_xref.media_id = props_media.media_id "
                . "ORDER BY position ASC LIMIT 1";
            $resultm = sql_query($qm);
            $media = sql_fetch_assoc($resultm);
            if ($media) {
                media_get_details($media, $params['mediawidth'], $params['mediaheight']);
                $storystring = str_replace('%M', $media['thumb_html'], $storystring);
                $storystring = str_replace('%m', $media['thumb_url'], $storystring);
            } else {
                $storystring = str_replace('%M', '', $storystring);
                $storystring = str_replace('%m', '', $storystring);
            }
        }
        $output .= $storystring.LF;
    }

    return $output;
}

?>
