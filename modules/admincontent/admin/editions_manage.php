<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  admincontent
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
 * @version     $Id: editions_manage.php,v 1.16 2007/12/11 15:46:29 roufneck Exp $
 */

/**
 * @admintitle  Manage editions
 * @adminprivs  editions_manage_live  Manage live site
 * @adminprivs  editions_manage_staging  Manage staging editions
 * @adminprivs  edition_preview  Preview staging editions
 * @adminnav    3
 * @return  string  admin screen html content
 */
function admin_editions_manage()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'editions_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'edition_add');

    // Set referer
    props_redirect(FALSE, 'set');

    $output = '';

    if (admin_has_priv('admincontent', 'editions_manage_live')) {
        $output .= '<h1>'.props_gettext("Currently published edition (live site)").'</h1>'.LF;

        $q  = "SELECT t1.*, COUNT(t2.story_id) AS story_count FROM props_editions AS t1 "
            . "LEFT JOIN props_stories AS t2 "
            . "ON t1.edition_id = t2.edition_id "
            . "WHERE !ISNULL(t1.publish_date) "
            . "GROUP BY t1.edition_id "
            . "ORDER BY t1.publish_date desc LIMIT 1";
        $result = sql_query($q);

        $output .=
             '<table>'.LF
            .'  <tr>'.LF
            .'    <th>' . props_gettext("No.") . '</th>'.LF
            .'    <th>' . props_gettext("Label") . '</th>'.LF
            .'    <th>' . props_gettext("Publish date") . '</th>'.LF
            .'    <th>' . props_gettext("Stories") . '</th>'.LF
            .'    <th colspan="2">' . props_gettext("Actions") . '</th>'.LF
            .'  </tr>'.LF;

        if (!sql_num_rows($result)) {
            $output .=  '<tr class="row1"><td colspan="5" style="text-align: center;">' . props_gettext("No edition is currently published.") . '</td></tr>'.LF;
        } else {
            $row_num = 1;
            while ($row = sql_fetch_object($result)) {
                $output .=
                     '  <tr class="row'.$row_num.'">'.LF
                    .'    <td style="text-align: center;">' . $row->edition_id . '</td>'.LF
                    .'    <td>' . htmlspecialchars($row->label) . '</td>'.LF
                    .'    <td style="text-align: center;">' . strftime(props_getkey('config.date.format'), strtotime($row->publish_date)) . '</td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=storysearch&amp;include[]=edition_id&amp;edition_id=' . $row->edition_id . '&amp;op=search" title="' . props_gettext("View stories") . '">' . $row->story_count . '</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=story_add&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Add story") . '">[' . props_gettext("Add story") . ']</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=edition_order&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Order") . '">[' . props_gettext("Order") . ']</a></td>'.LF
                    .'  </tr>'.LF;

                // Update row number
                $row_num = ($row_num == 1) ? 2 : 1;
            }
        }
    }

    if (admin_has_priv('admincontent', 'editions_manage_staging')) {
        $output .=
             '</table>'.LF
            .'<h1>' . props_gettext("Staging editions (under construction)") . '</h1>'.LF
            .'<table>'.LF
            .'  <tr>'.LF
            .'    <th>' . props_gettext("No.") . '</th>'.LF
            .'    <th>' . props_gettext("Label") . '</th>'.LF
            .'    <th>' . props_gettext("Stories") . '</th>'.LF
            .'    <th colspan="5">' . props_gettext("Actions") . '</th>'.LF
            .'  </tr>'.LF;

        $q  = "SELECT t1.*, COUNT(t2.story_id) AS story_count FROM props_editions AS t1 "
            . "LEFT JOIN props_stories AS t2 "
            . "ON t1.edition_id = t2.edition_id "
            . "WHERE ISNULL(t1.publish_date) "
            . "GROUP BY t1.edition_id ";
        $result = sql_query($q);

        if (!sql_num_rows($result)) {
            $output .=  '<tr class="row1"><td colspan="6" style="text-align: center;">' . props_gettext("No staging editions are currently open.") . '</td></tr>'.LF;
        } else {
            $row_num = 1;
            while ($row = sql_fetch_object($result)) {
                $output .=
                     '  <tr class="row'.$row_num.'">'.LF
                    .'    <td style="text-align: center;">' . $row->edition_id . '</td>'.LF
                    .'    <td>' . htmlspecialchars($row->label) . '</td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=storysearch&amp;include[]=edition_id&amp;edition_id=' . $row->edition_id . '&amp;op=search" title="' . props_gettext("View stories") . '">' . $row->story_count . '</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=story_add&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Add story") . '">[' . props_gettext("Add story") . ']</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=edition_order&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Order") . '">[' . props_gettext("Order") . ']</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="../?edition_id=' . $row->edition_id . '&amp;preview=' . TRUE . '" title="' . props_gettext("Preview") . '">[' . props_gettext("Preview") . ']</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=edition_publish&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Publish") . '">[' . props_gettext("Publish") . ']</a></td>'.LF
                    .'    <td style="text-align: center;"><a href="./?module=admincontent&amp;function=edition_delete&amp;edition_id=' . $row->edition_id . '&amp;pageID=' . props_pageID() . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');">[' . props_gettext("Delete") . ']</a></td>'.LF
                    .'  </tr>'.LF;
                // Update row number
                $row_num = ($row_num == 1) ? 2 : 1;
            }
        }
        $output .= '</table>'.LF;
    }

    return $output;
}

?>
