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
 * @version     $Id: story_edit.php,v 1.28 2008/01/07 18:07:16 roufneck Exp $
 */

// loadLibs
props_loadLib('sections,editions,media');

/**
 * @admintitle  Edit story
 * @adminprivs  assign_to_edition  Assign to edition
 * @adminprivs  wysiswg_editor  Use WYSIWYG editor
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_story_edit()
{
    $story_id = props_getrequest('story_id', VALIDATE_INT);
    $workflow_status_id = props_getrequest('workflow_status_id', VALIDATE_INT);
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    $assigned_to = props_getrequest('assigned_to', VALIDATE_INT);
    $revision_description = props_getrequest('revision_description', VALIDATE_TEXT);
    $access_level = props_getrequest('access_level', VALIDATE_INT);
    $headline = props_getrequest('headline', VALIDATE_TEXT, '!EMPTY');
    $subhead = props_getrequest('subhead', VALIDATE_TEXT);
    $byline_name = props_getrequest('byline_name', VALIDATE_TEXT);
    $byline_prefix = props_getrequest('byline_prefix', VALIDATE_TEXT);
    $byline_suffix = props_getrequest('byline_suffix', VALIDATE_TEXT);
    $body_content = props_getrequest('body_content', VALIDATE_HTML, '!EMPTY');
    $end_content = props_getrequest('end_content', VALIDATE_TEXT);
    $abstract = props_getrequest('abstract', VALIDATE_TEXT);
    $notes = props_getrequest('notes', VALIDATE_TEXT);
    $copyright = props_getrequest('copyright', VALIDATE_TEXT);
    $source_url = props_getrequest('source_url', VALIDATE_TEXT);
    $source_desc = props_getrequest('source_desc', VALIDATE_TEXT);
    $comments_enable = props_getrequest('comments_enable', VALIDATE_BOOL);
    $rss_feed = props_getrequest('rss_feed', VALIDATE_BOOL);
    $approved = props_getrequest('approved', VALIDATE_BOOL);
    $media = props_getrequest('media', VALIDATE_TEXT);
    $selected_threadcodes = props_getrequest('selected_threadcodes', VALIDATE_ARRAY);
    $available_threadcodes = props_getrequest('available_threadcodes', VALIDATE_ARRAY);
    $weight = props_getrequest('weight', VALIDATE_INT);

    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'storysearch');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'story_add');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'story_edit_comments', '&amp;story_id='.$story_id);

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
    $current_revision_description = $story->revision_description;
    $created_by = $story->created_by;
    $modified_by = $story->modified_by;
    $origination = $story->origination;
    $publication_status_id = $story->publication_status_id;

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Delete"):
            // Redirect
            props_redirect('goto', array('function'=>'story_delete', 'story_id'=>$story_id, 'pageID'=>props_pageID()));
            break;

        case props_gettext("Save & preview"):
        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {
                // If it's assigned to someone it has to be assigned by someone
                $assigned_by = (!empty($assigned_to)) ? $_SESSION['PROPS_USER']['user_id'] : '';

                $byline_prefix = ($byline_prefix == props_gettext("prefix")) ? '' : $byline_prefix;
                $byline_name = ($byline_name == props_gettext("name")) ? '' : $byline_name;
                $byline_suffix = ($byline_suffix == props_gettext("suffix")) ? '' : $byline_suffix;

                // Assemble SQL
                $q  = "UPDATE props_stories SET ";

                // If body or end content has changed, we need to create a new version
                if (($story->body_content != $body_content) || ($story->end_content != $end_content)) {
                    // Increase revision and set description
                    if ($revision_description == props_gettext("Enter here a revision description")) $revision_description = '';
                    $q .= "modified_stamp = NOW(), "
                        . "revision = revision+1, "
                        . "revision_description = '" . sql_escape_string($revision_description) . "', ";

                    // Move the current body content from the db into the previous versions table
                    $mq = "INSERT INTO props_stories_previous_versions SELECT "
                        . "story_id, revision, revision_description, body_content, end_content, "
                        . "modified_stamp, modified_by "
                        . "FROM props_stories WHERE story_id = $story_id";

                    $mq = "INSERT INTO props_stories_previous_versions SET "
                        . "story_id = $story->story_id, "
                        . "revision = $story->revision, "
                        . "revision_description = '" . sql_escape_string($story->revision_description) . "', "
                        . "body_content = '" . sql_escape_string($story->body_content) . "', "
                        . "end_content = '" . sql_escape_string($story->end_content) . "', "
                        . "modified_stamp = '" . sql_escape_string($story->modified_stamp) . "', "
                        . "modified_by = '" . sql_escape_string($story->modified_by) . "' ";
                    $result = sql_query($mq);
                }

                // If user is privileged and an other edition is selected, assign this
                // story to the selected edition and change the publication status.
                if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'assign_to_edition')
                    && $edition_id != $story->edition_id) {

                    $publication_status = PUBSTATUS_INEDITQUEUE;

                    // Add it if assigned to a new edition
                    if(!empty($edition_id)) {
                        // Append edition_id
                        $q .= "edition_id = $edition_id, ";

                        // If it's the current live edition, set pub status to "published",
                        // and set publish timestamp
                        if ($edition_id == edition_current_id(FALSE)) {
                            // Story transfered from an other edition. Publish it.
                            $q .= "published_stamp = NOW(), "
                                . "modified_stamp = NULL, ";
                            $publication_status = PUBSTATUS_PUBLISHED;
                        } else {
                            // It's either a staging edition, or a previously published (archived) edition
                            $result = sql_query("SELECT closed FROM props_editions WHERE edition_id = $edition_id");
                            $row = sql_fetch_object($result);

                            if ($row->closed) {
                                $publication_status = PUBSTATUS_ARCHIVED;
                            } else {
                                $publication_status = PUBSTATUS_STAGED;
                            }
                        }
                    } else {
                        $q .= "edition_id = NULL, ";
                    }

                    // Append publication status to SQL
                    $q .= "publication_status_id = $publication_status, ";
                }

                // Continue assembling SQL
                $q .= "workflow_status_id = $workflow_status_id, "
                    . "modified_by = '" . sql_escape_string($_SESSION['PROPS_USER']['user_id']) . "', "
                    . "assigned_to = '" . sql_escape_string($assigned_to) . "', "
                    . "assigned_by = '" . sql_escape_string($assigned_by) . "', "
                    . "section_id = $section_id, "
                    . "access_level = " . intval($access_level) . ", "
                    . "headline = '" . sql_escape_string($headline) . "', "
                    . "subhead = '" . sql_escape_string($subhead) . "', "
                    . "byline_prefix = '" . sql_escape_string($byline_prefix) . "', "
                    . "byline_name = '" . sql_escape_string($byline_name) . "', "
                    . "byline_suffix = '" . sql_escape_string($byline_suffix) . "', "
                    . "body_content = '" . sql_escape_string($body_content) . "', "
                    . "end_content = '" . sql_escape_string($end_content) . "', "
                    . "abstract = '" . sql_escape_string($abstract) . "', "
                    . "notes = '" . sql_escape_string($notes) . "', "
                    . "copyright = '" . sql_escape_string($copyright) . "', "
                    . "source_url = '" . sql_escape_string($source_url) . "', "
                    . "source_desc = '" . sql_escape_string($source_desc) . "', "
                    . "comments_enable = " . (($comments_enable) ? '1': '0') . ", "
                    . "rss_feed = " . (($rss_feed) ? '1': '0') . ", "
                    . "approved = " . (($approved) ? '1': '0') . ", "
                    . "weight = $weight "
                    . "WHERE story_id = $story_id";

                // Update the story
                $result = sql_query($q);

                // Clear any threadcodes which are associated with this story
                sql_query("DELETE FROM props_threadcodes_stories_xref WHERE story_id = $story_id");

                // Select current threadcodes
                $threadcodes = array();
                $q  = "SELECT threadcode_id, threadcode "
                    . "FROM props_threadcodes ";
                $result = sql_query($q);
                while ($row = sql_fetch_object($result)) {
                    $threadcodes[$row->threadcode_id] = $row->threadcode;
                }

                // Check for new threadcodes
                $available_threadcodes = sql_escape_string($available_threadcodes);
                $selected_threadcodes = sql_escape_string($selected_threadcodes);
                $postedcodes = array_merge($available_threadcodes, $selected_threadcodes);
                $newcodes = array_diff($postedcodes, $threadcodes);
                foreach ($newcodes as $threadcode) {
                    if (!empty($threadcode)) {
                        // It's a new threadcode, so add it to the DB
                        $q  = "INSERT INTO props_threadcodes SET threadcode = '$threadcode'";
                        $threadcode_id = sql_identity_insert($q);
                        $threadcodes[$threadcode_id] = $threadcode;
                    }
                }

                // Add story and threadcode references to the DB
                foreach ($selected_threadcodes as $threadcode) {
                    $threadcode_id = array_search($threadcode, $threadcodes);

                    if ($threadcode_id) {
                        // It's a new valid threadcode, so add it to the DB
                        $q  = "INSERT INTO props_threadcodes_stories_xref SET "
                            . " threadcode_id = $threadcode_id, "
                            . " story_id = $story_id";
                        sql_query($q);
                    }
                }

                // Clear any photos which are associated with this story
                sql_query("DELETE FROM props_media_story_xref WHERE story_id = $story_id");

                // Associate selected media
                $i = 1;
                $media_list = explode(',', $media);
                foreach ($media_list as $key => $id) {
                    $id = intval(str_replace('media', '', $id));
                    if ($id > 0) {
                        sql_query("INSERT INTO props_media_story_xref SET media_id = " . $id . ", story_id = $story_id, position = $i");
                        $i++;
                    }
                }

                // Redirect
                if (empty($GLOBALS['PROPS_ERRORSTACK']) && $op == props_gettext("Save & preview")) {
                    header('Location: ' . props_getkey('config.url.root') . '?' . http_build_query(array('cmd'=>'displaystory', 'story_id'=>$story_id, 'preview'=>TRUE)));
                    exit;
                } else {
                    props_redirect(TRUE);
                }

            } // END NO ERRORS
            break;

        // Set some default field values
        default:

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
            $comments_enable = $story->comments_enable;
            $rss_feed = $story->rss_feed;
            $approved = $story->approved;
            $revision_description = props_gettext("Enter here a revision description");
            $weight = $story->weight;

            // Get all selected codes
            $_POST['selected_threadcodes'] = array();
            $q  = "SELECT t2.threadcode_id, t2.threadcode "
                . "FROM props_threadcodes_stories_xref AS t1, props_threadcodes AS t2 "
                . "WHERE t1.threadcode_id = t2.threadcode_id "
                . "AND t1.story_id = $story_id "
                . "ORDER BY t2.threadcode ASC";
            $result = sql_query($q);
            while ($row = sql_fetch_object($result)) {
                $selected_threadcodes[] = $row->threadcode;
            }

            // Get all thread codes
            $_POST['available_threadcodes'] = array();
            $q  = "SELECT threadcode_id, threadcode "
                . "FROM props_threadcodes "
                . "ORDER BY threadcode ASC";
            $result = sql_query($q);
            while ($row = sql_fetch_object($result)) {
                $row->threadcode = $row->threadcode;
                if (!in_array($row->threadcode, $available_threadcodes)) {
                    $available_threadcodes[] = $row->threadcode;
                }
            }
            // Get media
            $media = '';
            $q = "SELECT media_id FROM props_media_story_xref WHERE story_id = $story_id ORDER BY position";
            $result = sql_query($q);
            while ($row = sql_fetch_object($result)) {
                $media .= (empty($media)) ? 'media'.$row->media_id : ',media'.$row->media_id;
            }

            break;

    } // END switch

    $byline_prefix = (empty($byline_prefix)) ? props_gettext("prefix"): $byline_prefix;
    $byline_name = (empty($byline_name)) ? props_gettext("name"): $byline_name;
    $byline_suffix = (empty($byline_suffix)) ? props_gettext("suffix"): $byline_suffix;

    if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
        props_error('Please correct the errors.');
    }

    if (($publication_status_id == PUBSTATUS_PUBLISHED) && (admin_has_priv($GLOBALS['PROPS_MODULE'], 'change_story'))) {
        props_error("NOTE: This story has already been published to the current edition. Any changes you make here will cause the story to be updated immediately on the live site.");
    }

    if ($approved != TRUE) {
        props_error("NOTE: This story is not yet approved and will not be accessible on the site.");
    }

    // Activate WYSIWYG-editor for body_content field
    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'wysiswg_editor')) {
        $GLOBALS['PROPS_WYSIWYG'] = 'body_content';
    }

    $GLOBALS['JavaScript'] =
         '<script language="JavaScript" type="text/javascript" src="' . props_getkey('config.url.scripts') . 'ToolMan.js"></script>'.LF
        .'<script language="JavaScript" type="text/javascript">'.LF
        .'  <!--'.LF
        .'    var dragsort = ToolMan.dragsort()'.LF
        .'    var junkdrawer = ToolMan.junkdrawer()'.LF
        .'    window.onload = function() {'.LF
        .'      junkdrawer.restoreListOrder("media-container");'.LF
        .'      dragsort.makeListSortable(document.getElementById("media-container"), saveOrder);'.LF
        .'    }'.LF

        .'    function saveOrder(item) {'.LF
        .'      var group = item.toolManDragGroup;'.LF
        .'      var list = group.element.parentNode;'.LF
        .'      var id = list.getAttribute("id");'.LF
        .'      if (id == null) return'.LF
        .'      group.register("dragend", function() {'.LF
        .'        ToolMan.cookies().set("list-" + id, junkdrawer.serializeList(list), 365)'.LF
        .'      })'.LF
        .'    }'.LF
        .'  //-->'.LF
        .'</script>'.LF;

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="post" name="storyform" onsubmit="submitStory();">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="story_id" type="hidden" value="' . $story_id . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF

        // Tab menu
        .'<div id="tab:story">'.LF
        .'  <span id="t:story:0" class="tab-select" onclick="selectTab(this);">' . props_gettext("Story") . '</span>'.LF
        .'  <span id="t:story:1" class="tab" onclick="selectTab(this);">' . props_gettext("Options") . '</span>'.LF
        .'  <span id="t:story:2" class="tab" onclick="selectTab(this);">' . props_gettext("Tags") . '</span>'.LF
        .'  <span id="t:story:3" class="tab" onclick="selectTab(this);">' . props_gettext("Media") . '</span>'.LF
        .'</div>'.LF

        // Story tab
        .'<fieldset class="tabbed" id="c:story:0">'.LF;

    // Include in these editions (if privileged)
    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'assign_to_edition')) {
        $current_edition_id = edition_current_id(FALSE);
        $q  = "SELECT * FROM props_editions WHERE closed = 0 ORDER BY edition_id ASC";
        $result = sql_query($q);
        if (!sql_num_rows($result)) {
            $editions = props_gettext("No editions are currently open.");
        } else {
            $editions = '  <select class="large" name="edition_id">'.LF
                       .'    <option value="">' . props_gettext("Not assigned") . '</option>'.LF;
            while ($row = sql_fetch_object($result)) {
                $strSelected = ($row->edition_id == $edition_id) ? 'selected="selected"' : '';
                $strLabel = ($row->label) ? '&nbsp;-&nbsp;'.htmlspecialchars($row->label) : '';
                $strLive = ($row->edition_id == $current_edition_id) ? '&nbsp;&nbsp;('.props_gettext("CURRENT LIVE SITE").')' : '';
                $editions .= '    <option ' . $strSelected . ' value="' . $row->edition_id . '">' . props_gettext("Edition") . ' #' . $row->edition_id . $strLabel . $strLive . '</option>'.LF;
            }
            $editions .= '  </select>'.LF;
        }

        $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext("Edition") . '</label></dt>'.LF
        .'    <dd>' . $editions . '</dd>'.LF
        .((props_geterror('edition_id')) ? '    <dd>' . props_geterror('edition_id') . '</dd>'.LF : '')
        .'  </dl>'.LF;
    }

    $output .=
         '  <dl>'.LF
        .'    <dt><label>' . props_gettext("Section") . '</label></dt>'.LF
        .'    <dd>' . section_select($section_id, 'section_id') . '</dd>'.LF
        .((props_geterror('section_id')) ? '    <dd>' . props_geterror('section_id') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Headline") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="headline" name="headline" value="' . htmlspecialchars($headline) . '" /></dd>'.LF
        .((props_geterror('headline')) ? '    <dd>' . props_geterror('headline') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><label>' . props_gettext("Subhead") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="subhead" name="subhead" value="' . htmlspecialchars($subhead) . '" /></dd>'.LF
        .((props_geterror('subhead')) ? '    <dd>' . props_geterror('subhead') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Revision") . '</label></dt>'.LF
        .'    <dd>'
        .'      #' . $revision . ' ' . ((!empty($current_revision_description)) ? ' ('.$current_revision_description.')' : '') . LF
        .       ((($revision > 1) && (admin_has_priv($GLOBALS['PROPS_MODULE'], 'story_revision_history'))) ? ' [ <a href="javascript:void(0)" onclick="window.open(\'./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=story_revision_history&amp;story_id=' . $story_id . '\',\'preview\',\'status=yes,scrollbars=yes,width=400,height=500\')">' . props_gettext("View history") . '</a> ]' : '') .LF
        .'      <br /><input class="large" type="text" id="revision_description" name="revision_description" value="' . htmlspecialchars($revision_description) . '" onclick="clearText(this);" />'.LF
        .'    </dd>'
        .((props_geterror('revision_description')) ? '    <dd>' . props_geterror('revision_description') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <p>'.LF
        .((props_geterror('abstract')) ? '    ' . props_geterror('Abstract') . '<br />'.LF : '')
        .'    <label class="label">' . props_gettext("Abstract") . '</label>'.LF
        .'    <textarea class="full" style="height: 80px;" id="abstract" name="abstract" rows="5" cols="80">' . htmlspecialchars($abstract) . '</textarea>'.LF
        .'  </p>'.LF

        .'  <p>'.LF
        .((props_geterror('body_content')) ? '    ' . props_geterror('body_content') . '<br />'.LF : '')
        .'    <label class="label">' . props_gettext("Body content") . '</label>'.LF
        .'    <textarea class="full" style="height: 350px;" id="body_content" name="body_content" rows="25" cols="80">' . htmlspecialchars($body_content) . '</textarea>'.LF
        .'  </p>'.LF

        .'  <p>'.LF
        .((props_geterror('end_content')) ? '    ' . props_geterror('end_content') . '<br />'.LF : '')
        .'    <label class="label">' . props_gettext("End content") . '</label>'.LF
        .'    <textarea class="full" style="height: 80px;" id="end_content" name="end_content" rows="5" cols="80">' . htmlspecialchars($end_content) . '</textarea>'.LF
        .'  </p>'.LF

        .'</fieldset>'.LF
        // Story tab end

        // Options tab
        .'<fieldset class="tabbed" id="c:story:1" style="display: none;">'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Story weight") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" id="weight" name="weight">'.LF;
    $w = 100;
    do {
        $output .= '        <option ' . ($w == $weight ? 'selected="selected"' : '') . ' value="' . $w . '">' . $w . '</option>'.LF;
        $w = $w - 10;
    } while ($w);
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('weight')) ? '    <dd>' . props_geterror('weight') . '</dd>'.LF : '')
        .'  </dl>'.LF
        
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Access level") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" id="access_level" name="access_level">'.LF
        .'        <option ' . (($access_level == ACCESS_FREE) ? 'selected="selected"' : '') . ' value="' . ACCESS_FREE . '">' . props_gettext("Free") . '</option>'.LF
        .'        <option ' . (($access_level == ACCESS_REG_REQUIRED) ? 'selected="selected"' : '') . ' value="' . ACCESS_REG_REQUIRED . '">' . props_gettext("Registration required") . '</option>'.LF;
    if (props_getkey('config.archives.paid')) {
        $output .=
         '        <option ' . (($access_level == ACCESS_PAID_ARCHIVES) ? 'selected="selected"' : '') . ' value="' . ACCESS_PAID_ARCHIVES . '">' . props_gettext("Paid archives") . '</option>'.LF;
    }
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('access_level')) ? '    <dd>' . props_geterror('access_level') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Workflow status") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" id="workflow_status_id" name="workflow_status_id">'.LF;
    foreach (props_getkey('config.workflow_status') as $key => $val) {
        $selected = ($key == $workflow_status_id) ? 'selected="selected"' : '';
        $output .=
         '        <option ' . $selected . ' value="' . $key . '">' . htmlspecialchars($val) . '</option>'.LF;
    }
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('workflow_status_id')) ? '    <dd>' . props_geterror('workflow_status_id') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Assigned to") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <select class="large" id="assigned_to" name="assigned_to">'.LF
        .'        <option value="">' . props_gettext("Not assigned") . '</option>'.LF;
    $result = sql_query("SELECT user_id, username FROM props_users WHERE user_type IN (".PROPS_USERTYPE_ADMIN.", ".PROPS_USERTYPE_FOUNDER.") ORDER BY username");
    while ($row = sql_fetch_object($result)) {
        $selected = ($row->user_id == $assigned_to) ? 'selected="selected"' : '';
        $output .=
         '        <option ' . $selected . ' value="' . $row->user_id . '">' . htmlspecialchars($row->username) . '</option>'.LF;
    }
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('var')) ? '      <dd>' . props_geterror('var') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Byline") . '</label><br /><span>' . props_gettext("Standard values 'prefix', 'name' and 'suffix' will not be stored.") . '</span></dt>'.LF
        .'    <dd>'.LF
        .'      <input style="width: 50px;" type="text" id="byline_prefix" name="byline_prefix" value="' . htmlspecialchars($byline_prefix) . '" />&nbsp;'.LF
        .'      <input style="width: 100px;" type="text" id="byline_name" name="byline_name" value="' . htmlspecialchars($byline_name) . '" />&nbsp;'.LF
        .'      <input style="width: 50px;" type="text" id="byline_suffix" name="byline_suffix" value="' . htmlspecialchars($byline_suffix) . '" />'.LF
        .'    </dd>'.LF
        .'    <dd>'.LF
        .'      <select class="large" id="byline_name_quickpick" name="byline_name_quickpick" onchange="window.document.storyform.byline_name.value = this.options[this.selectedIndex].value;">'.LF
        .'        <option>' . props_gettext("Quick pick name") . '</option>'.LF;
    // byline name quickpick bar
    $result = sql_query("SELECT DISTINCT(byline_name) FROM props_stories ORDER BY byline_name");
    while ($row = sql_fetch_object($result)) {
        if (!empty($row->byline_name)) {
            $option = (strlen($row->byline_name) > 24) ? substr($row->byline_name, 0, 24) . "..." : $row->byline_name;
            $output .= '        <option value="' . htmlspecialchars($row->byline_name) . '">' . htmlspecialchars($option) . '</option>'.LF;
        }
    }
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .((props_geterror('byline_prefix')) ? '    <dd>' . props_geterror('byline_prefix') . '</dd>'.LF : '')
        .((props_geterror('byline_name')) ? '    <dd>' . props_geterror('byline_name') . '</dd>'.LF : '')
        .((props_geterror('byline_suffix')) ? '    <dd>' . props_geterror('byline_suffix') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Copyright") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="copyright" name="copyright" value="' . htmlspecialchars($copyright) . '" /></dd>'.LF
        .((props_geterror('copyright')) ? '    <dd>' . props_geterror('copyright') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Source URL") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="source_url" name="source_url" value="' . htmlspecialchars($source_url) . '" /></dd>'.LF
        .((props_geterror('source_url')) ? '    <dd>' . props_geterror('source_url') . '</dd>'.LF : '')
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Source description") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="source_desc" name="source_desc" value="' . htmlspecialchars($source_desc) . '" /></dd>'.LF
        .((props_geterror('source_desc')) ? '    <dd>' . props_geterror('source_desc') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Notes") . '</label><br />' . props_gettext("Editors notes. These will not be displayed on the frontpage.") . '</dt>'.LF
        .'    <dd><textarea class="large" id="notes" name="notes" rows="3" cols="30">' . htmlspecialchars($notes) . '</textarea></dd>'.LF
        .((props_geterror('notes')) ? '    <dd>' . props_geterror('notes') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Enable comments") . '</label></dt>'.LF
        .'    <dd><input class="checkbox" type="checkbox" id="comments_enable" name="comments_enable" value="1" ' . ((!empty($comments_enable)) ? 'checked="checked"': '') . ' /></dd>'.LF
        .((props_geterror('comments_enable')) ? '<dd>' . props_geterror('comments_enable') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Include in RSS feed") . '</label></dt>'.LF
        .'    <dd><input class="checkbox" type="checkbox" id="rss_feed" name="rss_feed" value="1" ' . ((!empty($rss_feed)) ? 'checked="checked"': '') . ' /></dd>'.LF
        .((props_geterror('rss_feed')) ? '<dd>' . props_geterror('rss_feed') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Approve content for publishing") . '</label></dt>'.LF
        .'    <dd><input class="checkbox" type="checkbox" id="approved" name="approved" value="1" ' . ((!empty($approved)) ? 'checked="checked"': '') . ' /></dd>'.LF
        .((props_geterror('approved')) ? '<dd>' . props_geterror('approved') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'</fieldset>'.LF
        // Options tab end

        // Tags tab
        .'<fieldset class="tabbed" id="c:story:2" style="display: none;">'.LF
        .'  <p>' . props_gettext("Tags are used to tie related stories together, ex: 'Formula 1 2007')") . '</p>'.LF

        .'  <dl>'.LF
        .'    <dt>'.LF
        .'      <label>' . props_gettext("Tags assigned to this story") . '</label><br />'.LF
        .'      <select class="large" id="selected_threadcodes" name="selected_threadcodes[]" size="10" multiple="multiple" ondblclick="move_threadcodes(\'selected_threadcodes\', \'available_threadcodes\');">'.LF;
    foreach ($selected_threadcodes as $key => $val) {
        $output .=
        '        <option value="' . $val . '">' . $val . '</option>'.LF;
    }
    $output .=
         '      </select>'.LF
        .'    </dt>'.LF

        .'    <dd>'.LF
        .'      <label>' . props_gettext("Available tags") . '</label><br />'.LF
        .'      <select class="large" id="available_threadcodes" name="available_threadcodes[]" size="10" multiple="multiple" ondblclick="move_threadcodes(\'available_threadcodes\', \'selected_threadcodes\');">'.LF;
    foreach ($available_threadcodes as $key => $val) {
        if (!in_array($val, $selected_threadcodes)) {
         $output .=
         '        <option value="' . $val . '">' . $val . '</option>'.LF;
        }
    }
    $output .=
         '      </select>'.LF
        .'    </dd>'.LF
        .'    <dd>'.LF
        .'      <input class="medium" type="text" id="new_threadcode" name="new_threadcode" value="" />&nbsp;'.LF
        .'      <input type="button" value="' . props_gettext("Add") . '" onclick="add_threadcode();" onkeydown="transformTag(this)" onkeyup="transformTag(this)" onblur="transformTag(this)"  />'.LF
        .'    </dd>'.LF
        .'  </dl>'.LF
        .'  <p>' . props_gettext("Hold down Ctrl or Apple key to select multiple tags or double click") . '</p>'.LF
        .'</fieldset>'.LF
        // Tags tab

        // Media tab
        .'<fieldset class="tabbed" id="c:story:3" style="display: none;">'.LF
        .'  <input class="button" type="button" onclick="window.open(\'./?module=media&amp;function=media_picker\',\'media_picker\',\'status=yes,scrollbars=yes,width=600,height=650\')" value="' . props_gettext('.media.media_picker') . '" />'.LF
        .'  <p>' . props_gettext("You can drag and drop the thumbnails to order assigned images.") . '</p>'.LF

        .'  <ul id="media-container">'.LF;
    $media_items = '';
    $media_list = explode(',', $media);
    foreach ($media_list as $key => $id) {
        $id = intval(str_replace('media', '', $id));
        if ($id > 0) {
            $q  = "SELECT * FROM props_media "
                . "WHERE media_id = " . intval($id) . "";
            $result = sql_query($q);
            $row = sql_fetch_assoc($result);
            $media_items .= (empty($media_items)) ? $id : ','.$id;
            media_get_details($row);

            $output .=
             '    <li id="media' . $id . '" class="thumbnail_box">'.LF
            .'      <div class="thumbnail_image" style="background-image:url(\'' . $row['thumb_url'] . '\')"></div>'.LF
            .'      <div class="thumbnail_label" onclick="removeMedia(\'media' . $id . '\');">' . props_gettext("Remove") . '</div>'.LF
            .'    </li>'.LF;
        }
    }
    $output .=
         '  </ul>'.LF
        .'  <input id="media" name="media" type="hidden" value="' . $media . '" />'.LF

        .'</fieldset>'.LF
        // Media tab end

        .'<p>'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF;

    if (admin_has_priv($GLOBALS['PROPS_MODULE'], 'story_delete')) {
        $output .= '  <input class="button" name="op" type="submit" value="' . props_gettext("Delete") . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');" />&nbsp;&nbsp;'.LF;
    }

    $output .=
         '  <input class="button" name="op" type="submit" value="' . props_gettext("Save & preview") . '" />&nbsp;&nbsp;'.LF
        .'  <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'</p><br />'.LF

        .'</form>'.LF
        .'<br />'.LF;

    return $output;
}

?>
