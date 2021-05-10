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
 * @version     $Id: payments_report.php,v 1.2 2007/12/11 15:46:29 roufneck Exp $
 */

/**
 * @admintitle  View payments stats
 * @adminnav    4
 * @return  string  admin screen html content
 */
function admin_payments_report()
{
    // Get the needed posted vars here.
    $start_date = props_getrequest('start_date', VALIDATE_DATE, '!EMPTY,MAX10', TRUE);
    $end_date = props_getrequest('end_date', VALIDATE_DATE, '!EMPTY,MAX10', TRUE);

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Export to CSV"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // output appropriate content-type header
                header("Content-type: text/comma-separated-values");
                header("Content-Disposition: inline; filename=$start_date" . "_to_" . "$end_date.csv");

                // Get report
                $q  = "SELECT props_commerce_transactions.*, props_users.username "
                    . "FROM props_commerce_transactions, props_users "
                    . "WHERE props_commerce_transactions.user_id = props_users.user_id "
                    . "  AND TO_DAYS(transaction_date) >= TO_DAYS('$start_date') "
                    . "  AND TO_DAYS(transaction_date) <= TO_DAYS('$end_date') "
                    . "ORDER BY transaction_date";
                $result = sql_query($q);

                echo '"' . props_gettext("Date") . '","' . props_gettext("User ID") . '","' . props_gettext("Username") . '","' . props_gettext("IP Address") . '","' . props_gettext("Description") . '","' . props_gettext("Amount") . '","' . props_gettext("Reference ID") . '"' . "\n";
                while ($row = sql_fetch_object($result)) {

                    echo
                        '"' . strftime("%c", strtotime($row->transaction_date)) . '",' .
                        '"' . $row->user_id . '",' .
                        '"' . $row->username . '",' .
                        '"' . $row->ip_address . '",' .
                        '"' . $row->description . '",' .
                        '"' . $row->amount . '",' .
                        '"' . $row->reference_id . '"';

                    echo "\n";
                }

                // Exit here
                exit;
            }
            break;

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

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post">'.LF
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
            .'    <th>' . props_gettext("Description") . '</th>'.LF
            .'    <th>' . props_gettext("Amount") . '</th>'.LF
            .'    <th>' . props_gettext("Signups") . '</th>'.LF
            .'    <th>' . props_gettext("Revenue") . '</th>'.LF
            .'  </tr>'.LF;

        // Get summary
        $q  = "SELECT *, "
            . "  COUNT(description) AS total, "
            . "  SUM(amount) AS revenue, "
            . "  CONCAT(description, amount) AS transaction "
            . "FROM props_commerce_transactions "
            . "WHERE 1 "
            . "  AND TO_DAYS(transaction_date) >= TO_DAYS('$start_date') "
            . "  AND TO_DAYS(transaction_date) <= TO_DAYS('$end_date') "
            . "GROUP BY transaction";
        $result = sql_query($q);

        if (!sql_num_rows($result)) {
            $output .= '  <tr class="row1"><td colspan="4" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
        } else {
            $row_num = 1;
            while($row = sql_fetch_object($result)) {

                $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td>' . htmlspecialchars($row->description) . '</td>'.LF
                .'    <td>' . $row->amount . '</td>'.LF
                .'    <td style="text-align: right;">' . $row->total . '</td>'.LF
                .'    <td style="text-align: right;">' . sprintf("%.2f", $row->revenue) . '</td>'.LF
                .'  </tr>'.LF;

                $sum_total += $row->total;
                $sum_revenue += $row->revenue;
                $row_num = ($row_num == 1) ? 2 : 1;
            }

            $output .=
             '  <tr class="row'.$row_num.'">'.LF
            .'    <th style="text-align: right;" colspan="2">' . props_gettext("Total") . '</th>'.LF
            .'    <td style="text-align: right;">' . $sum_total . '</td>'.LF
            .'    <td style="text-align: right;">' . sprintf("%.2f", $sum_revenue) . '</td>'.LF
            .'  </tr>'.LF;
        }

        $output .=
             '</table>'.LF

            .'<h1>' . props_gettext("Payments report") . '</h1>'.LF
            .'<p><input class="button" name="op" type="submit" value="' . props_gettext("Export to CSV") . '" /></p>'.LF
            .'<table class="hairline searchresults">'.LF
            .'  <tr>'.LF
            .'    <th>' . props_gettext("Date") . '</th>'.LF
            .'    <th>' . props_gettext("Username") . '</th>'.LF
            .'    <th>' . props_gettext("IP address") . '</th>'.LF
            .'    <th>' . props_gettext("Description") . '</th>'.LF
            .'    <th>' . props_gettext("Amount") . '</th>'.LF
            .'    <th>' . props_gettext("Reference ID") . '</th>'.LF
            .'  </tr>'.LF;

        // Get payments
        $q  = "SELECT props_commerce_transactions.*, props_users.username "
            . "FROM props_commerce_transactions, props_users "
            . "WHERE props_commerce_transactions.user_id = props_users.user_id "
            . "  AND TO_DAYS(transaction_date) >= TO_DAYS('$start_date') "
            . "  AND TO_DAYS(transaction_date) <= TO_DAYS('$end_date') "
            . "ORDER BY transaction_date";
        $result = sql_query($q);

        if (!sql_num_rows($result)) {
            $output .= '  <tr class="row1"><td colspan="6" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
        } else {
            $row_num = 1;
            while($row = sql_fetch_object($result)) {
                $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td>' . strftime("%c", strtotime($row->transaction_date)) . '</td>'.LF
                .'    <td>' . $row->username . '</td>'.LF
                .'    <td>' . $row->ip_address . '</td>'.LF
                .'    <td>' . $row->description . '</td>'.LF
                .'    <td style="text-align: right;">' . $row->amount . '</td>'.LF
                .'    <td style="text-align: center;">' . $row->reference_id . '</td>'.LF
                .'  </tr>'.LF;

                $row_num = ($row_num == 1) ? 2 : 1;
            }
        }

        $output .= '</table>'.LF;
    }
