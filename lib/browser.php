<?php
/**
 * Lib - browser functions
 *
 * @package     api
 * @subpackage  browser
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
 * @version     $Id: browser.php,v 1.4 2006/10/22 19:55:15 roufneck Exp $
 */

/**
 * Sends HTTP headers to prevent caching the page
 */
function browser_nocache()
{
    header("Cache-control: private, no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Past date
    header("Pragma: no-cache");
}

?>
