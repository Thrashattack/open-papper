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
 * @version     $Id: gen_url.php,v 1.8 2007/11/14 11:19:04 roufneck Exp $
 */

// loadLibs
props_loadLib('url');

/**
 * Returns a URL using supplied parameters
 *
 * @tag     {gen_url}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>op</b></li>
 *   <li><b>format</b> - see $MIME_TYPES in the config file for options</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_gen_url(&$params)
{
    // If a format was not specified, use the current one
    if (!isset($params['format'])) {
        $params['format'] = props_getkey('request.format');
    }

    $urlargs = array ('format' => $params['format']);

    if (isset($params['cmd'])) {
        $urlargs['cmd'] = $params['cmd'];
    }

    return genurl($urlargs);
}

?>
