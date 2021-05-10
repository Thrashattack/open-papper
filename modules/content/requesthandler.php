<?php
/**
 * Module functions
 *
 * @package     modules
 * @subpackage  content
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
 * @version     $Id: requesthandler.php,v 1.3 2007/11/14 11:19:03 roufneck Exp $
 *
 * @userprivs   submit  Submit content
 */

// Trap 'command' request parameter
switch (props_getkey('request.cmd')) {

    case 'content-submit':
        rh_content_submit();
        break;
}

/**
 * Handles POST/GET cmd=content-submit
 */
function rh_content_submit()
{
    // Check form submission with pageID.
    if (!isset($_POST['pageID'])) {
        // No form post, do nothing
        return;
    } elseif ($_POST['pageID'] != $_SESSION['pageID']) {
        // Check pageID. An extra check against URL hacking.
        props_error("Invalid page referer. Please submit this form again.");
        return;
    }

    $onsuccess = props_getrequest('onsuccess', VALIDATE_TEXT);
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    $headline = props_getrequest('headline', VALIDATE_TEXT, '!EMPTY');
    $subhead = props_getrequest('subhead', VALIDATE_TEXT);
    $byline_name = props_getrequest('byline_name', VALIDATE_TEXT);
    $byline_prefix = props_getrequest('byline_prefix', VALIDATE_TEXT);
    $byline_suffix = props_getrequest('byline_suffix', VALIDATE_TEXT);
    $body_content = props_getrequest('body_content', VALIDATE_HTML, '!EMPTY');
    $end_content = props_getrequest('end_content', VALIDATE_TEXT);
    $abstract = props_getrequest('abstract', VALIDATE_TEXT);
    $copyright = props_getrequest('copyright', VALIDATE_TEXT);
    $source_url = props_getrequest('source_url', VALIDATE_TEXT);
    $source_desc = props_getrequest('source_desc', VALIDATE_TEXT);

    // If errors, display errors
    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        return;
    }

    props_loadLib('editions');

    // Assemble SQL
    $q  = "INSERT INTO props_stories SET ";

    $publication_status_id = PUBSTATUS_INEDITQUEUE;
    // Assign to edition if user is privileged and an edition was selected
    if (user_has_priv('content', 'assign_to_edition') && $edition_id) {
        // Set pub status of story to published if current live edition
        if ($edition_id == edition_current_id(FALSE)) {
            $publication_status_id = PUBSTATUS_PUBLISHED;
            // Set pubdate
            $q .= "published_stamp = NOW(), ";
        } else {
            $publication_status_id = PUBSTATUS_STAGED;
        }

        $q .= "edition_id = $edition_id, ";
    }

    $q .= "created_stamp = NOW(), "
        . "modified_stamp = NOW(), "
        . "publication_status_id = $publication_status_id, "
        . "created_by = '" . sql_escape_string($_SESSION['PROPS_USER']['user_id']) . "', "
        . "modified_by = '" . sql_escape_string($_SESSION['PROPS_USER']['user_id']) . "', "
        . "section_id = '$section_id', "
        . "revision = 1, "
        . "revision_description = 'Original revision', "
        . "origination = 'User submitted web entry', "
        . "headline = '" . sql_escape_string($headline) . "', "
        . "subhead = '" . sql_escape_string($subhead) . "', "
        . "byline_prefix = '" . sql_escape_string($byline_prefix) . "', "
        . "byline_name = '" . sql_escape_string($byline_name) . "', "
        . "byline_suffix = '" . sql_escape_string($byline_suffix) . "', "
        . "body_content = '" . sql_escape_string($body_content) . "', "
        . "end_content = '" . sql_escape_string($end_content) . "', "
        . "abstract = '" . sql_escape_string($abstract) . "', "
        . "copyright = '" . sql_escape_string($copyright) . "', "
        . "source_url = '" . sql_escape_string($source_url) . "', "
        . "source_desc = '" . sql_escape_string($source_desc) . "', "
        . "approved = 0";
    $story_id = sql_identity_insert($q);

    if ($onsuccess) {
        props_setkey('request.cmd', $onsuccess);
    }

    props_error("Content submitted.");

    return;
}

?>
