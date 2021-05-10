<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  stats
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
 * @version     $Id: stats_sections.php,v 1.2 2008/01/07 17:53:16 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * @admintitle  View section stats
 * @adminnav    1
 * @return  string  admin screen html content
 */
function admin_stats_sections()
{
    // Get the needed posted vars here.
    $start_date = props_getrequest('start_date', VALIDATE_DATE, '!EMPTY,MAX10', TRUE);
    $end_date = props_getrequest('end_date', VALIDATE_DATE, '!EMPTY,MAX10', TRUE);

    // Handle form submissions here
    $op = props_getrequest('op');
    switch($op) {

        default:
            if (empty($start_date)) $start_date = date('Y-m-01');
            if (empty($end_date)) $end_date = date('Y-m-d');
            break;

    } // END switch

    $GLOBALS['JavaScript'] =
         '  <link rel="stylesheet" type="text/css" media="screen" href="' . props_getkey('config.url.scripts') . 'calendar.css" />'.LF
        .'  <script type="text/javascript">'.LF
        .'    var languageCode = \'en\';'.LF
        .'    var pathToImages = \'' . props_getkey('config.url.scripts') . 'images/\';'.LF
        .'  </script>'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'calendar.js"></script>'.LF;

    $GLOBALS['PROPS_FUNCTION_INFO'] =
         '<p>'.props_gettext("View statistics for:").'</p>'.LF
        .'<p>'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;start_date=' . date('Y-m-d') . '&amp;end_date=' . date('Y-m-d') . '">' . props_gettext("Today") . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;start_date=' . date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) . '&amp;end_date=' . date('Y-m-d') . '">' . sprintf(props_gettext("Last %s days"), 7) . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;start_date=' . date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))) . '&amp;end_date=' . date('Y-m-d') . '">' . sprintf(props_gettext("Last %s days"), 30) . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;start_date=' . date('Y-m-d', mktime(0, 0, 0, date("m"), 1, date("Y"))) . '&amp;end_date=' . date('Y-m-d') . '">' . props_gettext("This month") . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;start_date=' . date('Y-m-d', mktime(0, 0, 0, 1, 1, date("Y"))) . '&amp;end_date=' . date('Y-m-d') . '">' . props_gettext("This year") . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;start_date=' . date('Y-m-d', mktime(0, 0, 0, 1, 1, date("Y")-1)) . '&amp;end_date=' . date('Y-m-d', mktime(0, 0, 0, 12, 31, date("Y")-1)) . '">' . props_gettext("Last year") . '</a><br />'.LF
        .'</p>'.LF;

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="get">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Settings") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Start date") . '</label></dt>'.LF
        .'    <dd><input class="medium" type="text" id="start_date" name="start_date" value="' . htmlspecialchars($start_date) . '" />'.LF
        .'      <img src="./images/button_calendar.png" style="cursor: pointer;" alt="Date selector" title="Date selector" onclick="displayCalendar(document.getElementById(\'start_date\'),\'yyyy-mm-dd\',this)" /></dd>'.LF
        .((props_geterror('start_date')) ? '      <dd>' . props_geterror('start_date') . '</dd>'.LF : '')
        .'    <dt><label>' . props_gettext("End date") . '</label></dt>'.LF
        .'    <dd><input class="medium" type="text" id="end_date" name="end_date" value="' . htmlspecialchars($end_date) . '" />'.LF
        .'      <img src="./images/button_calendar.png" style="cursor: pointer;" alt="Date selector" title="Date selector" onclick="displayCalendar(document.getElementById(\'end_date\'),\'yyyy-mm-dd\',this)" /></dd>'.LF
        .((props_geterror('end_date')) ? '      <dd>' . props_geterror('end_date') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'</fieldset>'.LF
        .'  <p>'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Generate report") . '" />'.LF
        .'  </p>'.LF;

    // Output summary
    if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
        $sum_total = 0;
        $sum_revenue = 0;

        $output .=
             '<h1>' . props_gettext("Summary") . '</h1>'.LF
            .'<table class="hairline">'.LF
            .'  <tr>'.LF
            .'    <th>' . props_gettext("Section") . '</th>'.LF
            .'    <th>' . props_gettext("Path") . '</th>'.LF
            .'    <th>' . props_gettext("Hits") . '</th>'.LF
            .'  </tr>'.LF;

        // Assemble SQL query to get the list of stories
        $q  = "SELECT *, SUM(hits) as sum_hits FROM props_stats_log "
            . "LEFT JOIN props_sections ON props_stats_log.id = props_sections.section_id "
            . "WHERE props_stats_log.command = 'displaysection' "
            . "AND props_stats_log.log_stamp >= '" . $start_date . "' "
            . "AND props_stats_log.log_stamp <= '" . $end_date . "' "
            . "GROUP BY props_stats_log.command, props_stats_log.id "
            . "ORDER BY sum_hits DESC "
            . "LIMIT 0, 50";
        $result = sql_query($q);

        if (!sql_num_rows($result)) {
            $output .= '  <tr class="row1"><td colspan="4" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
        } else {
            $row_num = 1;
            while($row = sql_fetch_object($result)) {

                $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td>' . htmlspecialchars($row->fullname) . '</td>'.LF
                .'    <td>' . section_name_path($row->section_id) . '</td>'.LF
                .'    <td style="text-align: right;">' . $row->sum_hits . '</td>'.LF
                .'  </tr>'.LF;

                $row_num = ($row_num == 1) ? 2 : 1;
            }
        }

        $output .=
             '</table>'.LF;
    }

    $output .= '</form>'.LF;

    return $output;

}

?>
