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
 * @version     $Id: poll_results.php,v 1.14 2007/12/11 15:46:31 roufneck Exp $
 */

/**
 * Returns results for the specified poll_id
 *
 * @tag    {poll_results}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>format</b> - valid tokens:
 *     <ul>
 *       <li>%i - poll ID #</li>
 *       <li>%q - question text</li>
 *       <li>%o - output all non-blank poll options in the format specified by optionformat</li>
 *       <li>%u - URL pointing to results page</li>
 *     </ul>
 *   </li>
 *   <li><b>optionformat</b> - valid tokens:
 *     <ul>
 *       <li>%n - option number</li>
 *       <li>%t - option text</li>
 *       <li>%v - number of votes</li>
 *       <li>%p - percentage of votes</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_poll_results(&$params)
{
    // Set parameter defaults
    if (!isset($params['format'])) $params['format'] = '<b>%q</b><br /><br />%o'; // default format
    if (!isset($params['optionformat'])) $params['optionformat'] = '%t - %v ' . props_gettext('votes') . ' (%p%)<br />'; // default optionformat

    // If query returns nothing, stop here and return altoutput
    if (props_getkey('request.poll_id') < 1) {
        return '';
    }

    // Replace format tokens with poll output
    $output = $params['format'];
    $output = str_replace("%i", props_getkey('polls.poll_id'), $output);
    $output = str_replace("%q", htmlspecialchars(props_getkey('polls.poll_question')), $output);

    $total_votes =
        props_getkey('polls.poll_option_1_votes') +
        props_getkey('polls.poll_option_2_votes') +
        props_getkey('polls.poll_option_3_votes') +
        props_getkey('polls.poll_option_4_votes') +
        props_getkey('polls.poll_option_5_votes') +
        props_getkey('polls.poll_option_6_votes') +
        props_getkey('polls.poll_option_7_votes') +
        props_getkey('polls.poll_option_8_votes') +
        props_getkey('polls.poll_option_9_votes') +
        props_getkey('polls.poll_option_10_votes');

    if ($total_votes == 0) {
        $total_votes = 1; // Prevent div by zero errors
    }

    // Assemble options string
    $options_string = "";
    for ($i = 1; $i <= 10; $i++) {
        if (props_getkey('polls.poll_option_'.$i)) {
            $this_option_string = $params['optionformat'];
            $this_option_string = str_replace("%n", $i, $this_option_string);
            $this_option_string = str_replace("%v", props_getkey('polls.poll_option_'.$i.'_votes'), $this_option_string);
            $this_option_string = str_replace("%p", round((props_getkey('polls.poll_option_'.$i.'_votes') / $total_votes) * 100), $this_option_string);
            $this_option_string = str_replace("%t", htmlspecialchars(props_getkey('polls.poll_option_'.$i)), $this_option_string);
            $options_string .= $this_option_string.LF;
        }
    }

    $output = str_replace('%o', $options_string, $output);
    $urlargs = array('cmd' => 'polls-showresults', 'poll_id' => props_getkey('polls.poll_id'));
    $output = str_replace('%u', genurl($urlargs), $output);

    // Return the final output
    return $output;
}

?>
