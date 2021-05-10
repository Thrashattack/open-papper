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
 * @version     $Id: section.php,v 1.9 2007/11/14 11:19:04 roufneck Exp $
 */

// loadLibs
props_loadLib('url,sections');

/**
 * Returns section information
 *
 * @tag     {section}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>section_shortname</b></li>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%u - url of section</li>
 *       <li>%N - full name of section</li>
 *       <li>%n - short (directory) name of section</li>
 *       <li>%s - static content</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_section(&$params)
{
    // Set default format
    if (!isset($params['format'])) $params['format'] = '&middot; <a href="%u">%N</a><br />';
    // Display current section if none was specified
    if (!isset($params['section_shortname'])) $params['section_shortname'] = section_shortname(props_getkey('request.section_id'));

    $section_id = section_id_of_shortname($params['section_shortname']);

    $sectionstring = $params['format'];
    $sectionstring = str_replace('%N', section_fullname($section_id), $sectionstring);
    $sectionstring = str_replace('%n', section_shortname($section_id), $sectionstring);
    $sectionstring = str_replace('%s', section_static_content($section_id), $sectionstring);
    $urlargs = array(
        'cmd' => 'displaysection',
        'section_id' => $section_id);
    $sectionstring = str_replace('%u', genurl($urlargs), $sectionstring);

    // Return output
    return $sectionstring;
}

?>
