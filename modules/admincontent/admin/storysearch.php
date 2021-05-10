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
 * @version     $Id: storysearch.php,v 1.14 2007/12/17 08:00:53 roufneck Exp $
 */

/**
 * @admintitle  Story search
 * @adminnav    1
 * @return  string  admin screen html content
 */
function admin_storysearch()
{
    props_loadLib('editions');

    // Set sitebar
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'storysearch');
    admin_sidebar_add($GLOBALS['PROPS_MODULE'], 'story_add');

    // Get the needed posted vars here.
    $op = props_getrequest('op', VALIDATE_TEXT);
    $position = props_getrequest('position', VALIDATE_INT);
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    $min_age = props_getrequest('min_age', VALIDATE_INT);
    $max_age = props_getrequest('max_age', VALIDATE_INT);
    $sort_column = props_getrequest('sort_column', '_a-zA-Z0-9', 'SANITIZE');
    $sort_direction = props_getrequest('sort_direction', 'A-Z', 'SANITIZE');
    $headline = props_getrequest('headline', VALIDATE_TEXT);
    $subhead = props_getrequest('subhead', VALIDATE_TEXT);
    $body_content = props_getrequest('body_content', VALIDATE_TEXT);
    $approved = props_getrequest('approved', VALIDATE_BOOL);
    $rss_feed = props_getrequest('rss_feed', VALIDATE_BOOL);
    $comments_enable = props_getrequest('comments_enable', VALIDATE_BOOL);
    // Arrays
    $include = props_getrequest('include', VALIDATE_ARRAY);
    $byline_name = props_getrequest('byline_name', VALIDATE_ARRAY);
    $publication_status_id = props_getrequest('publication_status_id', VALIDATE_ARRAY);
    $workflow_status_id = props_getrequest('workflow_status_id', VALIDATE_ARRAY);
    $created_by = props_getrequest('created_by', VALIDATE_ARRAY);
    $modified_by = props_getrequest('modified_by', VALIDATE_ARRAY);
    $assigned_to = props_getrequest('assigned_to', VALIDATE_ARRAY);
    $assigned_by = props_getrequest('assigned_by', VALIDATE_ARRAY);
    $section_id = props_getrequest('section_id', VALIDATE_ARRAY);
    $origination = props_getrequest('origination', VALIDATE_ARRAY);
    $copyright = props_getrequest('copyright', VALIDATE_ARRAY);
    $threadcode = props_getrequest('threadcode', VALIDATE_ARRAY);

    $GLOBALS['PROPS_FUNCTION_INFO'] =
         '<h2>' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']) . '</h2>'.LF
        .'<p>'.props_gettext("The content search function is very flexible and powerfull. Here are some examples to get started.").'</p>'.LF
        .'<p>'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;include%5B0%5D=headline">' . props_gettext("All content") . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;include%5B0%5D=edition_id&amp;edition_id='.edition_current_id(FALSE).'">' . props_gettext("Current edition") . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;include%5B0%5D=assigned_to&amp;assigned_to%5B0%5D='.$_SESSION['PROPS_USER']['user_id'].'">' . props_gettext("Content assigned to") . ' ' . $_SESSION['PROPS_USER']['username'] . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;include%5B0%5D=approved">' . props_gettext("Content not approved") . '</a><br />'.LF
        .'  <a href="./?op=search&amp;module='.$GLOBALS['PROPS_MODULE'].'&amp;function='.$GLOBALS['PROPS_FUNCTION'].'&amp;include%5B0%5D=approved&amp;approved=1&amp;include%5B%5D=max_age&amp;max_age=7&amp;include%5B%5D=rss_feed&amp;rss_feed=1">' . props_gettext("Content max 7 days old and in the RSS feed") . '</a><br />'.LF
        .'</p>'.LF;

    // Build query array for later use
    $GLOBALS['PROPS_QUERY_ARRAY'] = array();
    $var_array = array('edition_id', 'min_age', 'max_age', 'sort_column', 'sort_direction',
        'headline', 'subhead', 'body_content', 'include', 'byline_name',
        'publication_status_id', 'workflow_status_id', 'created_by', 'modified_by',
        'assigned_to', 'assigned_by', 'section_id', 'origination', 'copyright', 'threadcode',
        'approved', 'rss_feed', 'comments_enable');
    foreach ($var_array AS $var) {
        if (!empty(${$var})) {
            $GLOBALS['PROPS_QUERY_ARRAY'][$var] = ${$var};
        }
    }

    // Construct publication_status array
    $publication_status = array();
    $publication_status[PUBSTATUS_INEDITQUEUE] = props_gettext("In edit queue");
    $publication_status[PUBSTATUS_STAGED] = props_gettext("Staged for publication");
    $publication_status[PUBSTATUS_PUBLISHED] = props_gettext("Published");
    $publication_status[PUBSTATUS_ARCHIVED] = props_gettext("Archived");

    // Handle form submissions here
    switch($op) {

        case 'search':

            // Set default sort_column and direction
            if (empty($sort_column)) {
                $sort_column = "modified_stamp";
            }

            // Set default sort_column and direction
            if ($sort_direction != 'ASC') {
                $sort_direction = 'DESC';
            }

            // If user didn't select any criteria..
            if (empty($include)) {
                props_error("You did not select any criteria to search on.");
            }

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                // Set referer
                props_redirect(FALSE, array_merge(array('op'=>'search'), $GLOBALS['PROPS_QUERY_ARRAY']));

                // Assemble SQL
                $q  = "SELECT props_stories.story_id, headline, published_stamp, modified_stamp, "
                    . "  assigned_to, assigned_by, workflow_status_id, publication_status_id, section_id, approved, "
                    . "  COUNT(props_stories_comments.comment_id) AS sum_comments "
                    . "FROM props_stories ";

                // Queries by thread code need additional tables
                if (in_array("threadcode", $include)) {
                    $q .= "  LEFT JOIN props_threadcodes_stories_xref "
                        . "  ON props_stories.story_id = props_threadcodes_stories_xref.story_id ";
                }

                $q .= "  LEFT JOIN props_stories_comments "
                    . "  ON props_stories.story_id = props_stories_comments.story_id "
                    . "WHERE (1=1) ";

                if (in_array("edition_id", $include) && !empty($edition_id)) {
                    $q .= "AND edition_id = $edition_id ";
                }

                // Process text fields
                $fields = array('headline', 'subhead', 'body_content');
                foreach($fields as $fieldname) {
                    if (in_array($fieldname, $include) && !empty(${$fieldname})) {
                        $q .= "AND $fieldname LIKE '%" . sql_escape_string(${$fieldname}) . "%' ";
                    }
                }

                // Process booleans
                $fields = array('approved', 'rss_feed', 'comments_enable');
                foreach($fields as $fieldname) {
                    if (in_array($fieldname, $include)) {
                        $q .= "AND $fieldname = " . ((${$fieldname}) ? '1': '0') . " ";
                    }
                }

                // Now add additional criteria for queries by edition or thread code
                if (in_array('threadcode', $include)) {
                    $max = count($threadcode);
                    if ($max > 0) {
                        props_loadLib('stories');
                        $i = 0;
                        $q .= "AND props_threadcodes_stories_xref.threadcode_id IN (";
                        foreach($threadcode as $code) {
                            $threadcode_id = get_threadcode_id($code);
                            $q .= "$threadcode_id ";
                            if (++$i < $max) {
                                $q .= ", ";
                            }
                        }
                        $q .= ") ";
                    } else {
                        // Thread code is invalid.  Search for a bogus code so the search returns 0 results
                        $q .= "AND props_threadcodes_stories_xref.threadcode_id = -999 ";
                    }
                }

                // Process selectbox fields
                $fields = array('byline_name','publication_status_id','workflow_status_id',
                                'created_by','modified_by','assigned_to','assigned_by',
                                'section_id','origination','copyright');
                foreach($fields as $fieldname) {
                    if (in_array($fieldname, $include)) {
                        $itemcount = 0;
                        if (is_array(${$fieldname}) && !empty(${$fieldname})) {
                            $max = sizeof(${$fieldname});
                            $q .= "AND (";
                            foreach(${$fieldname} as $item) {
                                $q .= "$fieldname = '" . sql_escape_string($item) . "' ";
                                if (++$itemcount < $max) {
                                    $q .= " OR ";
                                }
                            }
                            $q .= ") ";
                        }
                    }
                }

                // Some fields are custom, and need to be added to the SQL query
                // on an individual basis.
                if (in_array("min_age", $include) && !empty($min_age)) {
                    $q .= "AND DATE_SUB(NOW(), INTERVAL $min_age DAY) >= created_stamp ";
                }
                if (in_array("max_age", $include) && !empty($max_age)) {
                    $q .= "AND DATE_SUB(NOW(), INTERVAL $max_age DAY) <= created_stamp ";
                }

                $q .= "GROUP BY story_id ";
                $q .= "ORDER BY $sort_column $sort_direction ";

                // Get total results
                $result = sql_query($q);
                $result_rows = sql_num_rows($result);
                if (!$position) {
                    $position = 0;
                }

                // Construct page navigation
                $pagination = props_pagination($result_rows, $position, 25, search_results_uri(array('op'=>'search')));

                // Get results for this page
                $q .= "LIMIT $position, 25";
                $result = sql_query($q);

                // Now reverse sort direction, so that clicking on the same column
                // will reverse the list
                if ($sort_direction == 'ASC') {
                    $sort_direction = 'DESC';
                } else {
                    $sort_direction = 'ASC';
                }

                // Page title
                $GLOBALS['PROPS_PAGETITLE'] = props_gettext("Search results");

                // Generate the search results
                $output =
                     '<p><a class="button" href="' . search_results_uri() . '">' . props_gettext("Modify search") . '</a></p>'
                    .'<p>'.sprintf(props_gettext("%s results found"), $result_rows).' - '.$pagination.'</p>'
                    .'<table>'.LF
                    .'  <tr>'.LF
                    .'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'headline', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Headline") . ' (' . $sort_direction . ')">' . props_gettext("Headline") . '</a></th>'.LF
                    .'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'section_id', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Section") . ' (' . $sort_direction . ')">' . props_gettext("Section") . '</a></th>'.LF
                    .'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'published_stamp', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Published") . ' (' . $sort_direction . ')">' . props_gettext("Published") . '</a></th>'.LF
                    //.'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'assigned_to', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Assigned to") . ' (' . $sort_direction . ')">' . props_gettext("Assigned to") . '</a></th>'.LF
                    //.'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'assigned_by', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Assigned by") . ' (' . $sort_direction . ')">' . props_gettext("Assigned by") . '</a></th>'.LF
                    .'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'workflow_status_id', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Workflow status") . ' (' . $sort_direction . ')">' . props_gettext("Workflow status") . '</a></th>'.LF
                    .'    <th><a href="' . search_results_uri(array('op'=>'search', 'sort_column'=>'publication_status_id', 'sort_direction'=>$sort_direction)) . '" title="' . props_gettext("Sort by") . ' ' . props_gettext("Publication status") . ' (' . $sort_direction . ')">' . props_gettext("Publication status") . '</a></th>'.LF
                    .'    <th>' . props_gettext("Comments") . '</th>'.LF
                    .'    <th>' . props_gettext("Actions") . '</th>'.LF
                    .'  </tr>'.LF;

                if (!$result_rows) {
                    $output .= '  <tr class="row1"><td colspan="7" style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
                } else {
                    props_loadLib('sections,stories');

                    $function = (admin_has_priv($GLOBALS['PROPS_MODULE'], 'story_edit')) ? 'story_edit' : 'story_view';

                    $row_num = 1;
                    while ($row = sql_fetch_object($result)) {

                        $headline = $row->headline;
                        if (strlen($headline) > 50) {
                            $headline = substr($headline, 0, 50) . "...";
                        }

                        $approved = ($row->approved != TRUE) ? '<span class="not_approved">' . props_gettext("Not approved") . '</span>&nbsp;' : '';

                        $output .=
                         '  <tr class="row'.$row_num.'">'.LF
                        .'    <td><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $function . '&amp;story_id=' . $row->story_id . '" title="' . props_gettext("Edit story") . ': ' . htmlspecialchars($row->headline) . '">' . htmlspecialchars($headline) . '</a></td>'.LF
                        .'    <td>' . section_shortname($row->section_id) . '</td>'.LF
                        .'    <td>' . (((($row->publication_status_id == PUBSTATUS_PUBLISHED) || ($row->publication_status_id == PUBSTATUS_ARCHIVED)) && !empty($row->published_stamp)) ? strftime("%a %d %b", strtotime($row->published_stamp)): '') . '</td>'.LF
                        //.'    <td>' . $row->assigned_to . '</td>'.LF
                        //.'    <td>' . $row->assigned_by . '</td>'.LF
                        .'    <td>' . props_getkey('config.workflow_status.'.$row->workflow_status_id) . '</td>'.LF
                        .'    <td>' . $approved . $publication_status[$row->publication_status_id] . '</td>'.LF
                        .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=story_edit_comments&amp;story_id=' . $row->story_id . '" title="' . props_gettext("Edit comments") . '">' . $row->sum_comments . '</a></td>'.LF
                        .'    <td style="text-align: center;"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=story_delete&amp;story_id=' . $row->story_id . '&amp;pageID=' . props_pageID() . '" onclick="return confirmSubmit(\'' . props_gettext("Are you sure you want to delete this?") . '\');">[' . props_gettext("Delete") . ']</a></td>'.LF
                        .'  </tr>'.LF;

                        // Update row number
                        $row_num = ($row_num == 1) ? 2 : 1;
                    }
                }

                $output .=
                     '</table>'.LF
                    .'<br /><p><a class="button" href="' . search_results_uri() . '">' . props_gettext("Modify search") . '</a></p>';

                return $output;
            }
            break;

    } // END switch

    // Set referer
    props_redirect(FALSE, $GLOBALS['PROPS_QUERY_ARRAY']);

    // Get admins and founders
    $adminlist = array();
    $result = sql_query("SELECT user_id, username FROM props_users WHERE user_type IN (".PROPS_USERTYPE_ADMIN.", ".PROPS_USERTYPE_FOUNDER.") ORDER BY username");
    while ($row = sql_fetch_object($result)) {
        $adminlist[$row->user_id] = $row->username;
    }
    sql_free_result($result);

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<form action="./" method="get">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="op" type="hidden" value="search" />'.LF
        .'  <p>'.LF
        .'    <input class="button" type="submit" value="' . props_gettext("Find stories matching all of the following criteria") . ':" />'.LF
        .'  </p>'.LF
        .'  <fieldset>'.LF
        .'    <legend>' . props_gettext("Search options") . '</legend>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('headline', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="headline" />&nbsp;<label>' . props_gettext("Headline") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="headline" name="headline" value="' . htmlspecialchars($headline) . '" /></dd>'.LF
        .((props_geterror('headline')) ? '      <dd>' . props_geterror('headline') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('subhead', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="subhead" />&nbsp;<label>' . props_gettext("Subhead") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="subhead" name="subhead" value="' . htmlspecialchars($subhead) . '" /></dd>'.LF
        .((props_geterror('subhead')) ? '      <dd>' . props_geterror('subhead') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('body_content', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="body_content" />&nbsp;<label>' . props_gettext("Body content") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="body_content" name="body_content" value="' . htmlspecialchars($body_content) . '" /></dd>'.LF
        .((props_geterror('body_content')) ? '      <dd>' . props_geterror('body_content') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('edition_id', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="edition_id" />&nbsp;<label>' . props_gettext("Edition") . '</label></dt>'.LF
        .'      <dd><input class="large" type="text" id="edition_id" name="edition_id" value="' . htmlspecialchars($edition_id) . '" /></dd>'.LF
        .((props_geterror('edition_id')) ? '      <dd>' . props_geterror('edition_id') . '</dd>'.LF : '')
        .'    </dl>'.LF;

    $query = sql_query("SELECT section_id, fullname FROM props_sections");
    $output .=
         '    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('section_id', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="section_id" />&nbsp;<label>' . props_gettext("Section") . '</label></dt>'.LF
        .'      <dd>' . listbox($query, "section_id", "fullname", 3) . '</dd>'.LF
        .((props_geterror('section_id')) ? '      <dd>' . props_geterror('section_id') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('min_age', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="min_age" />&nbsp;<label>' . props_gettext("Creation date is at least") . '</label></dt>'.LF
        .'      <dd><input class="narrow" type="text" id="min_age" name="min_age" value="' . htmlspecialchars($min_age) . '" />&nbsp;' . props_gettext("days ago") . '</dd>'.LF
        .((props_geterror('min_age')) ? '      <dd>' . props_geterror('min_age') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('max_age', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="max_age" />&nbsp;<label>' . props_gettext("Creation date is no more than") . '</label></dt>'.LF
        .'      <dd><input class="narrow" type="text" id="max_age" name="max_age" value="' . htmlspecialchars($max_age) . '" />&nbsp;' . props_gettext("days ago") . '</dd>'.LF
        .((props_geterror('max_age')) ? '      <dd>' . props_geterror('max_age') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('publication_status_id', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="publication_status_id" />&nbsp;<label>' . props_gettext("Publication status") . '</label></dt>'.LF
        .'      <dd>'.LF
        .'        <select class="large" multiple="multiple" name="publication_status_id[]" size="3">'.LF;
    foreach ($publication_status AS $key => $val) {
        $output .= '        <option ' . ((in_array($key, $publication_status_id)) ? 'selected="selected"' : '') . ' value="' . $key . '">' . $publication_status[$key] . '</option>'.LF;
    }
    $output .=
         '        </select>'.LF
        .'      </dd>'.LF
        .((props_geterror('publication_status_id')) ? '      <dd>' . props_geterror('publication_status_id') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('workflow_status_id', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="workflow_status_id" />&nbsp;<label>' . props_gettext("Workflow status") . '</label></dt>'.LF
        .'      <dd>'.LF
        .'        <select class="large" multiple="multiple" name="workflow_status_id[]" size="3">'.LF;
    foreach (props_getkey('config.workflow_status') as $key => $val) {
        $output .= '        <option ' . ((in_array($key, $workflow_status_id)) ? 'selected="selected"' : '') . ' value="' . $key . '">' . $val . '</option>'.LF;
    }
    $output .=
         '        </select>'.LF
        .'      </dd>'.LF
        .((props_geterror('workflow_status_id')) ? '      <dd>' . props_geterror('workflow_status_id') . '</dd>'.LF : '')
        .'    </dl>'.LF;

    $query = sql_query("SELECT DISTINCT(origination) FROM props_stories ORDER BY origination");
    $output .=
         '    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('origination', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="origination" />&nbsp;<label>' . props_gettext("Origination") . '</label></dt>'.LF
        .'      <dd>' . listbox($query, "origination", "origination", 3) . '</dd>'.LF
        .((props_geterror('origination')) ? '      <dd>' . props_geterror('origination') . '</dd>'.LF : '')
        .'    </dl>'.LF;

    $query = sql_query("SELECT * FROM props_threadcodes ORDER BY threadcode");
    $output .=
         '    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('threadcode', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="threadcode" />&nbsp;<label>' . props_gettext("Thread code") . '</label></dt>'.LF
        .'      <dd>' . listbox($query, "threadcode", "threadcode", 3) . '</dd>'.LF
        .((props_geterror('threadcode')) ? '      <dd>' . props_geterror('threadcode') . '</dd>'.LF : '')
        .'    </dl>'.LF;

    $query = sql_query("SELECT DISTINCT(byline_name) AS byline_name FROM props_stories ORDER BY byline_name");
    $output .=
         '    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('byline_name', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="byline_name" />&nbsp;<label>' . props_gettext("Byline name") . '</label></dt>'.LF
        .'      <dd>' . listbox($query, "byline_name", "byline_name", 3) . '</dd>'.LF
        .((props_geterror('byline_name')) ? '      <dd>' . props_geterror('byline_name') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('created_by', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="created_by" />&nbsp;<label>' . props_gettext("Created by") . '</label></dt>'.LF
        .'      <dd>' . adminbox($adminlist, "created_by", 3) . '</dd>'.LF
        .((props_geterror('created_by')) ? '      <dd>' . props_geterror('created_by') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('modified_by', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="modified_by" />&nbsp;<label>' . props_gettext("Modified by") . '</label></dt>'.LF
        .'      <dd>' . adminbox($adminlist, "modified_by", 3) . '</dd>'.LF
        .((props_geterror('modified_by')) ? '      <dd>' . props_geterror('modified_by') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('assigned_to', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="assigned_to" />&nbsp;<label>' . props_gettext("Assigned to") . '</label></dt>'.LF
        .'      <dd>' . adminbox($adminlist, "assigned_to", 3) . '</dd>'.LF
        .((props_geterror('assigned_to')) ? '      <dd>' . props_geterror('assigned_to') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('assigned_by', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="assigned_by" />&nbsp;<label>' . props_gettext("Assigned by") . '</label></dt>'.LF
        .'      <dd>' . adminbox($adminlist, "assigned_by", 3) . '</dd>'.LF
        .((props_geterror('assigned_by')) ? '      <dd>' . props_geterror('assigned_by') . '</dd>'.LF : '')
        .'    </dl>'.LF;

    $query = sql_query("SELECT DISTINCT(copyright) FROM props_stories ORDER BY copyright");
    $output .=
         '    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('copyright', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="copyright" />&nbsp;<label>' . props_gettext("Copyright") . '</label></dt>'.LF
        .'      <dd>' . listbox($query, "copyright", "copyright", 3) . '</dd>'.LF
        .((props_geterror('copyright')) ? '      <dd>' . props_geterror('copyright') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('approved', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="approved" />&nbsp;<label>' . props_gettext("Approved for publication") . '</label></dt>'.LF
        .'      <dd>'.LF
        .'        <input class="checkbox" type="radio" name="approved" value="1" ' . (($approved == TRUE) ? 'checked="checked"': '') . ' />' . props_gettext("True") . '&nbsp;'.LF
        .'        <input class="checkbox" type="radio" name="approved" value="0" ' . (($approved == FALSE) ? 'checked="checked"': '') . ' />' . props_gettext("False") . '&nbsp;'.LF
        .'      </dd>'.LF
        .((props_geterror('approved')) ? '      <dd>' . props_geterror('approved') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('rss_feed', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="rss_feed" />&nbsp;<label>' . props_gettext("RSS feed enabled") . '</label></dt>'.LF
        .'      <dd>'.LF
        .'        <input class="checkbox" type="radio" name="rss_feed" value="1" ' . (($rss_feed == TRUE) ? 'checked="checked"': '') . ' />' . props_gettext("True") . '&nbsp;'.LF
        .'        <input class="checkbox" type="radio" name="rss_feed" value="0" ' . (($rss_feed == FALSE) ? 'checked="checked"': '') . ' />' . props_gettext("False") . '&nbsp;'.LF
        .'      </dd>'.LF
        .((props_geterror('rss_feed')) ? '      <dd>' . props_geterror('rss_feed') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'    <dl>'.LF
        .'      <dt><input class="checkbox" ' . ((in_array('comments_enable', $include)) ? 'checked="checked"' : '') . ' type="checkbox" name="include[]" value="comments_enable" />&nbsp;<label>' . props_gettext("Comments enabled") . '</label></dt>'.LF
        .'      <dd>'.LF
        .'        <input class="checkbox" type="radio" name="comments_enable" value="1" ' . (($comments_enable == TRUE) ? 'checked="checked"': '') . ' />' . props_gettext("True") . '&nbsp;'.LF
        .'        <input class="checkbox" type="radio" name="comments_enable" value="0" ' . (($comments_enable == FALSE) ? 'checked="checked"': '') . ' />' . props_gettext("False") . '&nbsp;'.LF
        .'      </dd>'.LF
        .((props_geterror('comments_enable')) ? '      <dd>' . props_geterror('comments_enable') . '</dd>'.LF : '')
        .'    </dl>'.LF

        .'  </fieldset>'.LF
        .'  <p>'.LF
        .'    <input class="button" type="submit" value="' . props_gettext("Find stories") . '" />'.LF
        .'  </p>'.LF
        .'</form>'.LF;

    return $output;
}

/**
 * This generates the listboxes on the search form
 * @access  private
 */
function listbox(&$result, $value_column, $display_column, $size)
{
    $listbox = '<select class="large" name="' . $value_column . '[]" multiple="multiple" size="' . $size . '">'.LF;
    $array = props_getrequest($value_column, VALIDATE_ARRAY);

    while ($row = sql_fetch_object($result)) {
        $value = $row->$value_column;
        $display = $row->$display_column;

        If (!empty($value) && !empty($display)) {
            $select = (in_array($value, $array)) ? 'selected="selected"' : '';
            $listbox .= '<option ' . $select . ' value="' . htmlspecialchars($value) . '">' . htmlspecialchars($display) . '</option>'.LF;
        }
    }

    $listbox .= '</select>'.LF;

    sql_free_result($result);

    return $listbox;
}

/**
 * Returns the admin listboxes on the search form
 * @access  private
 */
function adminbox(&$adminlist, $fieldname, $size)
{
    $listbox = '<select class="large" name="' . $fieldname . '[]" multiple="multiple" size="' . $size . '">'.LF;
    $array = props_getrequest($fieldname, VALIDATE_ARRAY);

    foreach ($adminlist AS $user_id => $username) {
        $select = (in_array($user_id, $array)) ? 'selected="selected"' : '';

        $listbox .= '<option ' . $select . ' value="' . $user_id . '">' . $username . '</option>'.LF;
    }

    $listbox .= '</select>'.LF;

    return $listbox;
}

/**
 * Returns an uri to a search result
 * @access  private
 */
function search_results_uri($array = '')
{
    if (!is_array($array) || empty($array)) $array = array();
    $uri_array = array_merge($GLOBALS['PROPS_QUERY_ARRAY'], $array);
    $uri_array['module'] = $GLOBALS['PROPS_MODULE'];
    $uri_array['function'] = $GLOBALS['PROPS_FUNCTION'];
    return './?'.htmlspecialchars(http_build_query($uri_array));
}

?>
