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
 * @version     $Id: cc_exp_year_select.php,v 1.4 2006/10/22 19:55:18 roufneck Exp $
 */

/**
 * Returns a select menu listing possible credit card expiration years
 *
 * @tag    {cc_exp_year_select}
 * @param  array  &$params  parameters
 * @return  string  generated html code
 */
function tag_cc_exp_year_select(&$params)
{
    $current_year = date("Y");

    $output  = '<select name="cc_exp_year">'.LF;

    // 6 years ahead oughta be enough for anyone's credit card
    for ($year = $current_year; $year <= ($current_year + 6); $year++) {
        $output .= '<option value="' . $year . '">' . $year . '</option>'.LF;
    }

    $output .= '</select>'.LF;

    return $output;
}

?>
