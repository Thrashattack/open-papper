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
 * @version     $Id: assign_to_edition.php,v 1.2 2007/12/11 15:46:30 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * Returns section listing
 *
 * @tag     {assign_to_edition}
 * @param   array  &$params  parameters
 * @return  string  generated html code
 *
 * @userprivs  assign_to_edition  Assign to edition
 */
function tag_assign_to_edition(&$params)
{
    $section_id = props_getrequest('edition_id', VALIDATE_INT);

    $current_edition_id = edition_current_id(FALSE);
    $q  = "SELECT * FROM props_editions WHERE closed = 0 ORDER BY edition_id ASC";
    $result = sql_query($q);
    if (!sql_num_rows($result)) {
        $editions = props_gettext("No editions are currently open.");
    } else {
        $editions = '  <select class="large" name="edition_id">'.LF
                   .'    <option value="">' . props_gettext("Not assigned") . '</option>'.LF;
        while ($row = sql_fetch_object($result)) {
            $strSelected = ($row->edition_id == $edition_id) ? 'selected="selected"' : '';
            $strLabel = ($row->label) ? '&nbsp;-&nbsp;'.htmlspecialchars($row->label) : '';
            $strLive = ($row->edition_id == $current_edition_id) ? '&nbsp;&nbsp;('.props_gettext("CURRENT LIVE SITE").')' : '';
            $editions .= '    <option ' . $strSelected . ' value="' . $row->edition_id . '">' . props_gettext("Edition") . ' #' . $row->edition_id . $strLabel . $strLive . '</option>'.LF;
        }
        $editions .= '  </select>'.LF;
    }

    return $editions;
}

?>
