<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  users
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
 * @version     $Id: users_search.php,v 1.2 2008/01/08 14:20:54 roufneck Exp $
 */

/**
 * @admintitle  Users search
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_users_search()
{
    // Get the needed posted vars here.
    $search_string = props_getrequest('search_string', VALIDATE_TEXT);
    $username = props_getrequest('username', VALIDATE_TEXT);
    $group_id = props_getrequest('group_id', VALIDATE_INT);
    $position = props_getrequest('position', VALIDATE_INT);

    $output =
         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.LF
        .'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en">'.LF
        .'<head>'.LF
        .'  <title>PROPS Admin - ' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']) . '</title>'.LF
        .'  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.LF
        .'  <meta http-equiv="Content-Language" content="en" />'.LF
        .'  <meta name="robots" content="noindex,nofollow" />'.LF
        .'  <meta name="keywords" content="PROPS - Open Source News Publishing Platform" />'.LF
        .'  <link rel="shortcut icon" href="favicon.ico" />'.LF
        .'  <link rel="stylesheet" type="text/css" media="all" href="props.admin.css" />'.LF
        .'  <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="props.iefix.css" /><!{endif]-->'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'props.admin.js"></script>'.LF
        .'</head>'.LF
        .'<body>'.LF
        .'<br />'.LF
        .'<form action="./" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<p>'
        .'  <input name="search_string" type="text" value="' . htmlspecialchars($search_string) . '" />' . props_geterror('search_string')
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Search") . '" />'.LF
        .'</p>'.LF
        .'</form>'.LF
        .'<p>'.LF
        .'<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;username=all">'.props_gettext("All").'</a>'.LF;

    // Construct alphabet list
    $search_array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    foreach ($search_array as $key => $value) {
        $output .=
             '&nbsp;<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;username='.$value.'">'.ucfirst($value).'</a>'.LF;
    }

    $output .=
         '&nbsp;<a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '">#</a>'.LF
        .'</p>'.LF;

    // Construct query
    $q  = "SELECT * FROM props_users ";

    if ($search_string) {
        // Search in username and email_address
        $q .= "WHERE username LIKE '%%" . sql_escape_string($search_string) . "%%' "
            . "OR email_address LIKE '%%" . sql_escape_string($search_string) . "%%' ";
    } elseif ($username && $username != 'all') {
        $q .= "WHERE username LIKE '" . sql_escape_string($username) . "%' ";
    } else {
        $q .= "WHERE 1 = 1 ";
    }

    // Search for group members
    if ($group_id) {
        $q .= "AND group_id = $group_id ";
    }

    if ($search_string || $username || $group_id) {
        $q .= "ORDER BY username ASC ";
    } else {
        // Show last 25 registered useres by default
        $q .= "ORDER BY registered DESC ";
    }

    // Get total results
    $result = sql_query($q);
    $result_rows = sql_num_rows($result);
    if (!$position) {
        $position = 0;
    }

    // Construct page navigation
    $pagination = props_pagination($result_rows, $position, 25, './?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;search_string='.$search_string.'&amp;username='.$username.'&amp;group_id='.$group_id);

    // Get results for this page
    $q .= "LIMIT $position, 25";
    $result = sql_query($q);

    $output .=
         '<h1>'.props_gettext("Users").'</h1>'.LF
        .'<p>'.sprintf(props_gettext("%s results found"), $result_rows).' - '.$pagination.'</p>'.LF
        .'<table>'.LF
        .'  <tr>'.LF
        .'    <th>' . props_gettext("Username") . '</th>'.LF
        .'    <th>' . props_gettext("Type") . '</th>'.LF
        .'    <th>' . props_gettext("Email address") . '</th>'.LF
        .'    <th>' . props_gettext("Joined") . '</th>'.LF
        .'    <th>' . props_gettext("Last active") . '</th>'.LF
        .'  </tr>'.LF;

    if (!$result_rows) {
        $output .= '  <tr class="row1"><td colspan="5" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
    } else {
        $row_num = 1;
        while ($row = sql_fetch_object($result)) {

            switch ($row->user_type) {
                case PROPS_USERTYPE_FOUNDER: $usertype = '<span class="usertype_founder">'.props_gettext("Founder").'</span>'; break;
                case PROPS_USERTYPE_ADMIN: $usertype = '<span class="usertype_admin">'.props_gettext("Administrator").'</span>'; break;
                case PROPS_USERTYPE_USER: $usertype = '<span class="usertype_user">'.props_gettext("User").'</span>'; break;
                case PROPS_USERTYPE_GUEST: $usertype = '<span class="usertype_guest">'.props_gettext("Guest").'</span>'; break;
                case PROPS_USERTYPE_BOT: $usertype = '<span class="usertype_bot">'.props_gettext("Bot").'</span>'; break;
                case PROPS_USERTYPE_CLOSED: $usertype = '<span class="usertype_closed">'.props_gettext("Closed").'</span>'; break;
                default: $usertype = ''; break;
            }

            $registered = ($row->registered) ? strftime('%x %X', strtotime($row->registered)) : '&nbsp;';
            $last_login = ($row->last_login) ? strftime('%x %X', strtotime($row->last_login)) : '&nbsp;';

            $output .=
                 '  <tr class="row'.$row_num.'">'.LF
                .'    <td><a href="#" onclick="javascript:selectUser(\'' . $row->user_id . '\', \'' . htmlspecialchars($row->username) . '\');">' . htmlspecialchars($row->username) . '</a></td>'.LF
                .'    <td style="text-align: center;">' . $usertype . '</td>'.LF
                .'    <td style="text-align: center;">' . htmlspecialchars($row->email_address) . '</td>'.LF
                .'    <td style="text-align: center;">' . $registered . '</td>'.LF
                .'    <td style="text-align: center;">' . $last_login . '</td>'.LF
                .'  </tr>'.LF;

            // Update row number
            $row_num = ($row_num == 1) ? 2 : 1;
        }
    }

    $output .=
         '</table>'.LF
        .'</body>'.LF
        .'</html>'.LF;

    echo $output;
    exit;
}

?>
