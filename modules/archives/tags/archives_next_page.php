<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  archives
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
 * @version     $Id: archives_next_page.php,v 1.10 2007/11/13 13:10:23 roufneck Exp $
 */

/**
 * Returns next page link
 *
 * @tag    {archives_next_page}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_archives_next_page(&$params)
{
    $output = '';

    // If there are more results to be displayed, output a link
    if (props_getkey('archives.next_page')) {

        $urlargs = array(
            'search_string' => props_getkey('archives.search_string'),
            'cmd' => 'archives-search',
            'position' => (props_getkey('archives.next_page')),
            'search_type' => props_getrequest('search_type'),
            'date_range' => props_getrequest('date_range'),
            'archives_sortorder' => props_getkey('archives.sortorder_selected')
            );

        $output = '<a href="' . genurl($urlargs) . '">' . props_gettext("Next") . '</a>';
    }

    return $output;
}

?>
