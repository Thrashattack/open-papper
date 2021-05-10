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
 * @version     $Id: archives_subscription_plans_radioselect.php,v 1.7 2007/10/26 08:23:08 roufneck Exp $
 */

// loadLibs
props_loadLib('archives');

/**
 * Returns remaining credits
 *
 * @tag    {archives_credits_remaining}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_archives_subscription_plans_radioselect(&$params)
{
    $output = '';

    $q = "SELECT * FROM props_archives_subscription_plans ORDER BY amount ASC";
    $result = sql_query($q);

    while ($row = sql_fetch_object($result)) {
        $output .=
             '<input name="subscription_plan_id" type="radio" value="' . $row->plan_id . '" />&nbsp;&nbsp;'
            .'<b>' . sprintf('$%0.2f', $row->amount) . '</b>&nbsp;-&nbsp;' . $row->description . '<br />'.LF;
    }

    return $output;
}

?>
