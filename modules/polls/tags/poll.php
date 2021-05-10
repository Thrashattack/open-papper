<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  polls
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
 * @version     $Id: poll.php,v 1.18 2007/12/11 15:46:31 roufneck Exp $
 */

props_loadLib('url');

/**
 * Returns a poll
 *
 * @tag    {poll}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>section</b> - If a section shortname is supplied in this parameter, only polls
 *              assigned to this section will be output. Use the 'depth' parameter
 *              to include subsections as well.<br />
 *              If this parameter is omitted, it will default to the current section.
 *   </li>
 *   <li><b>depth</b> - When the 'section' parameter is set, this controls how many levels
 *              below the given section to include. Ex: If set to 0, only polls from
 *              the given section will be output. If set to 2, polls from children
 *              and grandchildren of the given section will be output as well. Default
 *              is 0.
 *   </li>
 *   <li><b>whichpoll</b> - which active poll to display. "latest" displays the most
 *              recently-added active poll.  "random" displays a random active
 *              poll. Default is "random.".
 *   </li>
 *   <li><b>optionformat</b> - valid tokens for optionformat string:
 *     <ul>
 *       <li>%n - option number</li>
 *       <li>%t - option text</li>
 *     </ul>
 *   </li>
 *   <li><b>format</b> - valid tokens for format string:
 *     <ul>
 *       <li>%i - poll ID #</li>
 *       <li>%q - question text</li>
 *       <li>%u - URL pointing to results page</li>
 *       <li>%o - output all non-blank poll options in the format specified by optionformat</li>
 *    </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_poll(&$params)
{
    // set parameter defaults
    if (!isset($params['format'])) {
        $params['format'] =
             '<form action="' . props_getkey('config.url.root') . '" method="post">'.LF
            .'  <input type="hidden" name="op" value="polls-vote" />'.LF
            .'  <input type="hidden" name="poll_id" value="%i" />'.LF
            .'  <h3>' . props_gettext("Current Poll") . '</h3>'.LF
            .'  <p class="smalltext">%q</p>'.LF
            .'    <table style="border:none;">'.LF
            .'      %o'.LF
            .'    </table>'.LF
            .'  <p><input class="button" type="submit" value="' . props_gettext("Submit Vote") . '" /></p>'.LF
            .'  <a class="smalltext" href="%u">' . props_gettext("View Results") . '</a>'.LF
            .'</form>'.LF;
    }

    if (!isset($params['optionformat'])) $params['optionformat'] = '<tr><td><input type="radio" name="poll_option" value="%n" /></td><td valign="top" class="smalltext">%t</td></tr>';
    if (!isset($params['section'])) $params['section'] = section_shortname(props_getkey('request.section_id'));
    if (!isset($params['depth'])) $params['depth'] = 0;
    if (!isset($params['whichpoll'])) $params['whichpoll'] = 'random';

    // Begin construction of SQL query
    $q = "SELECT * FROM props_polls WHERE poll_active = 1 ";

    // Assemble clause which restricts query to certain section_ids
    if (intval($params['depth']) > 0) {
        // If we're including subsections, get a list of them
        $sections_array = section_get_childs(section_id_of_shortname($params['section']), $params['depth']);
        $sections_array[] = section_id_of_shortname($params['section']); // include the given section
    } else {
        // No subsections.  Just the given section_id.
        $sections_array = array(section_id_of_shortname($params['section']));
    }

    // Add clause to SQL code which restricts search to certain sections
    $q .= " AND section_id IN (" . implode(",", $sections_array) . ") ";

    // Are we looking for the most recent poll, or a random one?
    if ($params['whichpoll'] == 'latest') {
        $order_by = 'poll_id DESC';
    } else {
        $order_by = 'RAND()';
    }

    $q .= "ORDER BY $order_by LIMIT 1";
    $result = sql_query($q);

    // If there is not a current poll, stop here and return altoutput
    if (sql_num_rows($result) == 0) {
        return '';
    }

    $row = sql_fetch_object($result);

    // assemble options string
    $options_string = '';
    for ($i = 1; $i <= 10; $i++) {
        $code  = "if (trim(\$row->poll_option_$i) != \"\") {\n";
        $code .= "\t\$this_option_string = \$params[\"optionformat\"];\n";
        $code .= "\t\$this_option_string = str_replace(\"%n\", $i, \$this_option_string);\n";
        $code .= "\t\$this_option_string = str_replace(\"%t\", htmlspecialchars(\$row->poll_option_$i), \$this_option_string);\n";
        $code .= "\t\$options_string .= \$this_option_string;\n";
        $code .= "}\n\n";
        eval($code);
    }

    // Replace format tokens with poll output
    $output = $params['format'];
    $output = str_replace('%o', $options_string, $output);
    $output = str_replace('%i', $row->poll_id, $output);
    $output = str_replace('%q', htmlspecialchars($row->poll_question), $output);
    $urlargs = array('cmd' => 'polls', 'poll_id' => $row->poll_id);
    $output = str_replace('%u', genurl($urlargs), $output);

    // Return the final output
    return $output;
}

?>
