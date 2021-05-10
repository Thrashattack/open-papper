<?php
/**
 * Lib - pfpro_compat functions
 *
 * This library provides drop-in replacement functions for those
 * which are available when PHP is compiled with --with-pfpro.
 * Because PHP's native pfpro extensions are not available on
 * shared hosting servers or Windows, and are difficult to compile
 * into Apache, this should make it easier to get paid archives
 * working.
 *
 * Note that this lib is slightly less secure than PHP's pfpro
 * functions, because it submits transactions using command-line
 * system() calls, and the parameters of those can be viewed using
 * the 'ps' command.  Ultimately, we really should move away from
 * PayFlow Pro toward something which is better supported in PHP.
 *
 * IMPORTANT: Before you use this file, you must set the following
 * environment variable, or this script will not work:
 *
 * <code>
 * PFPRO_CERT_PATH = C:\Verisign\payflowpro\win32\certs // or whatever your path is.
 * </code>
 *
 * Original code was written by Jason Caldwell
 * <jason -at- thinkingman -dot- org> and is available at
 * ftp://ftp.thinkingman.org/pub/php/php_pfpro.zip
 *
 * Jason agreed to allow us to release it under the GPL as part of
 * PROPS.  Thanks Jason!
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
 * @version     $Id: pfpro_compat.php,v 1.4 2007/10/26 08:23:06 roufneck Exp $
 *
 * @todo        This lib could really use some formatting cleanup... and could
 *              be better integrated with PROPS. Any volunteers?
 */

/**
 * Set constants
 */
define('PFPRO_DEFAULTHOST',    props_getkey('config.pfpro.hostname'));
define('PFPRO_DEFAULTPORT',    443);
define('PFPRO_DEFAULTTIMEOUT', 30);
define('PFPRO_PROXYADDRESS',   NULL);
define('PFPRO_PROXYPORT',      NULL);
define('PFPRO_PROXYLOGIN',     NULL);
define('PFPRO_PROXYPASSWORD',  NULL);
define('PFPRO_EXE_PATH',       props_getkey('config.pfpro.path'));

/**
 * This function is here for compatibility only.
 * @return  string  NULL
 */
function pfpro_init()
{
    return NULL;
}

/**
 * This function is here for compatibility only.
 * @return  string  NULL
 */
function pfpro_cleanup()
{
    return NULL;
}

/**
 * Returns the version of the Payflow Pro software.
 *
 * pfpro_version() normally returns the version string of the PayFlowPro
 * Library. However, the function in this INCLUDE file returns the version
 * of the *executable*.
 *
 * @return  string  version of the Payflow Pro library
 */
function pfpro_version()
{
    @exec(PFPRO_EXE_PATH, $result);
    $version = substr($result[0], strlen($result[0])-4, 4);

    return $version;
}

/**
 * Processes a transaction with Payflow Pro.
 * @param   array   $transaction     An associative array containing keys and values
 *                                   that will be encoded and passed to the processor
 * @param   string  $url             host to connect to
 * @param   int     $port            port to connect on
 * @param   int     $timeout         timeout to be used, in seconds
 * @param   string  $proxy_url       SSL proxy hostname
 * @param   int     $proxy_port      SSL proxy port
 * @param   string  $proxy_logon     SSL proxy logon identity
 * @param   string  $proxy_password  SSL proxy logon password
 * @return  array   An associative array of the keys and values in the response.
 */
