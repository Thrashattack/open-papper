<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  polls
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
 * @version     $Id: poll_comments.php,v 1.14 2007/12/11 15:46:31 roufneck Exp $
 */

/**
 * Returns comments for the requested poll_id
 *
 * WARNING: Changed commentformat to format
 *
 * @tag    {poll_comments}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%u - user_id of poster</li>
 *       <li>%U - username of poster</li>
 *       <li>%d - date / time of comment</li>
 *       <li>%c - bodytext of comment</li>
 *     </ul>
 *   </li>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 *   <li><b>deletedtext</b> - Displays text when a comment is deleted.</li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_poll_comments(&$params)
{
    if (!isset($params['prepend'])) $params['prepend'] = '<table>';
    if (!isset($params['append'])) $params['append'] = '</table>';
    if (!isset($params['format'])) $params['format'] = '<tr><th>' . props_gettext('From') . ': <b>%U</b><br /><br />%d<br /></th><td valign="top">%c</td></tr>';
    if (!isset($params['dateformat'])) $params['dateformat'] = '%c';
    if (!isset($params['deletedtext'])) $params['deletedtext'] = '(' . props_gettext("Deleted by Administrator") . ')';

    $output = '';

    // Get a list of comments for the specified poll
    $q  = "SELECT t1.*, t2.username FROM props_polls_comments AS t1 "
        . "LEFT JOIN props_users AS t2 "
        . "ON t1.user_id = t2.user_id "
        . "WHERE poll_id = " . props_getkey('request.poll_id') . " "
        . "AND (t1.user_id = t2.user_id OR t1.user_id = 0) "
        . "ORDER BY comment_id ASC";
    $result = sql_query($q);

    // If query returns nothing, stop here and return altoutput
    if (sql_num_rows($result) == 0) {
        return '';
    }

    // Loop through comments and output them
    while ($row = sql_fetch_object($result)) {
        $username = ($row->user_id == ANONYMOUS_USER_ID) ? props_gettext("Guest") : $row->username;
        $comment = $params['format'];
        $comment = str_replace('%u', $row->user_id, $comment);
        $comment = str_replace('%U', $username, $comment);
        if ($row->deleted) {
            $comment = str_replace('%c', $params['deletedtext'], $comment);
        } else {
            $comment = str_replace('%c', htmlspecialchars($row->bodytext), $comment);
        }
        $comment = str_replace('%d', strftime($params['dateformat'], strtotime($row->timestamp)), $comment);

        $output .= $comment.LF;
    }

    // Return the final output
    return $output;
}

?>
