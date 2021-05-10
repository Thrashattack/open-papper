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
 * @version     $Id: groups_manage.php,v 1.5 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Group management
 * @adminnav    3
 * @return  string  admin screen html content
 */
function admin_groups_manage()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'groups_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'group_add');

    // Set referer
    props_redirect(FALSE, 'set');

    $output =
         '<h1>'.props_gettext("Groups").'</h1>'.LF
        .'<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Group name") . '</th>'.LF
        .'    <th>' . props_gettext("Default") . '</th>'.LF
        .'    <th>' . props_gettext("Members") . '</th>'.LF
        .'    <th colspan="2">' . props_gettext("Actions") . '</th>'.LF
        .'  </tr>'.LF;

    $q  = "SELECT props_users_groups.*, COUNT(props_users.user_id) AS count "
        . "FROM props_users_groups "
        . "LEFT JOIN props_users ON props_users_groups.group_id = props_users.group_id "
        . "GROUP BY props_users_groups.group_id ORDER BY group_name";
    $result = sql_query($q);
    $row_num = 1;
    while ($row = sql_fetch_object($result)) {

        switch ($row->default_user_type) {
            case PROPS_USERTYPE_FOUNDER: $usertype = '<span class="usertype_founder">'.props_gettext("Founder").'</span>'; break;
            case PROPS_USERTYPE_ADMIN: $usertype = '<span class="usertype_admin">'.props_gettext("Administrator").'</span>'; break;
            case PROPS_USERTYPE_USER: $usertype = '<span class="usertype_user">'.props_gettext("User").'</span>'; break;
            case PROPS_USERTYPE_GUEST: $usertype = '<span class="usertype_guest">'.props_gettext("Guest").'</span>'; break;
            case PROPS_USERTYPE_BOT: $usertype = '<span class="usertype_bot">'.props_gettext("Bot").'</span>'; break;
            case PROPS_USERTYPE_CLOSED: $usertype = '<span class="usertype_closed">'.props_gettext("Closed").'</span>'; break;
            default: $usertype = ''; break;
        }

        $output .=
             '  <tr class="row'.$row_num.'">'.LF
            .'    <td>'.LF
            .'      <strong><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=group_edit&amp;group_id=' . $row->group_id . '">' . htmlspecialchars($row->group_name) . '</a></strong><br />'
            .'      '.htmlspecialchars($row->group_desc).LF
            .'    </td>'.LF
            .'    <td style="text-align: center;">' . $usertype . '</td>'.LF
            .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=users_manage&amp;group_id=' . $row->group_id . '">' . $row->count . '</a></td>'.LF
            .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=group_edit&amp;group_id=' . $row->group_id . '">[' . props_gettext("Edit") . ']</a></td>'.LF
            .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=users_manage&amp;group_id=' . $row->group_id . '">[' . props_gettext("Members") . ']</a></td>'.LF
            .'  </tr>'.LF;

        // Update row number
        $row_num = ($row_num == 1) ? 2 : 1;
    }

    $output .=
         '</table>'.LF;

    return $output;
}

?>
