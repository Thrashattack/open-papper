<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  content
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
 * @version     $Id: assign_to_section.php,v 1.1 2007/10/19 16:18:06 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * Returns section listing
 *
 * @tag     {assign_to_section}
 * @param   array  &$params  parameters
 * @return  string  generated html code
 *
 * @userprivs  assign_to_section  Assign to section
 */
function tag_assign_to_section(&$params)
{
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    return section_select($section_id, 'section_id');
}

?>
