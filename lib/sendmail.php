<?php
/**
 * Lib - templates functions
 *
 * @package     api
 * @subpackage  templates
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
 * @version     $Id: sendmail.php,v 1.3 2007/08/27 07:56:18 roufneck Exp $
 */

// loadLibs
include_once(props_getkey('config.dir.libs') . 'phpmailer/class.phpmailer.php');

class props_sendmail extends PHPMailer {
    // Set default variables for all new objects
    var $WordWrap = 75;

    function props_sendmail() {

        // Set mailer. Can be smtp, sendmail, qmail or mail.
        switch (props_getkey('config.mail.mailer')) {
            case 'smtp':
                $this->IsSMTP();
                break;

            case 'sendmail':
                $this->IsSendmail();
                break;

            case 'qmail':
                $this->IsQmail();
                break;

            default:
                $this->IsMail();
                break;
        }

        if (props_getkey('config.mail.host')) {
            $this->Host = props_getkey('config.mail.host');
        }

        if (props_getkey('config.mail.port')) {
            $this->Port = props_getkey('config.mail.port');
        }

        if (props_getkey('config.mail.smtp.user')) {
            $this->SMTPAuth = TRUE;
            $this->Username = props_getkey('config.mail.smtp.user');
            $this->Password = props_getkey('config.mail.smtp.password');
        }

        if (props_getkey('config.mail.wordwrap')) {
            $this->WordWrap = props_getkey('config.mail.wordwrap');
        }
    }

    // Replace the default error_handler
    function error_handler($msg) {
        trigger_error('PROPS_MAILER: '.$msg, E_USER_ERROR);
        exit;
    }
}

?>
