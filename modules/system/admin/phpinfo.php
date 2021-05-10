<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  adminmain
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
 * @version     $Id: phpinfo.php,v 1.1 2007/10/19 16:24:15 roufneck Exp $
 */

/**
 * @admintitle  PHP info
 * @adminnav    2
 * @return  string  admin screen html content
 */
function admin_phpinfo()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'info');
    admin_sidebar_add('adminmain', 'about');

    ob_start();
    @phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_VARIABLES);
    $phpinfo = ob_get_clean();

    $phpinfo = trim($phpinfo);
    preg_match_all('#<body[^>]*>(.*)</body>#si', $phpinfo, $output);

    if (empty($phpinfo) || empty($output))
    {
        props_error("No PHP info available.");
        return;
    }

    $output = $output[1][0];

    // expose_php can make the image not exist
    if (preg_match('#<a[^>]*><img[^>]*></a>#', $output))
    {
        $output = preg_replace('#<tr class="v"><td>(.*?<a[^>]*><img[^>]*></a>)(.*?)</td></tr>#s', '<tr class="row1"><td><table class="type2"><tr><td>\2</td><td>\1</td></tr></table></td></tr>', $output);
    }
    else
    {
        $output = preg_replace('#<tr class="v"><td>(.*?)</td></tr>#s', '<tr class="row1"><td><table class="type2"><tr><td>\1</td></tr></table></td></tr>', $output);
    }
    $output = preg_replace('#<table[^>]+>#i', '<table>', $output);
    $output = preg_replace('#<img border="0"#i', '<img', $output);
    $output = str_replace(array('class="e"', 'class="v"', 'class="h"', '<hr />', '<font', '</font>'), array('class="row1"', 'class="row2"', '', '', '<span', '</span>'), $output);

    if (empty($output))
    {
        props_error("No PHP info available.");
        return;
    }

    $orig_output = $output;

    preg_match_all('#<div class="center">(.*)</div>#siU', $output, $output);
    $output = (!empty($output[1][0])) ? $output[1][0] : $orig_output;

    return $output;
}

?>
