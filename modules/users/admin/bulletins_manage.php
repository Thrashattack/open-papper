<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  users
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
 * @version     $Id: bulletins_manage.php,v 1.9 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Manage bulletins
 * @adminnav    5
 * @return  string  admin screen html content
 */
function admin_bulletins_manage()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletins_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletin_add');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'bulletin_send');

    // Set referer
    props_redirect(FALSE, 'set');

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Bulletin name") . '</th>'.LF
        .'    <th>' . props_gettext("Bulletin shortname") . '</th>'.LF
        .'    <th>' . props_gettext("Subscribers") . '</th>'.LF
        .'    <th>' . props_gettext("Actions") . '</th>'.LF
        .'  </tr>'.LF;

    // $q  = "SELECT * FROM props_bulletins ORDER BY bulletin_name";
    $q  = "SELECT props_bulletins.bulletin_id, bulletin_name, bulletin_shortname, COUNT(user_id) AS count "
        . "FROM props_bulletins "
        . "LEFT JOIN props_bulletins_subscriptions ON props_bulletins.bulletin_id = props_bulletins_subscriptions.bulletin_id "
        . "GROUP BY props_bulletins.bulletin_id";
    $result = sql_query($q);
    $row_num = 1;
    while ($row = sql_fetch_object($result)) {
        $output .=
         '  <tr class="row'.$row_num.'">'.LF
        .'    <td><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=bulletin_edit&amp;bulletin_id=' . $row->bulletin_id . '">' . htmlspecialchars($row->bulletin_name) . '</a></td>'.LF
        .'    <td>' . htmlspecialchars($row->bulletin_shortname) . '</td>'.LF
        .'    <td style="text-align: center;">' . $row->count . '</td>'.LF
        .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=bulletin_send&amp;bulletin_id=' . $row->bulletin_id . '">[' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.bulletin_send') . ']</a></td>'.LF
        .'  </tr>'.LF;

        $row_num = ($row_num == 1) ? 2 : 1;
    }

    $output .= '</table>'.LF;

    return $output;
}

?>
