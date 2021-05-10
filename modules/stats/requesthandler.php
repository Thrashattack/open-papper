<?php
/**
 * Module functions
 *
 * @package     modules
 * @subpackage  stats
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
 * @version     $Id: requesthandler.php,v 1.2 2007/12/11 15:46:31 roufneck Exp $
 */

// Trap 'command' request parameter
switch (props_getkey('request.cmd')) {

    case 'displaysection':
        if (props_getkey('request.section_id')) {
            props_stats_log('displaysection', props_getkey('request.section_id'));
        }
        break;

    case 'displaystory':
        if (props_getkey('request.story_id')) {
            props_stats_log('displaystory', props_getkey('request.story_id'));
        }
        break;

    case 'polls':
        if (props_getkey('request.poll_id')) {
            props_stats_log('polls', props_getkey('request.poll_id'));
        }
        break;

    case 'media':
        if (props_getkey('request.media_id')) {
            props_stats_log('media', props_getkey('request.media_id'));
        }
        break;
}

/**
 * Log stats
 */
function props_stats_log($command, $id)
{
    // Insert OR update session data
    $q = "INSERT INTO props_stats_log SET "
        ."  log_stamp = '".date('Y-m-d')."', "
        ."  command = '".sql_escape_string($command)."', "
        ."  id = '".(int) $id ."', "
        ."  hits = 1 "
        ."ON DUPLICATE KEY UPDATE "
        ."  hits = hits + 1";
    return sql_query($q);
}

?>
