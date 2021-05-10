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
 * @version     $Id: db_maintenance.php,v 1.2 2007/12/11 15:46:32 roufneck Exp $
 */

/**
 * @admintitle  Database maintenance
 * @adminprivs  db_optimize   Optimize tables
 * @adminprivs  db_check      Check tables
 * @adminnav    4
 * @return  string  admin screen html content
 */
function admin_db_maintenance()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'info');
    admin_sidebar_add('adminmain', 'about');

    props_loadLib('system');

    $op = props_getrequest('op');
    $tables = props_getrequest('tables', 'a-zA-Z_, ', 'SANITIZE');

    $optimize_priv = admin_has_priv($GLOBALS['PROPS_MODULE'], 'db_optimize');
    $check_priv = admin_has_priv($GLOBALS['PROPS_MODULE'], 'db_check');

    $output = '';

    // Handle form submissions here
    switch($op) {

        case props_gettext('.system.db_optimize'):
        case 'optimize':
            // If no errors, do update, otherwise drop through and display errors
            if (!$optimize_priv) {
                props_error("You do not have permission to perform the selected action.");
            } elseif (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                $result = sql_query("OPTIMIZE TABLE ".sql_escape_string($tables));
                if ($result) {
                    props_error(sprintf(props_gettext("Successfully optimized table '%s'."), $tables));
                }
            }
            break;

        case props_gettext('.system.db_check'):
        case 'check':
            // If no errors, do update, otherwise drop through and display errors
            if (!$check_priv) {
                props_error("You do not have permission to perform the selected action.");
            } elseif (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                $result = sql_query("CHECK TABLE ".sql_escape_string($tables));
                if ($result) {
                    props_error(sprintf(props_gettext("Successfully checked table '%s'."), $tables));
                }
            }
            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Table") . '</th>'.LF
        .'    <th>' . props_gettext("Engine") . '</th>'.LF
        .'    <th>' . props_gettext("Records") . '</th>'.LF
        .'    <th>' . props_gettext("Size") . '</th>'.LF
        .'    <th>' . props_gettext("Overhead") . '</th>'.LF
        .'    <th>' . props_gettext("Next index") . '</th>'.LF
        .'    <th>' . props_gettext("Last checked") . '</th>'.LF
        .'  </tr>'.LF;

    $row_num = 1;
    static $sum_tblsize, $sum_rows, $sum_overhead, $all_tables;

    $result = sql_query("SHOW TABLE STATUS");
    while ($row = sql_fetch_object($result)) {

        $formatted_size = $unit = $formatted_overhead = $overhead_unit = '';

        // Original code is from phpMyAdmin
        // MyISAM, ISAM or Heap table: Row count, data size and index size is accurate.
        if (preg_match('@^(MyISAM|ISAM|HEAP|MEMORY)$@', $row->Engine)) {
            $tblsize = doubleval($row->Data_length) + doubleval($row->Index_length);
            $sum_tblsize += $tblsize;
            list($formatted_size, $unit) = props_formatByteDown($tblsize, 3, ($tblsize > 0) ? 1 : 0);
            if (isset($row->Data_free) && $row->Data_free > 0) {
                list($formatted_overhead, $overhead_unit) = props_formatByteDown($row->Data_free, 3, ($row->Data_free > 0) ? 1 : 0);
                $sum_overhead += $row->Data_free;
            }
            $sum_rows += $row->Rows;
        } elseif ($row->Engine == 'InnoDB') {
            // InnoDB table: Row count is not accurate but data and index sizes are.
            $tblsize = $row->Data_length + $row->Index_length;
            $sum_tblsize += $tblsize;
            list($formatted_size, $unit) = props_formatByteDown($tblsize, 3, ($tblsize > 0) ? 1 : 0);
            $sum_rows += $row->Rows;
        } elseif (preg_match('@^(MRG_MyISAM|BerkeleyDB)$@', $row->Engine)) {
            // Merge or BerkleyDB table: Only row count is accurate.
            $formatted_size = ' - ';
            $unit = '';
            $sum_rows += $row->Rows;
        } else {
            // Unknown table type.
            $formatted_size = 'unknown';
            $unit = '';
        }

        $output .=
             '  <tr class="row'.$row_num.'">'.LF
            .'    <td>' . $row->Name . '</td>'.LF
            .'    <td style="text-align: center;">' . $row->Engine . '</td>'.LF
            .'    <td style="text-align: center;">' . $row->Rows . '</td>'.LF
            .'    <td style="text-align: right;">' . $formatted_size . ' ' . $unit . '</td>'.LF
            .'    <td style="text-align: right;">';

        if ($optimize_priv && $formatted_overhead > 0) {
            $output .= '<a href="?module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;op=optimize&amp;tables=' . $row->Name . '">' . $formatted_overhead . ' ' . $overhead_unit . '</a>';
        } else {
            $output .= $formatted_overhead . ' ' . $overhead_unit;
        }

        $output .=
             '</td>'.LF
            .'    <td style="text-align: center;">' . $row->Auto_increment . '</td>'.LF
            .'    <td>';

        if ($row->Check_time) {
            if ($check_priv) $output .= '<a href="?module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;op=check&amp;tables=' . $row->Name . '">' . $row->Check_time . '</a>';
            else $output .=  $row->Check_time;
        } else {
            if ($check_priv) $output .= '<a href="?module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;op=check&amp;tables=' . $row->Name . '">' . props_gettext("Not checked") . '</a>';
            else $output .= props_gettext("Not checked");
        }

        $output .=
             '</td>'.LF
            .'  </tr>'.LF;

        $all_tables .= ($all_tables == '') ? $row->Name : ', '.$row->Name;

        // Update row number
        $row_num = ($row_num == 1) ? 2 : 1;
    }

    list($formatted_tblsize, $tblsize_unit) = props_formatByteDown($sum_tblsize, 3, ($tblsize > 0) ? 1 : 0);
    list($formatted_overhead, $overhead_unit) = props_formatByteDown($sum_overhead, 3, ($tblsize > 0) ? 1 : 0);

    $output .=
         '  <tr class="row'.$row_num.'">'.LF
        .'    <th style="text-align: right;" colspan="2">' . props_gettext("Total") . ':&nbsp;</th>'.LF
        .'    <td style="text-align: center;">' . $sum_rows . '</td>'.LF
        .'    <td style="text-align: right;">' . $formatted_tblsize . ' ' . $tblsize_unit . '</td>'.LF
        .'    <td style="text-align: right;">' . $formatted_overhead . ' ' . $overhead_unit . '</td>'.LF
        .'    <td>&nbsp;</td>'.LF
        .'    <td>&nbsp;</td>'.LF
        .'  </tr>'.LF
        .'</table>'.LF;

    $buttons = '';

    if ($optimize_priv) {
        $buttons .= '    <input class="button" name="op" type="submit" value="' . props_gettext('.system.db_optimize') . '" />&nbsp;'.LF;
    }
    if ($check_priv) {
        $buttons .= '    <input class="button" name="op" type="submit" value="' . props_gettext('.system.db_check') . '" />&nbsp;'.LF;
    }

    if (!empty($buttons)) {
        $output .=
         '<br />'.LF
        .'<form action="./" method="post">'.LF
        .'  <p>'.LF
        .'    <input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'    <input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'    <input name="tables" type="hidden" value="' . $all_tables . '" />'.LF
        . $buttons
        .'  </p>'.LF
        .'</form>'.LF;
    }

    return $output;
}

?>
