<?php
/**
 * Lib - editions functions
 *
 * @package     api
 * @subpackage  editions
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
 * @version     $Id: editions.php,v 1.5 2007/08/22 21:55:23 roufneck Exp $
 */

/**
 * Return current edition id
 *
 * This returns the edition_id of the current edition (i.e. the most
 * recently published edition, not necessarily the one currently being
 * displayed).
 *
 * @param   bool   $die  set to FALSE returns FALSE in stead of die command
 * @return  mixed  edition_id or FALSE/DIE on failure
 */
function edition_current_id($die = TRUE)
{
    if (!isset($GLOBALS['PROPS_CURRENT_EDITION'])) {

        $q  = "SELECT edition_id FROM props_editions "
            . "WHERE !ISNULL(publish_date) "
            . "ORDER BY publish_date desc LIMIT 1";
        $result = sql_query($q);
        $row = sql_fetch_object($result);
        if (!$row && $die) {
            $GLOBALS['PROPS_ERRORDESC'] = props_gettext("This site has nothing published yet. Please come back in a short time.");
            trigger_error('There are no published editions.', E_USER_ERROR);
        } elseif (!$row) {
            $GLOBALS['PROPS_CURRENT_EDITION'] = FALSE;
        } else {
            $GLOBALS['PROPS_CURRENT_EDITION'] = $row->edition_id;
        }
    }

    return $GLOBALS['PROPS_CURRENT_EDITION'];
}

/**
 * Returns Unix timestamp of specified edition
 *
 * @param   int  $edition_id
 * @return  int  Unix timestamp
 */
function edition_date($edition_id)
{
    // Get date/time of specified edition
    $q  = "SELECT publish_date FROM props_editions WHERE edition_id = $edition_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    // Convert to timestamp and return
    return strtotime($row->publish_date);
}

?>
