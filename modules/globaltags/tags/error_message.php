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
 * @version     $Id: error_message.php,v 1.7 2007/08/22 21:55:26 roufneck Exp $
 */

/**
 * Returns error messages
 *
 * Example:
 * <code>
 * // ex. with POST/GET var
 * {error_message var="email_address"}
 * <p class="error">Invalid email address</p>
 * // output all error messages
 * {error_message}
 * <p class="error">The story is emailed to...</p>
 * </code>
 *
 * @tag     {error_message}
 * @param   array  &$params  parameters
 * @return  string  generated html code
 */
function tag_error_message(&$params)
{
    if (isset($params['var'])) {
        return props_geterror($params['var']);
    }

    $output = '';
    if (isset($GLOBALS['PROPS_ERRORSTACK'])) {
        foreach ($GLOBALS['PROPS_ERRORSTACK'] as $key => $val) {
            if (is_numeric($key)) {
                $output .= '<p class="error">'.$GLOBALS['PROPS_ERRORSTACK'][$key]['errstr'].'</p>'.LF;
            }
        }
    }

    return $output;
}

?>
