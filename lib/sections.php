<?php
/**
 * Lib - sections functions
 *
 * @package     api
 * @subpackage  sections
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
 * @version     $Id: sections.php,v 1.26 2008/01/09 17:49:36 roufneck Exp $
 */

// Load data on all sections into a global array.  This is then accessed by
// all the functions below, to reduce load on the database
sections_init();

/**
 * Reloads the sections array
 *
 * Must be called immediately after any changes are made in the db.
 */
function sections_reload()
{
    unset($GLOBALS['PROPS_SECTIONS']);
    sections_init();
}

/**
 * Populates the sections array with values from the db
 */
function sections_init() {

    $GLOBALS['PROPS_SECTIONS'] = array();

    $q  = "SELECT section_id, parent_id, fullname, shortname, sortorder "
        . "FROM props_sections "
        . "ORDER BY parent_id, sortorder";
    $result = sql_query($q);

    while ($row = sql_fetch_assoc($result)) {
        $GLOBALS['PROPS_SECTIONS'][$row['section_id']] = $row;
    }

    // Free memory
    sql_free_result($result);
}

/**
 * Checks if a section is valid
 * @param   int   $section_id
 * @return  bool  TRUE on success, FALSE on failure
 */
function section_is_valid($section_id)
{
    if ($GLOBALS['PROPS_SECTIONS'][$section_id]['section_id'] == $section_id) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Returns the path to the directory containing templates of a section
 * @param   int     $section_id
 * @return  string  template dir
 */
function section_template_directory($section_id)
{
    if ($section_id == FRONTPAGE_SECTION_ID) {
        return props_getkey('config.dir.templates');
    } else {
        // Walk up the tree, tacking each parent onto the beginning of the path
        $path = '';
        do {
            if ($path) $path = "/$path";
            $path = $GLOBALS['PROPS_SECTIONS'][$section_id]['shortname'] . $path;
            $section_id = $GLOBALS['PROPS_SECTIONS'][$section_id]['parent_id'];
        } while($section_id != FRONTPAGE_SECTION_ID);

        return props_getkey('config.dir.templates') . '/' . $path;
    }
}

/**
 * Returns the sortorder of a section
 * @param   int  $section_id
 * @return  int  sortorder
 */
function section_sortorder($section_id)
{
    return $GLOBALS['PROPS_SECTIONS'][$section_id]['sortorder'];
}

/**
 * Returns the parent_id of a section
 * @param   int  $section_id
 * @return  int  parent_id
 */
function section_parent_id($section_id)
{
    return $GLOBALS['PROPS_SECTIONS'][$section_id]['parent_id'];
}

/**
 * Returns the id of the child section in position 'x'
 * where 'x' is the sortorder
 */
function section_child_id($section_id, $position)
{
    $childcount = 0;

    $sectionlist = array_keys($GLOBALS['PROPS_SECTIONS']);
    foreach($sectionlist as $this_section_id) {
        if ($GLOBALS['PROPS_SECTIONS'][$this_section_id]['parent_id'] == $section_id) {
            $childcount++;
            if ($childcount == $position) {
                return $this_section_id;
            }
        }
    }
    
    // Error, section not found
    return NULL;
}

/**
 * Returns the short name of a section
 * @param   int     $section_id
 * @return  string  shortname
 */
function section_shortname($section_id)
{
    return $GLOBALS['PROPS_SECTIONS'][$section_id]['shortname'];
}

/**
 * Returns the full name of a section
 * @param   int     $section_id
 * @return  string  fullname
 */
function section_fullname($section_id)
{
    if (!empty($section_id)) {
        return $GLOBALS['PROPS_SECTIONS'][$section_id]['fullname'];
    } else {
        return '';
    }
}

/**
 * Returns the static content of a section
 * @param   int     $section_id
 * @return  string  static_content
 */
function section_static_content($section_id)
{
    $q  = "SELECT static_content "
        . "FROM props_sections "
        . "WHERE section_id = $section_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    return $row->static_content;
}

/**
 * Returns the section_id of a shortname
 * @param   string  $shortname
 * @return  int     section_id
 */
function section_id_of_shortname($shortname)
{
    // There's gotta be some array function in PHP that will do this
    // automagically, but I can't find it.
    $sectionlist = array_keys($GLOBALS['PROPS_SECTIONS']);
    foreach($sectionlist as $this_section_id) {
        if (section_shortname($this_section_id) == $shortname) {
            return $this_section_id;
        }
    }

    trigger_error("Invalid section: '$shortname'", E_USER_ERROR);
}

/**
 * Returns the amount of children of a section
 * @param   int  $section_id
 * @return  int  child count
 */
function section_num_childs($section_id)
{
    $childcount = 0;

    // There's gotta be some array function in PHP that will do this
    // automagically, but I can't find it.
    $sectionlist = array_keys($GLOBALS['PROPS_SECTIONS']);
    foreach($sectionlist as $this_section_id) {
        if ($GLOBALS['PROPS_SECTIONS'][$this_section_id]['parent_id'] == $section_id) {
            $childcount++;
        }
    }

    return($childcount);
}

/**
 * Returns a dropdown select menu allowing selection of a section
 *
 * @param   int     $selected_section_id  selected section_id
 * @param   string  $keyname              name and id of select element
 * @param   int     $exclude_children_of  exclude children of this section id
 * @return  string  select element html code
 */
function section_select($selected_section_id, $keyname = 'section_id', $optional = FALSE, $exclude_children_of = 0)
{
    // This simply calls the helper function below, which does all the dirty work
    $output = section_select_hierarchy(0, $selected_section_id, $keyname, $optional, $exclude_children_of);

    return $output;
}

/**
 * Recursive construct the section hierarchy in a selectbox
 * @access  private
 */
function section_select_hierarchy($pid, $selected_section_id, $keyname, $optional, $exclude_children_of)
{
    $hierarchy = '';

    // Open select
    if ($pid == 0) {
        $hierarchy .= '<select class="large" id="' . $keyname . '" name="' . $keyname . '">'.LF;
        if ($optional == TRUE) {
            $hierarchy .= '<option value="">' . props_gettext("Not assigned") . '</option>'.LF;
        }
    }

    foreach ($GLOBALS['PROPS_SECTIONS'] as $sid => $value) {
        if ($GLOBALS['PROPS_SECTIONS'][$sid]['parent_id'] == $pid) {
            //echo "pid:$pid; sid:$sid; sortorder:".section_sortorder($sid)."; childs:".section_num_childs($pid).";".BR;

            // output this section
            $selected = ($sid == $selected_section_id) ? 'selected="selected"' : '';

            $hierarchy .=
                '<option ' . $selected . ' value="' . $sid . '">'
                . section_name_path($sid) . '</option>'.LF;

            // If this section has children, add them
            if (section_num_childs($sid) > 0) {
                $hierarchy .= section_select_hierarchy($sid, $selected_section_id, $keyname, $optional, $exclude_children_of);
            }
        }
    }

    // Close select
    if ($pid == 0) {
        $hierarchy .= '</select>'.LF;
    }

    return $hierarchy;
}

/**
 * Returns the section path for a given section id
 *
 * Example:
 * <code>
 * Front page : Sports : Local
 * </code>
 *
 * @return  string  section path
 */
function section_name_path($section_id)
{
    $section_name_path = section_fullname($section_id);

    while ($section_id = section_parent_id($section_id)) {
        $section_name_path = section_fullname($section_id) . " : " . $section_name_path;
    }

    return $section_name_path;
}

/**
 * Returns the number of stories of the section
 *
 * Note that this number does not distinguish between published & unpublished
 * stories, etc.
 *
 * @param   int  $section_id
 * @return  int  story count
 */
function section_num_stories($section_id)
{
    $q  = "SELECT COUNT(*) AS story_count FROM props_stories "
        . "WHERE section_id = $section_id";
    $result = sql_query($q);
    $row = sql_fetch_object($result);

    return $row->story_count;
}

/**
 * Auto archive stories
 *
 * This updates the access level of stories in sections where auto-archiving
 * is enabled, after the specified number of days. It should be called whenever
 * a new edition is published, or randomly during page loads via the index.php
 * interpreter.
 */
function sections_auto_archive()
{
    // Loop through a list of all sections in which auto-archiving is enabled
    $q  = "SELECT section_id, auto_archive_access_level, auto_archive_days "
        . "FROM props_sections "
        . "WHERE auto_archive_enabled";
    $result = sql_query($q);
    while ($srow = sql_fetch_object($result)) {
        // Update the access level of stories in this section which have
        // reached the proper age
        $q  = "UPDATE props_stories "
            . "SET access_level = $srow->auto_archive_access_level "
            . "WHERE publication_status_id IN (" . PUBSTATUS_PUBLISHED . "," . PUBSTATUS_ARCHIVED . ") "
            . "AND section_id = $srow->section_id "
            . "AND published_stamp < DATE_SUB(NOW(), INTERVAL $srow->auto_archive_days DAY)";
        sql_query($q);
    }
}

/**
 * Returns section_ids for all children of a section
 *
 * @param   int    $section_id
 * @param   int    $max_depth
 * @return  array  children of section
 */
function section_get_childs($section_id, $max_depth)
{
    $sections_array = array();

    $sections_array = section_do_get_childs($section_id, $sections_array, 0, $max_depth);

    return $sections_array;
}

/**
 * Recursive helper function for above
 * @access  private
 */
function section_do_get_childs($current_section_id, $sections_array, $current_depth, $max_depth)
{
    $current_depth++;

    // If we have reached the maximum depth, return now
    if ($current_depth > $max_depth) {
        return $sections_array;
    }

    // Loop through all children of the $current_section_id
    for ($i = 1; $i <= section_num_childs($current_section_id); $i++) {
        // Get the section_id of this child
        $child_section_id = child_section($current_section_id, $i);
        // Add this child section to the array
        $sections_array[] = $child_section_id;
        // Recurse thyself for this section
        $sections_array = section_do_get_childs($child_section_id, $sections_array, $current_depth, $max_depth);
    }

    // Return the newly-modified $sections_array
    return $sections_array;
}

/**
 * Returns a html unordered section list
 *
 * Example:
 * <code>
 * sections_draw_hierarchy();
 * // returns
 * <ul>
 *   <li>frontpage
 *     <ul>
 *       <li>sports</li>
 *       <li>opinion</li>
 *     </ul>
 *   </li>
 * </ul>
 * </code>
 *
 * @param   int     $current_parent
 * @param   int     $current_depth
 * @return  string  generated html code
 */
function sections_draw_hierarchy($current_parent = 0, $current_depth = 0)
{
    $hierarchy = '';
    foreach ($GLOBALS['PROPS_SECTIONS'] as $sid => $value) {
        if ($GLOBALS['PROPS_SECTIONS'][$sid]['parent_id'] == $current_parent) {
            //echo $current_parent.':'.$sid.':'.section_sortorder($sid).':'.section_num_childs($current_parent).BR;

            // Output the section name (with a link if it's not the front page)
            if ($sid == FRONTPAGE_SECTION_ID) {
                $hierarchy .= '<li id="node.' . $sid . '" noDrag="true" noSiblings="true"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=section_edit&amp;section_id=' . $sid . '">' . htmlspecialchars($GLOBALS['PROPS_SECTIONS'][$sid]['fullname']) . '</a>';
            } else {
                $hierarchy .= '<li id="node.' . $sid . '"><a href="./?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=section_edit&amp;section_id=' . $sid . '">' . htmlspecialchars($GLOBALS['PROPS_SECTIONS'][$sid]['fullname']) . '</a>';
            }

            // If this section has children, add them
            if (section_num_childs($sid) > 0) {
                $hierarchy .=
                    LF.'<ul>'.LF
                    .sections_draw_hierarchy($sid, $current_depth+1)
                    .'</ul>'.LF;
            }

            // Close section
            $hierarchy .=
                     '</li>'.LF;
        }
    }
    return $hierarchy;
}

/**
 * Returns a new sortorder
 *
 * @return  int  new sortorder
 */
function section_get_new_sortorder()
{
    $sortorder = 0;
    foreach($GLOBALS['PROPS_SECTIONS'] as $section_id => $value) {
        if ($GLOBALS['PROPS_SECTIONS'][$section_id]['sortorder'] > $sortorder) {
            $sortorder = $GLOBALS['PROPS_SECTIONS'][$section_id]['sortorder'];
        }
    }
    return $sortorder + 1;
}



/**
 * Old functions. I left these here just in case.
 *

// Returns a number indicating how many siblings (children of the same parent)
// a given section_id has
function num_sibling_sections($section_id)
{
    $my_parent_id = section_parent_id($section_id);
    $siblingcount = 0;

    // There's gotta be some array function in PHP that will do this
    // automagically, but I can't find it.
    $sectionlist = array_keys($GLOBALS['PROPS_SECTIONS']);
    foreach($sectionlist as $this_section_id) {
        if (($this_section_id != $section_id) &&
            ($GLOBALS['PROPS_SECTIONS'][$this_section_id]['parent_id'] == $my_parent_id)) {
            $siblingcount++;
        }
    }

    return $siblingcount;
}



// This wrapper function returns the depth of the sections
// tree (i.e. how many levels down does the tree go?)
function sections_tree_depth()
{
    // This simply calls the recursive helper function below,
    // which does the real work
    return do_tree_depth(FRONTPAGE_SECTION_ID, 0, 0);
}

function do_tree_depth($current_section_id, $current_depth, $max_depth_so_far)
{
    $current_depth++;

    if ($current_depth > $max_depth_so_far) {
        $max_depth_so_far = $current_depth;
    }

    for ($i = 1; $i <= section_num_childs($current_section_id); $i++) {
        $max_depth_so_far = do_tree_depth(child_section($current_section_id, $i),
        $current_depth, $max_depth_so_far);
    }

    return $max_depth_so_far;
}



*/

?>
