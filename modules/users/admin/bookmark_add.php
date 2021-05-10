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
 * @version     $Id: bookmark_add.php,v 1.1 2007/10/19 16:24:50 roufneck Exp $
 */

/**
 * @admintitle  Add bookmark
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_bookmark_add()
{
    // Get the needed posted vars here.
    $bookmark_name = props_getrequest('bookmark_name', VALIDATE_TEXT, '!EMPTY');
    $bookmark_url = $_SESSION['LAST_URL'];

    if (empty($bookmark_name)) {
        props_error("Bookmark name may not be empty.");
    }

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Add bookmark"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                if (sql_num_rows(sql_query("SELECT bookmark_name FROM props_users_bookmarks WHERE bookmark_name = '".sql_escape_string($bookmark_name)."' AND user_id = ".$_SESSION['PROPS_USER']['user_id']))) {
                    $GLOBALS['PROPS_ERRORSTACK']['bookmark_name']['message'] = props_gettext("This bookmark name already exists.");
                }

                if (sql_num_rows(sql_query("SELECT bookmark_url FROM props_users_bookmarks WHERE bookmark_url = '".sql_escape_string($bookmark_url)."' AND user_id = ".$_SESSION['PROPS_USER']['user_id']))) {
                    $GLOBALS['PROPS_ERRORSTACK']['bookmark_url']['message'] = props_gettext("This bookmark already exists.");
                }

                // Catch errors
                if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
                    break;
                }

                // Assemble SQL. Use sql_escape_string for all vars except integers to prevent DB hacking.
                $q  = "INSERT INTO props_users_bookmarks SET "
                    . "  user_id = " . $_SESSION['PROPS_USER']['user_id'] . ", "
                    . "  bookmark_name = '" . sql_escape_string($bookmark_name) . "', "
                    . "  bookmark_url = '" . sql_escape_string($bookmark_url) . "'";
                sql_query($q);

                props_error("Bookmark added.");
            }
            break;
    } // END switch

    $GLOBALS['PROPS_FUNCTION_INFO'] =
         '<h2>' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']) . '</h2>'.LF
        .'<p>' . sprintf(props_gettext("The bookmark system is very flexible. You can create bookmarks from every non posted page, the [+] will indicate that a screen is available for bookmarking. Bookmarks can be added by clicking on [+] after '%s'. You can delete bookmarks at your %s."), props_gettext("Bookmarks"), '<a href="./?module=users&amp;function=preferences">' . strtolower(props_gettext('.users.preferences')) . '</a>') . '</p>'.LF;

    $output = '<p style="text-align: center;"><a href="javascript:history.go(-1)">' . props_gettext("Go back") . '</a></p>';

    return $output;
}

?>