/*


        $q  = "SELECT *, UNIX_TIMESTAMP(stamp) AS unix_stamp "
            . "FROM props_archives_signups "
            . "WHERE TO_DAYS(stamp) >= TO_DAYS('$start_date') "
            . "AND TO_DAYS(stamp) <= TO_DAYS('$end_date') "
            . "ORDER BY stamp ASC";
        $result = sql_query($q);

        if (!sql_num_rows($result)) {
            $output .=
                 '<h1>' . props_gettext("Details") . '</h1>'.LF
                .'<p>' . props_gettext("No results found.") . '</p>';
        } else {

            $output .=
                 '<h1>' . props_gettext("Details") . '</h1>'.LF
                .'<p><input class="button" name="op" type="submit" value="' . props_gettext("Export to CSV") . '" /></p>'.LF
                .'<table class="hairline searchresults">'.LF
                .'  <tr>'.LF
                .'    <th>' . props_gettext("Timestamp") . '</th>'.LF
                .'    <th>' . props_gettext("Type") . '</th>'.LF
                .'    <th>' . props_gettext("Username") . '</th>'.LF
                .'    <th>' . props_gettext("IP address") . '</th>'.LF
                .'    <th>' . props_gettext("Plan description") . '</th>'.LF
                .'    <th>' . props_gettext("Amount") . '</th>'.LF
                .'    <th>' . props_gettext("Transaction ID") . '</th>'.LF
                .'  </tr>'.LF;

            while ($row = sql_fetch_object($result)) {
                $output .=
                     '  <tr>'.LF
                    .'    <td>' . date("Y-M-d H:i", $row->unix_stamp) . '</td>'.LF
                    .'    <td>' . $row->type . '</td>'.LF
                    .'    <td>' . $row->username . '</td>'.LF
                    .'    <td>' . $row->ip_address . '</td>'.LF
                    .'    <td>' . $row->plan_description . '</td>'.LF
                    .'    <td style="text-align: right;">' . $row->amount . '</td>'.LF
                    .'    <td style="text-align: center;">' . $row->transaction_reference_id . '</td>'.LF
                    .'  </tr>'.LF;
            }

            $output .=
                 '</table>'.LF;
        }

    }
*/
    $output .= '</form>'.LF;

    return $output;

}

?>
