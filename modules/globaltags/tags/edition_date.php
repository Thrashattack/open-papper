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
 * @version     $Id: edition_date.php,v 1.7 2007/07/12 12:51:53 roufneck Exp $
 */

// loadLibs
props_loadLib('editions');

/**
 * Returns edition date
 *
 * @tag     {edition_date}
 * @param   array  &$params  parameters
 * <ul>
 *   <li><b>dateformat</b> - See PHP's {@link  http://www.php.net/manual/en/function.strftime.php  strftime}.</li>
 * </ul>
 * @return  string  generated html code
 */
function tag_edition_date(&$params)
{
    //  Set default date format if not supplied
    if (!isset($params['dateformat'])) {
        $params['dateformat'] = '%x';
    }

    return strftime($params['dateformat'], edition_date(props_getkey('request.edition_id')));
}

?>
