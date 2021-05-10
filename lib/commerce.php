<?php
/**
 * Lib - commerce functions
 *
 * @package     api
 * @subpackage  commerce
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
 * @version     $Id: commerce.php,v 1.14 2007/10/26 08:23:06 roufneck Exp $
 */

/**
 * Load pfpro
 *
 * If native pfpro() functions are not available, load the drop-in replacement
 * functions.
 */
if (!function_exists('pfpro_init')) {
    require_once(props_getkey('config.dir.libs') . 'commerce/pfpro_compat.php');
}

/**
 * Run a credit card transaction
 *
 * Runs a credit card transaction via the designated credit card processing
 * gateway. Returns an associative array consisting of three elements:
 * - <b>approved</b>       - set to TRUE if the transaction cleared, or FALSE otherwise
 * - <b>result_code</b>    - contains any numeric result codes set by the gateway
 * - <b>result_message</b> - contains any textual message returned by the gateway
 *
 * Also sets the following registry keys:
 * - <b>commerce.approved</b>
 * - <b>commerce.result_code</b>
 * - <b>commerce.result_message</b>
 *
 * @param   string  $cc_name       credit card holder name
 * @param   int     $cc_number     credit card number
 * @param   int     $cc_exp_month  credit card expiration month
 * @param   int     $cc_exp_year   credit card expiration year
 * @param   string  $transaction_amount  Transaction amount
 * @param   string  $description   Transaction description
 * @return  array   associative array (approved, result_code, result_message)
 */
function cc_transaction($cc_name, $cc_number, $cc_exp_month, $cc_exp_year, $transaction_amount, $description)
{
    $result = array();

    // Only uncomment this for debug purposes
    //$result['reference_id'] = TRUE; $result['approved'] = TRUE; return $result;

    // Init PFPro library
    pfpro_init();

    // Prepare array containing transaction info
    $cc_expire = sprintf("%02d%02d", $cc_exp_month, ($cc_exp_year - 2000));
    $transaction = array(
                    'USER'    => props_getkey('config.pfpro.user'),
                    'PWD'     => props_getkey('config.pfpro.password'),
                    'PARTNER' => props_getkey('config.pfpro.partner'),
                    'TRXTYPE' => 'S',
                    'TENDER'  => 'C',
                    'AMT'     => $transaction_amount,
                    'ACCT'    => $cc_number,
                    'EXPDATE' => $cc_expire
                   );

    $response = pfpro_process($transaction, props_getkey('config.pfpro.hostname'));

    if ($response) {
        if ($response['RESULT'] == 0) {
            $result['approved'] = TRUE;
            $result['reference_id'] = $response['PNREF'];

            // Log this transaction (we log both CC transactions and archives signups separately
            // to provide an audit trail in case something goes awry between the CC transaction
            // and the credits being posted to the user's account.
            $q  = "INSERT INTO props_commerce_log SET "
                . "  user_id = '" . $_SESSION['PROPS_USER']['user_id'] . "', "
                . "  ip_address = '" . props_get_ipaddress() . "', "
                . "  transaction_date = NOW(), "
                . "  description = '" . sql_escape_string($description) . "', "
                . "  amount = '$transaction_amount', "
                . "  reference_id = '" . sql_escape_string($result['reference_id']) . "'";
            sql_query($q);
        } else {
            $result['approved'] = FALSE;
        }

        $result['result_code'] = $response['RESULT'];
        $result['result_message'] = $response['RESPMSG'];

    } else {
        $result['approved'] = FALSE;
        $result['result_code'] = -999;
        $result['result_message'] = props_gettext("Could not establish connection to credit card authorization gateway.");
    }

    // Shut down PFPro library
    pfpro_cleanup();

    props_setkey('commerce.approved', $result['approved']);
    props_setkey('commerce.result_code', $result['result_code']);
    props_setkey('commerce.result_message', $result['result_message']);

    // print_r($transaction); print_r($response); exit;

    return $result;
}

/**
 * Validates a credit card number
 *
 * This function accepts a credit card number and, optionally, a code for
 * a credit card name. If a Name code is specified, the number is checked
 * against card-specific criteria, then validated with the Luhn Mod 10
 * formula. Otherwise it is only checked against the formula. Valid name
 * codes are:
 * - <b>mcd</b> - Master Card
 * - <b>vis</b> - Visa
 * - <b>amx</b> - American Express
 * - <b>dsc</b> - Discover
 * - <b>dnc</b> - Diners Club
 * - <b>jcb</b> - JCB
 *
 * This function is based upon code by Alan Little of Holotech Enterprises.
 * For more info, see:
 *
 * @link  http://www.beachnet.com/~hstiles/cardtype.html  Credit Card Validation
 * @link  http://www.zend.com/codex.php?id=80&single=1
 * @param   int     $Num   credit card number
 * @param   string  $Name  name code
 * @return  bool    TRUE on success, FALSE on failure
 */
function cc_validate($Num, $Name = 'n/a')
{
    //  Innocent until proven guilty
    $GoodCard = true;

    //  Get rid of any non-digits
    $Num = ereg_replace("[^[:digit:]]", "", $Num);

    //  Perform card-specific checks, if applicable
    switch ($Name) {

        case "mcd" :
            $GoodCard = ereg("^5[1-5].{14}$", $Num);
            break;

        case "vis" :
            $GoodCard = ereg("^4.{15}$|^4.{12}$", $Num);
            break;

        case "amx" :
            $GoodCard = ereg("^3[47].{13}$", $Num);
            break;

        case "dsc" :
            $GoodCard = ereg("^6011.{12}$", $Num);
            break;

        case "dnc" :
            $GoodCard = ereg("^30[0-5].{11}$|^3[68].{12}$", $Num);
            break;

        case "jcb" :
            $GoodCard = ereg("^3.{15}$|^2131|1800.{11}$", $Num);
            break;
    }

    // The Luhn formula works right to left, so reverse the number.
    $Num = strrev($Num);

    $Total = 0;

    for ($x=0; $x<strlen($Num); $x++) {
        $digit = substr($Num,$x,1);

        // If it's an odd digit, double it
        if ($x/2 != floor($x/2)) {
            $digit *= 2;

            // If the result is two digits, add them
            if (strlen($digit) == 2) {
                $digit = substr($digit,0,1) + substr($digit,1,1);
            }
        }

        // Add the current digit, doubled and added if applicable, to the Total
        $Total += $digit;
    }

    //  If it passed (or bypassed) the card-specific check and the Total is
    //  evenly divisible by 10, it's cool!
    if ($GoodCard && $Total % 10 == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Log transaction details
 *
 * @param  string  $type
 * @param  string  $username
 * @param  string  $plan_description
 * @param  float   $amount
 * @param  string  $reference_id
 */
function cc_log_transaction($description, $amount, $reference_id)
{
    $ip_address = $_SERVER["REMOTE_ADDR"];

    // Log this transaction
    $q  = "INSERT INTO props_commerce_transactions SET "
        . "  user_id = '" . $_SESSION['PROPS_USER']['user_id'] . "', "
        . "  ip_address = '" . props_get_ipaddress() . "', "
        . "  transaction_date = NOW(), "
        . "  description = '" . sql_escape_string($description) . "', "
        . "  amount = '$amount', "
        . "  reference_id = '" . sql_escape_string($reference_id) . "'";
    sql_query($q);
}

?>
