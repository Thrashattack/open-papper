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
 * @version     $Id: getkey.php,v 1.2 2007/12/11 15:46:30 roufneck Exp $
 */

/**
 * Returns the value of a registry key
 *
 * Example:
 * <code>
 * // Display story headline
 * {getkey var='story.headline' prepend='<h1>' append='</h1>'}
 * // Display story views
 * {getkey var='story.views' prepend='<p>Total views: ' append='</p>'}
 * </code>
 *
 * @tag     {getkey}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>var</b> - key variable</li>
 *   <li><b>escape (default = true)</b> - html-escape the value. Set to true or
 *      false. For security reasons (XSS attacks) leave it to true.
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_getkey(&$params)
{
    $val = props_getkey($params['var']);

    if (!$val) {
        return '';
    }

    if (isset($params['escape']) && $params['escape'] == 'false') {
        return $val;
    }

    return htmlspecialchars($val);
}

?>
