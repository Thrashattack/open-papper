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
 * @version     $Id: story_view.php,v 1.11 2007/12/11 15:46:29 roufneck Exp $
 */

// loadLibs
props_loadLib('sections,editions,media');

/**
 * @admintitle  View story
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_story_view()
{
    $story_id = props_getrequest('story_id', VALIDATE_INT);
    $redirect_url = props_getrequest('redirect_url');

    if (!isset($_POST['selected_threadcodes']) || !is_array($_POST['selected_threadcodes'])) {
        $_POST['selected_threadcodes'] = array();
    }

    // Get details from DB.
    $q  = "SELECT * FROM props_stories "
        . "WHERE story_id = $story_id";
    $result = sql_query($q);
    $story = sql_fetch_object($result);

    if (!sql_num_rows($result)) {
        props_error("Invalid ID.");
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
        exit;
    }

    sql_free_result($result);

    $created_stamp = $story->created_stamp;
    $modified_stamp = $story->modified_stamp;
    $published_stamp = $story->published_stamp;
    $revision = $story->revision;
    $revision_description = $story->revision_description;
    $created_by = $story->created_by;
    $modified_by = $story->modified_by;
    $origination = $story->origination;
    $publication_status_id = $story->publication_status_id;
    $workflow_status_id = $story->workflow_status_id;
    $assigned_to = $story->assigned_to;
    $edition_id = $story->edition_id;
    $section_id = $story->section_id;
    $copyright = $story->copyright;
    $headline = $story->headline;
    $subhead = $story->subhead;
    $byline_prefix = $story->byline_prefix;
    $byline_name = $story->byline_name;
    $byline_suffix = $story->byline_suffix;
    $body_content = $story->body_content;
    $end_content = $story->end_content;
    $abstract = $story->abstract;
    $notes = $story->notes;
    $access_level = $story->access_level;

    // Get media
    $media = array();
    $q = "SELECT media_id FROM props_media_story_xref WHERE story_id = $story_id ORDER BY position";
    $result = sql_query($q);
    while ($row = sql_fetch_object($result)) {
        $media[] = $row->media_id;
    }

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect to main menu
            header('Location: ' . $redirect_url);
            exit;
            break;

        // Set some default field values
        default:

            // Set referer
            if (empty($_SERVER["HTTP_REFERER"])) {
                $redirect_url = PROPS_URL;
            } else {
                $redirect_url = $_SERVER["HTTP_REFERER"];
            }

            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post" name="storyform" onSubmit="selectAllThreadcodes();">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="story_id" type="hidden" value="' . $story_id . '" />'.LF
        .'<input name="redirect_url" type="hidden" value="' . htmlspecialchars($redirect_url) . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<p>'.LF
        .'  <input class="button" name="op" value="' . props_gettext("Cancel") . '" type="submit" />'.LF
        .'</p>'.LF
        .'</form>'.LF;

    $output .=
         '<fieldset>'.LF
        .'  <legend>' . htmlspecialchars($headline) . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Headline") . '</label></dt>'.LF
        .'    <dd><strong>' . htmlspecialchars($headline) . '</strong></dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Subhead") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($subhead) . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Byline") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($byline_prefix) . ' ' . htmlspecialchars($byline_name) . ' ' . htmlspecialchars($byline_suffix) . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Abstract") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($abstract) . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Body content") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($body_content) . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Revision") . '</label></dt>'.LF
        .'    <dd><strong>#' . $revision . ':</strong> ' . htmlspecialchars($revision_description) . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Copyright") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($copyright) . '</dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Notes") . '</label></dt>'.LF
        .'    <dd>' . htmlspecialchars($notes) . '</dd>'.LF
        .'  </dl>'.LF;

    foreach ($media as $key => $id) {
        $q  = "SELECT * FROM props_media "
            . "WHERE media_id = " . intval($id) . "";
        $result = sql_query($q);
        $row = sql_fetch_assoc($result);
        media_get_details($row);

        $output .= '  <span style="float: left;" class="thumbnail" id="media-' . $id . '"><input name="media[]" value="' . $id . '" type="hidden" /><img src="' . $row['thumb_url'] . '" alt="' . htmlspecialchars($row['caption']) . '" /></span>'.LF;
    }

    $output .=
         '</fieldset>'.LF;

    return $output;
}

?>
