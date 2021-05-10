<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  admincontent
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
 * @version     $Id: edition_publish.php,v 1.14 2007/12/11 15:46:28 roufneck Exp $
 */

set_time_limit(0);

// loadLibs
props_loadLib('editions');

/**
 * @admintitle  Publish edition
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_edition_publish()
{
    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'editions_manage');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'edition_add');

    // Get the needed posted vars here.
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    $publish_date = props_getrequest('publish_date', VALIDATE_DATE, '!EMPTY');

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Publish"):

            // Find out the edition_id of the current (retiring) edition
            $retiring_edition = edition_current_id(FALSE);

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // Make sure the supplied date is greater than that of the current live edition
            $this_edition_timestamp = strtotime($publish_date);

            if ($retiring_edition) {
                $current_edition_timestamp = edition_date($retiring_edition);
                if ($current_edition_timestamp >= $this_edition_timestamp) {
                    $GLOBALS['PROPS_ERRORSTACK']['publish_date']['message'] = props_gettext("You must specify a date which is more recent than that of the current live edition.");
                }
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // Mark all staged stories in this edition as published
                $q  = "UPDATE props_stories SET publication_status_id = " . PUBSTATUS_PUBLISHED . ", "
                    . "published_stamp = '$publish_date' "
                    . "WHERE edition_id = $edition_id "
                    . "AND publication_status_id = " . PUBSTATUS_STAGED;
                sql_query($q);

                //  Mark the new edition as published
                $q  = "UPDATE props_editions SET "
                    . "publish_date = '$publish_date' "
                    . "WHERE edition_id = $edition_id ";
                sql_query($q);

                if ($retiring_edition) {
                    // Mark all stories in the retiring edition as archived. I wish MySQL had subselects.
                    $q  = "UPDATE props_stories SET publication_status_id = " . PUBSTATUS_ARCHIVED . " "
                        . "WHERE edition_id = $retiring_edition";
                    sql_query($q);

                    //  Mark the retiring edition as closed
                    $q  = "UPDATE props_editions SET closed = 1 WHERE edition_id = $retiring_edition";
                    sql_query($q);

                    // Auto-archive stories in sections where auto-archive is enabled
                    props_loadLib('sections');
                    sections_auto_archive();
                }

                // Redirect
                props_redirect(TRUE);
            }
            break;

        default:
            if (empty($publish_date)) $publish_date = date('Y-m-d');
            break;

    } // END switch

    // Set to empty to prevent problems with javascript
    if (substr($publish_date, 0, 10) == '0000-00-00') $publish_date = '';

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
        .'<input name="edition_id" type="hidden" value="' . $edition_id . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Settings") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Publish date") . '</label></dt>'.LF
        .'    <dd><input class="medium" type="text" id="publish_date" name="publish_date" value="' . htmlspecialchars($publish_date) . '" />'.LF
        .'      <img src="./images/button_calendar.png" style="cursor: pointer;" alt="Date selector" title="Date selector" onclick="displayCalendar(document.getElementById(\'publish_date\'),\'yyyy-mm-dd\',this)" /></dd>'.LF
        .((props_geterror('publish_date')) ? '      <dd>' . props_geterror('publish_date') . '</dd>'.LF : '')
        .'    </dl>'.LF
        .'  <p>'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Publish") . '" />'.LF
        .'  </p>'.LF
        .'</fieldset>'.LF
        .'</form>'.LF;

    return $output;
}

?>
