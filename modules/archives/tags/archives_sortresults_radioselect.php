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
 * @version     $Id: archives_sortresults_radioselect.php,v 1.8 2007/07/12 12:51:52 roufneck Exp $
 */

/**
 * Returns radio buttons allowing user to select whether search results should be ordered by relevance or date
 *
 * @tag    {archives_sortresults_radioselect}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_archives_sortresults_radioselect(&$params)
{
    // if 'date' sort order has been selected, output that field as being checked,
    // otherwise default to 'relevance'
    if (props_getkey('archives.sortorder_selected') == 'date') {
        $output =
             '<input name="archives_sortorder" type="radio" value="most_relevant" /> ' . props_gettext("Best matches first") . ' &nbsp;'.LF
            .'<input name="archives_sortorder" type="radio" value="most_recent" checked="checked" /> ' . props_gettext("Most recent first").LF;
    } else {
        $output =
             '<input name="archives_sortorder" type="radio" value="most_relevant" checked="checked" /> ' . props_gettext("Best matches first") . ' &nbsp;'.LF
            .'<input name="archives_sortorder" type="radio" value="most_recent" /> ' . props_gettext("Most recent first").LF;
    }

    return $output;
}

?>
