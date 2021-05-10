<?php
/**
 * Lib - url functions
 *
 * @package     api
 * @subpackage  url
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
 * @version     $Id: url.php,v 1.12 2007/12/11 15:46:28 roufneck Exp $
 */

// loadLibs
props_loadLib('editions');

/**
 * Generates an URL
 *
 * @param   array   $args  url components
 * @return  string  url
 */
function genurl($args, $htmlspecialchars = TRUE)
{
    // We need to preserve the current edition_id if it is not the
    // default (most recently published) one
    if ((!isset($args['edition_id'])) && (props_getkey('request.edition_id') != edition_current_id())) {
        $args['edition_id'] = props_getkey('request.edition_id');
    }

    // Add preview if needed
    if (props_getkey('request.preview') == TRUE) {
        $args['preview'] = TRUE;
    }

    // If a format was not specified, use the current one
    if (!isset($args['format'])) {
        $args['format'] = props_getkey('request.format');
    }

    if (props_getkey('config.url.static')) {
        // generate static url (version 2)
        $url = props_getkey('config.url.root') . 'content/2-0-/';

        while (list($name, $val) = each($args)) {
            $url .= "$name/$val/";
        }

        $url .= $args['module'] . '.' . $args['format'];

    } else {
        // Generate dynamic url
        $url = props_getkey('config.url.root') . '?' . http_build_query($args);
    }

/*

http://example.com/content/publication/(edition_id)/front_page/sub_sections/
http://example.com/content/publication/(edition_id)/front_page/sub_sections/article_id/article_title/op.format?template=tagtest.html


        // generate static url (version 3)
        $static_url = props_getkey('config.url.root') . 'content/';

        if (isset($args['edition_id'])) {
            $static_url .= $args['edition_id'].'ed/';
        }

        if (isset($args['section_id'])) {
            $section_id = $args['section_id'];
            $section = '';
            while ($section_id != FRONTPAGE_SECTION_ID) {
                $section = section_shortname($section_id).'/'.$section;
                $section_id = section_parent_id($section_id);
            }
            $static_url .= $section;
        }

        if (isset($args['story_id'])) {
            $static_url .= $args['story_id'].'stry/';
        }

        if (isset($args['headline'])) {
            $static_url .= str_replace(' ', '-', $args['headline']).'/';
        }

        while (list($name, $val) = each($args)) {
            if (!in_array($name, array('op', 'format', 'edition_id', 'section_id', 'story_id'))) {
                $static_url .= "$name/$val/";
            }
        }

        $static_url .= $args['op'] . '.' . $args['format'];

        echo "[".__LINE__."] $static_url".BR;
*/
    if ($htmlspecialchars) {
        return htmlspecialchars($url);
    } else {
        return $url;
    }
}

?>
