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
 * @version     $Id: sectionlist.php,v 1.10 2008/01/08 20:02:59 roufneck Exp $
 */

// loadLibs
props_loadLib('url,sections');

/**
 * Returns section listing
 *
 * @tag     {sectionlist}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>parent_section</b></li>
 *   <li><b>depth</b></li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%u - url of section</li>
 *       <li>%N - full name of section</li>
 *       <li>%n - short (directory) name of section</li>
 *       <li>%D - depth of this section in the site hierarchy</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_sectionlist(&$params)
{
    $output = '';

    // Set parameter defaults
    if (!isset($params['depth'])) $params['depth'] = '1';
    if (!isset($params['parent_id'])) $params['parent_id'] = 1;
    if (!isset($params['format'])) $params['format'] = '&middot; <a href="%u">%N</a><br />';

    // Set defaults for internal tracking variables
    if (!isset($params['current_depth'])) $params['current_depth'] = '0';

    // Start at the given section
    if (!isset($params['current_section_id'])) $params['current_section_id'] = $params['parent_id'];

    // Print the current section name according to the given format (if it's not the top level section)
    if ($params['current_depth'] != 0) {
        $sectionstring = $params['format'];
        $sectionstring = str_replace('%N', section_fullname($params['current_section_id']), $sectionstring);
        $sectionstring = str_replace('%n', section_shortname($params['current_section_id']), $sectionstring);
        $sectionstring = str_replace('%D', $params['current_depth'], $sectionstring);
        $urlargs = array('cmd' => 'displaysection', 'section_id' => $params['current_section_id']);
        $sectionstring = str_replace('%u', genurl($urlargs), $sectionstring);
        $output .= $sectionstring.LF;
    }

    // Abort if we've reached the requested depth
    $params['current_depth']++;
    if ($params['current_depth'] > $params['depth']) {
        return $output;
    }

    // Now recurse thyself for all children
    $current_section_id = $params['current_section_id'];
    $num_children = section_num_childs($current_section_id);
    for ($i = 1; $i <= $num_children; $i++) {
        $params['current_section_id'] = section_child_id($current_section_id, $i);
        $output .= tag_sectionlist($params);
    }

    // If we're returning the final value
    if ($params['current_depth'] == 1) {
        return $output;
    } else {
        // We're at a recursion level and we're just returning to the calling function
        return $output;
    }
}

?>
