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
 * @version     $Id: end_content.php,v 1.6 2007/07/12 12:51:53 roufneck Exp $
 */

/**
 * Returns end content of story
 *
 * @tag    {end_content}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_end_content(&$params)
{
    return props_getkey('story.end_content');
}

?>
