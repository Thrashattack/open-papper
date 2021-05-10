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
 * @version     $Id: section_fullname.php,v 1.6 2007/12/11 15:46:30 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * Returns full name of current section
 *
 * e.g. 'Local Sports'
 *
 * @tag     {section_fullname}
 * @param   array  &$params  parameters
 * @return  string  generated html code
 */
function tag_section_fullname(&$params)
{
    return htmlspecialchars(section_fullname(props_getkey('request.section_id')));
}

?>
