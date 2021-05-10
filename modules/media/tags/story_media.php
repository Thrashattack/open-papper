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
 * @version     $Id: story_media.php,v 1.9 2007/12/11 15:46:31 roufneck Exp $
 */

// loadLib
props_loadLib('media');

/**
 * Returns the media assigned to the current story_id
 *
 * @tag     {story_media}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>startrow</b> - start listing media at this row</li>
 *   <li><b>endrow</b> - stop listing media at this row</li>
 *   <li><b>mediawidth</b> - </li>
 *   <li><b>mediaheight</b> - </li>
 *   <li><b>scaling</b> - </li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%U - original media url</li>
 *       <li>%m - media thumbnail url</li>
 *       <li>%M - media thumbnail inculding &lt;img /&gt;</li>
 *       <li>%E - embedded media including media player if needed</li>
 *       <li>%c - caption</li>
 *       <li>%C - subcaption</li>
 *       <li>%r - credit line</li>
 *       <li>%R - credit suffix</li>
 *       <li>%u - credit URL</li>
 *       <li>%I - media ID</li>
 *     </ul>
 * </ul>
 * @return  string  generated html code
 */
function tag_story_media($params)
{
    static $output;
    $story_id = props_getkey('request.story_id');

    // If width and height were not specified, default to the original file
    if (!isset($params['mediawidth'])) $params['mediawidth'] = '';
    if (!isset($params['mediaheight'])) $params['mediaheight'] = '';
    if (!isset($params['format'])) $params['format'] = '%E';
    if (!isset($params['scaling'])) $params['scaling'] = 'constrain';

    // Get list of photos associated with the current story
    $q  = "SELECT * FROM props_media_story_xref, props_media "
        . "WHERE story_id = $story_id "
        . "AND props_media_story_xref.media_id = props_media.media_id "
        . "ORDER BY position";

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

    $result = sql_query($q);

    while ($media = sql_fetch_assoc($result)) {
        media_get_details($media, $params['mediawidth'], $params['mediaheight'], $params['scaling']);

        // Get correct image width/height
        $width = (empty($params['mediawidth'])) ? $media['width'] : $params['mediawidth'];
        $height = (empty($params['mediaheight'])) ? $media['height'] : $params['mediaheight'];

        if ($params['scaling'] != 'absolute') {
            // Default: 'constrain'
            $scale = min($width/$media['width'], $height/$media['height']);
            $width = (int)($media['width']*$scale);
            $height = (int)($media['height']*$scale);
        }

        // Start with the format string and replace tokens
        $media_output = $params['format'];
        $media_output = str_replace('%I', $media['media_id'], $media_output);
        $media_output = str_replace('%E', $media['embedded'], $media_output);
        $media_output = str_replace('%M', $media['thumb_html'], $media_output);
        $media_output = str_replace('%m', $media['thumb_url'], $media_output);
        $media_output = str_replace('%U', $media['source_url'], $media_output);
        $media_output = str_replace('%c', htmlspecialchars($media['caption']), $media_output);
        $media_output = str_replace('%C', htmlspecialchars($media['subcaption']), $media_output);
        $media_output = str_replace('%r', htmlspecialchars($media['credit_line']), $media_output);
        $media_output = str_replace('%R', htmlspecialchars($media['credit_suffix']), $media_output);
        $media_output = str_replace('%u', htmlspecialchars($media['credit_url']), $media_output);

        $media_output = str_replace('%w', $width, $media_output);
        $media_output = str_replace('%h', $height, $media_output);

        $output .= $media_output.LF;
    }

    return $output;
}

?>
