<?php
/**
 * Lib - bulletins functions
 *
 * @package     api
 * @subpackage  bulletins
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
 * @version     $Id: bulletins.php,v 1.9 2007/09/17 15:01:04 roufneck Exp $
 */

/**
 * Sends bulletin content to a single email address
 *
 * At the moment this function is not needed. Just left it here in case...
 */
function bulletin_sendmail($email_recipient, $subject, $msg_plaintext, $msg_html)
{
    // Load the class
    props_loadLib('sendmail');

    // Assemble the email
    $mail = new props_sendmail();

    // Set headers and body text
    $mail->Sender       = props_getkey('config.email.bounce_address');
    $mail->From         = props_getkey('config.email.from_address');
    $mail->FromName     = props_getkey('config.email.from_name');
    $mail->AddAddress($email_recipient);
    $mail->AddCustomHeader('X-PROPS-Recipient: ' . $email_recipient);
    $mail->IsHTML(true);
    $mail->Subject      = $subject;

    if (!empty($msg_html)) {
        $mail->IsHTML(true);
        $mail->Body =   $msg_html;
        $mail->AltBody =$msg_plaintext;
    } else {
        $mail->Body =   $msg_plaintext;
    }

    $result = $mail->Send();
}

/**
 * Sends bulletin content to a single email address
 *
 * At the moment this function is not needed. Just left it here in case...
 */
function bulletin_sendmail_single($email_recipient, $subject, $msg_plaintext, $msg_html)
{
    // Code adapted from http://www.zend.com/zend/trick/html-email.php,
    // with bug fixes from the talkback messages at that URL

    // add From: header
    $headers  = "From: " . props_getkey('config.email.from_name') . " <" . props_getkey("bulletin_sender_email") . ">\n";

    // add various return-path and bounces-to headers
    $headers .= "Return-Path: " . props_getkey('config.email.bounce_address') . "\n";
    $headers .= "Errors-To: " . props_getkey('config.email.bounce_address') . "\n";
    $headers .= "X-Errors-To: " . props_getkey('config.email.bounce_address') . "\n";
    $headers .= "Return-Errors-To: " . props_getkey('config.email.bounce_address') . "\n";
    $headers .= "Bounces-To: " . props_getkey('config.email.bounce_address') . "\n";
    $headers .= "Bounces-to: " . props_getkey('config.email.bounce_address') . "\n";

    // specify MIME version 1.0
    $headers .= "MIME-Version: 1.0\n";

    // unique boundary
    $boundary = uniqid("PROPSBULLETIN");

    // tell e-mail client this e-mail contains alternate versions
    $headers .= "Content-Type: multipart/alternative; boundary = \"$boundary\"\n\n";

    // message to people with clients who don't
    // understand MIME
    $headers .= "This is a MIME encoded message.\n\n";

    // plain text version of message
    $headers .= "--$boundary\n" .
       "Content-Type: text/plain; charset=ISO-8859-1\n" .
       "Content-Transfer-Encoding: base64\n\n";
    $headers .= chunk_split(base64_encode($msg_plaintext));

    // HTML version of message
    $headers .= "--$boundary\n" .
       "Content-Type: text/html; charset=ISO-8859-1\n" .
       "Content-Transfer-Encoding: base64\n\n";
    $headers .= chunk_split(base64_encode($msg_html));

    //send message
    mail($email_recipient, $subject, "", $headers, "-f" . props_getkey('config.email.from_address'));
}

/**
 * Returns true if the user_id is subscribed to the bulletin_id
 *
 * At the moment this function is not needed. Just left it here in case...
 *
 * @return  bool  TRUE on success, FALSE on failure
 */
function bulletin_user_is_subscribed($user_id, $bulletin_id)
{
    $q  = "SELECT COUNT(*) AS sub_count ";
    $q .= "FROM props_bulletins_subscriptions ";
    $q .= "WHERE user_id = $user_id AND bulletin_id = $bulletin_id";
    $subscribed_query = sql_query($q);
    $srow = sql_fetch_object($subscribed_query);

    if ($srow->sub_count) {
        return TRUE;
    } else {
        return FALSE;
    }
}

?>
