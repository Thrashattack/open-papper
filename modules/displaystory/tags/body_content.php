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
 * @version     $Id: body_content.php,v 1.9 2007/07/12 12:51:53 roufneck Exp $
 */

/**
 * Returns body content of story
 *
 * NOTICE: Please keep in mind that the generated html code has html
 * paragraphs. This means that the output is prepended / appended with
 * &lt;p&gt;&lt;/p&gt;. And newlines are replaced with &lt;/p&gt;&lt;p&gt;.
 *
 * @tag    {body_content}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>inline25</b> - inline text to be output (between paragraphs) roughly 25% of the way through the story.</li>
 *   <li><b>inline50</b> - inline text to be output (between paragraphs) roughly 50% of the way through the story.</li>
 *   <li><b>inline75</b> - inline text to be output (between paragraphs) roughly 75% of the way through the story.</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_body_content(&$params)
{
    $body_content = props_getkey('story.body_content');

    if (!isset($params['inline25'])) $params['inline25'] = '';
    if (!isset($params['inline50'])) $params['inline50'] = '';
    if (!isset($params['inline75'])) $params['inline75'] = '';

    // If we don't need to insert any inline content,
    // return now so as to save CPU cycles
    if (($params['inline25'] == '') && ($params['inline50'] == '') && ($params['inline75'] == ''))
        return $body_content;

    // Split content on paragraph boundaries
    $body_content_array = split('</p><p>', $body_content);
    $paragraph_count = sizeof($body_content_array);
    if ($params['inline25'] != '')  $body_content_array[intval($paragraph_count * .25 )] .= $params['inline25'];
    if ($params['inline50'] != '')  $body_content_array[intval($paragraph_count * .50 )] .= $params['inline50'];
    if ($params['inline75'] != '')  $body_content_array[intval($paragraph_count * .75 )] .= $params['inline75'];

    // Now put all the pieces back together again
    $body_content = implode('</p><p>', $body_content_array);

    return $body_content;
}

?>
