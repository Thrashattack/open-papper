<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  commerce
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
 * @version     $Id: cc_exp_month_select.php,v 1.4 2006/10/22 19:55:18 roufneck Exp $
 */

/**
 * Returns a select menu listing the 12 months of the year
 *
 * @tag    {cc_exp_month_select}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_cc_exp_month_select(&$params)
{
    $output =
         '<select name="cc_exp_month">'.LF
        .'  <option value="1">01</option>'.LF
        .'  <option value="2">02</option>'.LF
        .'  <option value="3">03</option>'.LF
        .'  <option value="4">04</option>'.LF
        .'  <option value="5">05</option>'.LF
        .'  <option value="6">06</option>'.LF
        .'  <option value="7">07</option>'.LF
        .'  <option value="8">08</option>'.LF
        .'  <option value="9">09</option>'.LF
        .'  <option value="10">10</option>'.LF
        .'  <option value="11">11</option>'.LF
        .'  <option value="12">12</option>'.LF
        .'</select>'.LF;

    return $output;
}

?>
