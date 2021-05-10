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
 * @version     $Id: story_url.php,v 1.9 2007/11/14 11:19:04 roufneck Exp $
 */

// loadLibs
props_loadLib('url');

/**
 * Returns a link to the current story
 *
 * @tag    {story_url}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>op</b></li>
 *   <li><b>output_format</b> - MIME output type mapping (e.g. html, print, xml, etc.).</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_story_url(&$params)
{
    // Default module is displaystory
    if (!isset($params['cmd'])) {
        $params['cmd'] = 'displaystory';
    }

    // Default module is displaystory
    if (!isset($params['output_format'])) {
        $params['output_format'] = props_getkey('request.format');
    }

    $urlargs = array (
        'cmd' => $params['cmd'],
        'story_id' => props_getkey('story.story_id'),
        'format' => $params['output_format']);

    $output = genurl($urlargs);

    return $output;
}

?>
