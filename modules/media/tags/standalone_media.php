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
 * @version     $Id: standalone_media.php,v 1.6 2007/12/11 15:46:31 roufneck Exp $
 */

// loadLib
props_loadLib('media');

/**
 * Returns the media file specified by the ?media_id URL parameter
 *
 * @tag     {standalone_media}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>mediawidth</b> - </li>
 *   <li><b>mediaheight</b> - </li>
 *   <li><b>scaling</b> - </li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%E - embedded media including media player if needed</li>
 *       <li>%M - media thumbnail inculding &lt;img /&gt;</li>
 *       <li>%m - media thumbnail url</li>
 *       <li>%c - caption</li>
 *       <li>%C - subcaption</li>
 *       <li>%r - credit line</li>
 *       <li>%R - credit suffix</li>
 *       <li>%u - credit URL</li>
 *     </ul>
 * </ul>
 * @return  string  generated html code
 */
function tag_standalone_media(&$params)
{
    // Sanitize the provided media_id to guard against malicious URL hacking
    $media_id = props_getrequest('media_id', VALIDATE_INT);

    // If width and height were not specified, default to the original file
    if (!isset($params['mediawidth'])) $params['mediawidth'] = '';
    if (!isset($params['mediaheight'])) $params['mediaheight'] = '';
    if (!isset($params['format'])) $params['format'] = '%E';
    if (!isset($params['scaling'])) $params['scaling'] = 'constrain';

    // Get details from DB.
    $q  = "SELECT * FROM props_media "
        . "WHERE media_id = $media_id ";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        return FALSE;
    }

    $media = sql_fetch_assoc($result);
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

    return $media_output;
}

?>
