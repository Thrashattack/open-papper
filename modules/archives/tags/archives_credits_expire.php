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
 * @version     $Id: archives_credits_expire.php,v 1.4 2007/11/24 04:23:59 phutureboy Exp $
 */

// loadLibs
props_loadLib('archives');

/**
 * Return remaining time
 *
 * @tag    {archives_credits_expire}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 * </ul>
 * @return  string  generated html code
 */
function tag_archives_credits_expire(&$params)
{
    if (!isset($_SESSION['PROPS_USER']['authenticated'])) {
        return;
    }

    //  Set default date format if not supplied
    if (!isset($params['dateformat'])) {
        $params['dateformat'] = '%x';
    }

    $time = archives_credits_expire($_SESSION['PROPS_USER']['user_id']);

    if (($time == '0000-00-00 00:00') || ($time == '0000-00-00 00:00:00')) {
        return props_gettext("Your credit(s) do not expire.");
    } else {
        return strftime($params['dateformat'], strtotime($time));
    }
}

?>
