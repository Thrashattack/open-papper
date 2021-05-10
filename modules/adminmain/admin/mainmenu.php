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
 * @version     $Id: mainmenu.php,v 1.19 2007/12/27 08:25:47 roufneck Exp $
 */

/**
 * @admintitle  Main menu
 * @adminprivs  site_status   View site status
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_mainmenu()
{
    props_loadLib('system');

    // Set sitebar
    admin_sidebar_add('users', 'preferences');
    admin_sidebar_add('system', 'update');

    // Set referer
    props_redirect(FALSE, 'set');

    if (props_getrequest('cookielogin')) {
        props_error("You are automatically logged in. If you don't want this, then please use the logout option and login again without the 'Remember me' box checked.");
    }

    $GLOBALS['PROPS_FUNCTION_INFO'] =
         '<h2>' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']) . '</h2>'.LF
        .'<p>' . props_gettext("Overview of system statistics.") . '</p>'.LF
        .'<h2>' . props_gettext("Getting started") . '</h2>'.LF
        .'<p>' . sprintf(props_gettext("With a fresh installation, the first thing you need to do is updating %s. This will enable access to all admin functions."), '<a href="./?module=system&amp;function=update">' . props_gettext("template tags and user privs") . '</a>') . '</p>'.LF
        .'<p>' . sprintf(props_gettext("Before the frontpage can display anything, you first need to %s, %s to it and %s it."), '<a href="./?module=admincontent&amp;function=edition_add">' . props_gettext("add an edition") . '</a>', '<a href="./?module=admincontent&amp;function=story_add">' . props_gettext("add content") . '</a>', '<a href="./?module=admincontent&amp;function=editions_manage">' . props_gettext("publish") . '</a>') . '</p>'.LF
        .'<h2>' . props_gettext("Language") . '</h2>'.LF
        .'<p>' . sprintf(props_gettext("Changing the language can be done at your %s panel."), '<a href="./?module=users&amp;function=preferences">' . strtolower(props_gettext('.users.preferences')) . '</a>') . '</p>'.LF
        .'<h2>' . props_gettext("Bookmarks") . '</h2>'.LF
        .'<p>' . sprintf(props_gettext("The bookmark system is very flexible. You can create bookmarks from every non posted page, the [+] will indicate that a screen is available for bookmarking. Bookmarks can be added by clicking on [+] after '%s' in the sidebar. You can delete bookmarks at your %s panel."), props_gettext("Bookmarks"), '<a href="./?module=users&amp;function=preferences">' . strtolower(props_gettext('.users.preferences')) . '</a>') . '</p>'.LF;

    static $sum_tblsize, $sum_rows, $sum_overhead;

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

        if ($row->Name == 'props_editions') {
            $sum_editions = $row->Rows;
        } elseif ($row->Name == 'props_stories') {
            $sum_stories = $row->Rows . ' (' . $formatted_size . ' ' . $unit . ')';
        } elseif ($row->Name == 'props_media') {
            $sum_media = $row->Rows;
        } elseif ($row->Name == 'props_users') {
            $sum_users = $row->Rows;
        }
    }

    list($formatted_tblsize, $tblsize_unit) = props_formatByteDown($sum_tblsize, 3, ($tblsize > 0) ? 1 : 0);

    $sum_editions = (admin_has_priv('admincontent', 'editions_manage')) ? '<a href="./?module=admincontent&amp;function=editions_manage">'.$sum_editions.'</a>': $sum_editions;
    $sum_stories = (admin_has_priv('admincontent', 'storysearch')) ? '<a href="./?module=admincontent&amp;function=storysearch&amp;op=search&amp;include%5B0%5D=headline">'.$sum_stories.'</a>': $sum_stories;
    $sum_media = (admin_has_priv('media', 'media_search')) ? '<a href="./?module=media&amp;function=media_search&amp;op=search&amp;status=1&amp;group%5B%5D=1&amp;group%5B%5D=2&amp;group%5B%5D=3&amp;search_string=">'.$sum_media.'</a>': $sum_media;
    $sum_users = (admin_has_priv('users', 'users_manage')) ? '<a href="./?module=users&amp;function=users_manage">'.$sum_users.'</a>': $sum_users;

    $output =
         '<fieldset>'.LF
        .'  <legend>' . props_gettext("Statistics") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Server") . '</label></dt>'.LF
        .'    <dd>' . $_SERVER['SERVER_SOFTWARE'] . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>PROPS ' . props_gettext("version") . '</label></dt>'.LF
        .'    <dd>' . PROPS_VERSION . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Database") . '</label><br />' . props_gettext("Total size") . '</dt>'.LF
        .'    <dd>' . props_getkey('config.db.type') . ' ' . sql_server_version() . ' ['.props_getkey('db.charset').']</dd>'.LF
        .'    <dd>' . $formatted_tblsize . ' ' . $tblsize_unit . '</dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Total editions") . '</label></dt>'.LF
        .'    <dd>' . $sum_editions . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Total stories") . '</label></dt>'.LF
        .'    <dd>' . $sum_stories . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Total media files") . '</label></dt>'.LF
        .'    <dd>' . $sum_media . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Total users") . '</label></dt>'.LF
        .'    <dd>' . $sum_users . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Current Edition") . '</label><br />' . props_gettext("This is the current live edition.") . '</dt>'.LF;

    $q  = "SELECT t1.*, COUNT(t2.story_id) AS story_count FROM props_editions AS t1 "
        . "LEFT JOIN props_stories AS t2 "
        . "ON t1.edition_id = t2.edition_id "
        . "WHERE !ISNULL(t1.publish_date) "
        . "GROUP BY t1.edition_id "
        . "ORDER BY t1.publish_date desc LIMIT 1";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        $output .= '    <dd>'.props_gettext("There are no published editions.").'</dd>'.LF;
    } else {
        $row = sql_fetch_object($result);
        $output .=
             '    <dd><strong>#' . $row->edition_id . ' ' . htmlentities($row->label) . '</strong> (' . strftime(props_getkey('config.date.format'), strtotime($row->publish_date)) . ')</dd>'.LF
            .'    <dd>&nbsp;&nbsp;'.LF;
        if (admin_has_priv('admincontent', 'storysearch')) {
            $output .= '<a href="./?module=admincontent&amp;function=storysearch&amp;include[]=edition_id&amp;edition_id=' . $row->edition_id . '&amp;op=search" title="' . props_gettext("View stories") . '">[' . props_gettext("Stories") . ': ' . $row->story_count . ']</a>&nbsp;&nbsp;'.LF;
        }
        if (admin_has_priv('admincontent', 'story_add')) {
            $output .= '<a href="./?module=admincontent&amp;function=story_add&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Add story") . '">[' . props_gettext("Add story") . ']</a>&nbsp;&nbsp;'.LF;
        }
        if (admin_has_priv('admincontent', 'edition_order')) {
            $output .= '<a href="./?module=admincontent&amp;function=edition_order&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Order") . '">[' . props_gettext("Order") . ']</a>&nbsp;&nbsp;'.LF;
        }

        $output .= '    </dd>'.LF;
    }

    $output .=
         '  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Staging editions") . '</label><br />' . props_gettext("These editions are under construction.") . '</dt>'.LF;

    $q  = "SELECT t1.*, COUNT(t2.story_id) AS story_count FROM props_editions AS t1 "
        . "LEFT JOIN props_stories AS t2 "
        . "ON t1.edition_id = t2.edition_id "
        . "WHERE ISNULL(t1.publish_date) "
        . "GROUP BY t1.edition_id ";
    $result = sql_query($q);

    if (!sql_num_rows($result)) {
        $output .= '    <dd>'.props_gettext("There are no staging editions.").'</dd>'.LF;
    } else {
        while ($row = sql_fetch_object($result)) {
            $output .=
                 '    <dd><strong>#' . $row->edition_id . ' ' . htmlentities($row->label) . '</strong></dd>'.LF
                .'    <dd>&nbsp;&nbsp;'.LF;
            if (admin_has_priv('admincontent', 'storysearch')) {
                $output .= '<a href="./?module=admincontent&amp;function=storysearch&amp;include[]=edition_id&amp;edition_id=' . $row->edition_id . '&amp;op=search" title="' . props_gettext("View stories") . '">[' . props_gettext("Stories") . ': ' . $row->story_count . ']</a>&nbsp;&nbsp;'.LF;
            }
            if (admin_has_priv('admincontent', 'story_add')) {
                $output .= '<a href="./?module=admincontent&amp;function=story_add&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Add story") . '">[' . props_gettext("Add story") . ']</a>&nbsp;&nbsp;'.LF;
            }
            if (admin_has_priv('admincontent', 'edition_order')) {
                $output .= '<a href="./?module=admincontent&amp;function=edition_order&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Order") . '">[' . props_gettext("Order") . ']</a>&nbsp;&nbsp;'.LF;
            }
            if (admin_has_priv('admincontent', 'edition_preview')) {
                $output .= '<a href="../?edition_id=' . $row->edition_id . '&amp;preview=' . TRUE . '" title="' . props_gettext("Preview") . '">[' . props_gettext("Preview") . ']</a>&nbsp;&nbsp;'.LF;
            }
            if (admin_has_priv('admincontent', 'edition_publish')) {
                $output .= '<a href="./?module=admincontent&amp;function=edition_publish&amp;edition_id=' . $row->edition_id . '" title="' . props_gettext("Publish") . '">[' . props_gettext("Publish") . ']</a>&nbsp;&nbsp;'.LF;
            }
            $output .= '    </dd>'.LF;
        }
    }

    $output .=
         '  </dl>'.LF
        .'<form action="./" method="post">'.LF
        .'  <input name="module" type="hidden" value="system" />'.LF
        .'  <input name="function" type="hidden" value="update" />'.LF;

    if (admin_has_priv('system', 'update_tags')) {
        $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext('.system.update_tags') . '</label><br />' . props_gettext("Tags are used in the templates.") . '</dt>'.LF
        .'    <dd><input class="button" name="op" type="submit" value="' . props_gettext('.system.update_tags') . '" /></dd>'.LF
        .'  </dl>'.LF;
    }

    if (admin_has_priv('system', 'update_privs')) {
        $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext('.system.update_privs') . '</label><br />' . props_gettext("Update all user privs (frontpage and admin control panel).") . '</dt>'.LF
        .'    <dd><input class="button" name="op" type="submit" value="' . props_gettext('.system.update_privs') . '" /></dd>'.LF
        .'  </dl>'.LF;
    }

    $output .=
         '</form>'.LF
        .'</fieldset>'.LF;

    return $output;
}

?>
