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
 * @version     $Id: include.php,v 1.6 2007/07/12 12:54:49 roufneck Exp $
 */

/**
 * Includes a .inc file from the includes dir into the current template.
 *
 * This tag is processed before all other tags.
 *
 * @tag     {include}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>snippet</b> - filename to be included, minus the .inc extension</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_include(&$params)
{
    // sanitize filename
    $params['snippet'] = ereg_replace('[^a-zA-Z0-9_-]', '', $params['snippet']);

    $filepath = props_getkey('config.dir.includes') . '/' . $params['snippet'] . '.inc';

    if (file_exists($filepath)) {
        return file_get_contents($filepath);
    } else {
        return '';
    }
}

?>