function pfpro_process($transaction, $url=PFPRO_DEFAULTHOST, $port=PFPRO_DEFAULTPORT, $timeout=PFPRO_DEFAULTTIMEOUT,
                              $proxy_url=PFPRO_PROXYADDRESS, $proxy_port=PFPRO_PROXYPORT, $proxy_logon=PFPRO_PROXYLOGIN,
                              $proxy_password=PFPRO_PROXYPASSWORD)
{
    if(!(is_array($transaction))) {
        return NULL;
    }

    /* destruct (transaction) array into (trans) string
        and dynamically add LENGTH TAGS */
    foreach($transaction as $val1=>$val2) {
        $parmsArray[] = $val1 . '[' . strlen($val2) . ']=' . $val2;
    }
    $parmsString = implode($parmsArray, '&');

    $trans  = "export PFPRO_CERT_PATH=" . props_getkey('config.pfpro.certs') . '; ';
    $trans .= PFPRO_EXE_PATH . ' ';
    $trans .= $url . ' ';
    $trans .= $port . ' "';
    $trans .= $parmsString . '" ';
    $trans .= $timeout . ' ';
    $trans .= $proxy_url . ' ';
    $trans .= $proxy_port . ' ';
    $trans .= $proxy_logon . ' ';
    $trans .= $proxy_password;

    /* run transaction, if result blank, return(NULL) */
    @exec($trans, $result);
    if ($result[0] == NULL) {
        return NULL;
    }

    /* replace any '&' that are surrounded by spaces -- this assumes
        the '&' isn't a delimiter, but instead part of a message string
        and converting it to 'ASCII(38)' will prevent the explode function from
        thinking it's actually a delimiter. */
    $result[0] = str_replace(' & ', ' ASCII(38) ', $result[0]);

    /* construct (pfpro) array out of (result) string */
    $valArray = explode('&', $result[0]);

    foreach ($valArray as $val) {
        $valArray2 = explode('=', $val);
        $pfpro[$valArray2[0]] = str_replace('ASCII(38)', '&', $valArray2[1]);
    }

    return $pfpro;
}

/**
 * Receives a string, processes it, and then returns a results string.
 *
 * This functionality is NOT part of the standard pfpro functions as defined in
 * the PHP manual. I've added this functionality simply because I think it
 * should be here.
 *
 * Use autoLenTags = 1 to enable auto length tags, or 0 (zero) for no length
 * tags. The default is 1.
 *
 * Example:
 * <code>
 * // will default to 1 if not specified
 * pfpro_process_raw(' ... transaction string ... ');
 * // will process the string without length tags
 * pfpro_process_raw(' ... transaction string ... ', 0);
 * </code>
 *
 * @param   array   $transaction     An associative array containing keys and values
 *                                   that will be encoded and passed to the processor
 * @param   string  $url             host to connect to
 * @param   int     $port            port to connect on
 * @param   int     $timeout         timeout to be used, in seconds
 * @param   string  $proxy_url       SSL proxy hostname
 * @param   int     $proxy_port      SSL proxy port
 * @param   string  $proxy_logon     SSL proxy logon identity
 * @param   string  $proxy_password  SSL proxy logon password
 * @return  array   An associative array of the keys and values in the response.
 */
function pfpro_process_raw($transaction, $autoLenTags=1, $url=PFPRO_DEFAULTHOST, $port=PFPRO_DEFAULTPORT, $timeout=PFPRO_DEFAULTTIMEOUT,
                                    $proxy_url=PFPRO_PROXYADDRESS, $proxy_port=PFPRO_PROXYPORT, $proxy_logon=PFPRO_PROXYLOGIN,
                                    $proxy_password=PFPRO_PROXYPASSWORD)
{
    if(!(is_string($transaction))) {
        return NULL;
    }

    /* Check to see if autoLenTags is enabled */
    if($autoLenTags) {
        $transaction = str_replace(' & ', ' ASCII(38) ', $transaction);
        $transArray = explode('&', $transaction);

        foreach($transArray as $val) {
            list($val1[], $val2[]) = split('=', $val, 2);
        }

        $cnt = count($transArray);
        for($x=0; $x<$cnt; $x++) {
            $val2[$x] = str_replace('ASCII(38)', '&', $val2[$x]);
            $a[] = $val1[$x] . '[' . strlen($val2[$x]) . ']=' . $val2[$x];
        }

        $transaction = implode('&', $a);
    }

    $trans  = "export PFPRO_CERT_PATH=" . props_getkey('config.pfpro.certs') . '; '
            . PFPRO_EXE_PATH . ' '
            . $url . ' '
            . $port . ' "'
            . $transaction . '" '
            . $timeout . ' '
            . $proxy_url . ' '
            . $proxy_port . ' '
            . $proxy_logon . ' '
            . $proxy_password;

    /* run transaction, if result blank, return(NULL) */
    @exec($trans, $result);
    if($result[0] == NULL) {
        return NULL;
    }

    return $result[0];
}

?>
