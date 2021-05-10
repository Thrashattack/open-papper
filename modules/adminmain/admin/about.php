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
 * @version     $Id: about.php,v 1.9 2007/11/02 16:53:49 roufneck Exp $
 */

/**
 * @admintitle  About
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_about()
{
    props_loadLib('media');

    // Set sitebar
    admin_sidebar_add('users', 'preferences');

    $output =
         '<p style="text-align: center; margin: 0 auto;"><img src="./images/props_logo.png" alt="PROPS logo" /></p>'.LF
        .'<br />'.LF
        .'<fieldset style="width: 500px; margin: 0 auto;">'.LF
        .'<legend>PROPS publishing system v' . PROPS_VERSION . '</legend>'.LF
        .'<p style="text-align: justify;">' . props_gettext("PROPS is a multi language extensible Internet publishing system, designed specifically for periodicals such as newspapers and magazines who want to publish online, either exclusively or as an extension of their print publication.") . '</p>'.LF

        .'<p>' . props_gettext("Features") . ':</p>'.LF
        .'<ul>'.LF
        .'  <li>'. props_gettext("Plug-in API allowing modular extension of base functionality.") . '</li>'.LF
        .'  <li>'. props_gettext("Delivery of content to multiple target platforms (HTML, XHTML, XML/XSL, WAP/WML, text, etc).") . '</li>'.LF
        .'  <li>'. props_gettext("Content access management: free access, registered access and paid access (subscriptions).") . '</li>'.LF
        .'  <li>'. props_gettext("Workflow management.") . '</li>'.LF
        .'  <li>'. props_gettext("Extendable database abstraction layer.") . '</li>'.LF
        .'  <li>'. props_gettext("Powerfull tag-based templating system, which gives strict separation of design and content.") . '</li>'.LF
        .'  <li>'. sprintf(props_gettext("Media handling '%s'."), media_supported_types()) . '</li>'.LF
        .'  <li>'. props_gettext("Permissions-based multiuser and group management system.") . '</li>'.LF
        .'  <li>'. props_gettext("Mailinglists and subscriptions.") . '</li>'.LF
        .'</ul>'.LF

        .'<p>' . props_gettext("Developers") . ': Derrick Miller, Blake Girardot, Geert Eltink.</p>'.LF
        .'<p>PROPS &copy; 2001-2007 props publishing systems</p>'.LF
        .'<ul class="hlist">'.LF
        .'  <li class="begin"><a target="_blank" href="http://props.sourceforge.net/">PROPS</a></li>'.LF
        .'  <li>-&nbsp;<a target="_blank" href="http://sourceforge.net/forum/forum.php?forum_id=93029">' . props_gettext("Forums") . '</a></li>'.LF
        .'  <li>-&nbsp;<a target="_blank" href="http://sourceforge.net/tracker/?group_id=29581&amp;atid=396656">' . props_gettext("Bug report") . '</a></li>'.LF
        .'  <li>-&nbsp;<a target="_blank" href="http://sourceforge.net/tracker/?group_id=29581&amp;atid=396659">' . props_gettext("Support request") . '</a></li>'.LF
        .'  <li>-&nbsp;<a target="_blank" href="http://sourceforge.net/tracker/?group_id=29581&amp;atid=396659">' . props_gettext("Feature requests") . '</a></li>'.LF
        .'</ul>'.LF
        .'</fieldset>'.LF
        .'<br />'.LF;

    return $output;
}

?>
