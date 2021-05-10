<?php
/**
 * Admin function
 *
 * @package     modules
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
 * @version     $Id: subscription_plans_manage.php,v 1.5 2007/10/26 08:23:08 roufneck Exp $
 */

/**
 * @admintitle  Manage subscription plans
 * @adminnav    2
 * @return  string  admin screen html content
 */
function admin_subscription_plans_manage()
{
    // Set referer
    props_redirect(FALSE, 'set');

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext('.archives.subscription_plan_add'):
            // Set referer
            props_redirect('goto', array('function'=>'subscription_plan_add'));
            break;

    } // END switch

    $output =
         '<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Plan description") . '</th>'.LF
        .'    <th>' . props_gettext("Story credits included") . '</th>'.LF
        .'    <th>' . props_gettext("Days until expire") . '</th>'.LF
        .'    <th>' . props_gettext("Amount") . '</th>'.LF
        .'  </tr>'.LF;

    $q  = "SELECT * FROM props_archives_subscription_plans "
        . "ORDER BY amount ASC";
    $result = sql_query($q);
    $row_num = 1;
    while ($row = sql_fetch_object($result)) {
        $credits = ($row->credits == '-1') ? props_gettext("unlimited") : $row->credits;
        $expire = ($row->days_until_expire) ? $row->days_until_expire : props_gettext("No expiration");

        $output .=
             '  <tr class="row'.$row_num.'">'.LF
            .'    <td><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=subscription_plan_edit&amp;plan_id=' . $row->plan_id . '">' . $row->description . '</a></td>'.LF
            .'    <td>' . $credits . '</td>'.LF
            .'    <td>' . $expire . '</td>'.LF
            .'    <td>' . sprintf("%.2f", $row->amount) . '</td>'.LF
            .'  </tr>'.LF;

        // Update row number
        $row_num = ($row_num == 1) ? 2 : 1;
    }

    $output .=
         '</table>'.LF
        .'<form action="./" method="post">'.LF
        .'  <p>'.LF
        .'    <input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'    <input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext('.archives.subscription_plan_add') . '" />'.LF
        .'  </p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
