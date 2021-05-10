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
 * @version     $Id: story_date.php,v 1.7 2007/07/12 12:51:53 roufneck Exp $
 */

/**
 * Returns story date
 *
 * @tag    {story_date}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_story_date(&$params)
{
    if (!isset($params['dateformat'])) {
        $params['dateformat'] = '%x';
    }

    return strftime($params['dateformat'], strtotime(props_getkey('story.published_stamp')));
}

?>
