<?php
/**
 * Lib - database loader
 *
 * Load a different set of database functions depending upon which database
 * type is in use. If you want to add support for another database server,
 * create a functions file for it and include it in the switch statement.
 *
 * @package     api
 * @subpackage  database
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
 * @version     $Id: database.php,v 1.9 2007/09/20 08:42:57 roufneck Exp $
 */

/**
 * Database loader
 */
switch(props_getkey('config.db.type'))
{
    case 'mysql';
        require_once(props_getkey('config.dir.libs') . 'database/mysql.php');
        break;
    default;
        trigger_error('Configuration error: Invalid database type: ' . props_getkey('config.db.type'), E_USER_ERROR);
        break;
}

?>
