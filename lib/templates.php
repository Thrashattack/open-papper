<?php
/**
 * Lib - templates functions
 *
 * @package     api
 * @subpackage  templates
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
 * @version     $Id: templates.php,v 1.37 2007/12/11 15:46:28 roufneck Exp $
 */

// loadLibs
props_loadLib('sections');

/**
 * Load the tag registry
 */
include(props_getkey('config.dir.modules') . 'tag_registry.php');

$GLOBALS['CONTROLSTACK'] = array();

/**
 * Returns the content of a template
 *
 * This function searches for a template in this order:
 * - $template.$format
 * - $module-$function.$format
 * - $module.$format
 * - if story_id -> displaystory.$format
 * - displaysection.$format
 *
 * @return  string  template content
 */
function template_get_content($section_id, $command, $format, $template)
{
    $cmd = explode('-', props_getkey('request.cmd'), 2);

    $module = (isset($cmd[0])) ? $cmd[0] : '';
    $function = (isset($cmd[1])) ? $cmd[1] : '';

    $template_path = FALSE;
    if (!empty($template)) {
        $template_path = template_get_path($section_id, $template.'.'.$format);
    }

    if ($template_path === FALSE && !empty($module) && !empty($function)) {
        $template_path = template_get_path($section_id, $command.'.'.$format);
    }

    if ($template_path === FALSE && !empty($module)) {
        $template_path = template_get_path($section_id, $module.'.'.$format);
    }

    if ($template_path === FALSE && props_getkey('request.story_id')) {
        $template_path = template_get_path($section_id, 'displaystory.'.$format);
    }

    if ($template_path === FALSE) {
        $template_path = template_get_path($section_id, 'displaysection.'.$format);
    }

    if ($template_path !== FALSE) {
        return file_get_contents($template_path);
    }

    trigger_error('Cannot locate template ' . $command . '.' . $format . '\'', E_USER_ERROR);
    exit;
}

/**
 * Returns a template path
 *
 * This function walks the templates directory from a section up to the
 * root, and returns a path to the first available template.
 *
 * @param   int     $section_id
 * @param   string  $template    template name (e.g. 'users-login.html')
 * @return  string  template path
 */
function template_get_path($section_id, $template)
{
    // Walk the sections tree from the given section_id up to the root (front page)
    // looking for a template matching the given parameters
    do {
        // Construct a path to the template
        $template_path = section_template_directory($section_id) . '/' . $template;

        // If this template exists, return it, or exit if we reach the top
        // of the templates dir without finding a suitable template
        if (file_exists($template_path)) {
            return $template_path;
        }

        if ($section_id == FRONTPAGE_SECTION_ID) {
            return FALSE;
        }

        // No luck yet, let's try the parent directory
        $section_id = section_parent_id($section_id);

    } while(TRUE);
}

/**
 * Checks for a valid tag
 *
 * @access  private
 * @param   string  $tag
 * @return  bool    TRUE on success, FALSE on failure
 */
function template_valid_tag($tag) {

    global $TAG; // globalizes the array which was loaded from tag_registry.php

    if (isset($TAG["$tag"])) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Execute a tag function
 *
 * Given a tag name (ex: storylist) and its arguments (ex: section="sports")
 * this will parse the tag's attributes, execute the appropriate tag function,
 * and return the output. The requested tag must be valid.
 *
 * Parameter 'user_logged_in' can be added any time. Options are true and
 * false. If set to true, it executes when a user is logged in. If set to
 * false, it executes when no user is logged in.
 *
 * Example:
 * <code>
 * // Display username only when logged in
 * {user_name user_logged_in="true"}
 * </code>
 *
 * @access  private
 */
function template_exec_tag($tag, $args)
{
    global $TAG; // globalizes the array which was loaded from tag_registry.php

    // Load the tag function if it is registered
    if (isset($TAG[$tag])) {
        require_once(props_getkey('config.dir.modules') . $TAG[$tag] . '/tags/' . $tag . '.php');
    } else {
        return FALSE;
    }

    // Check logged_in option
    if (isset($args['user_logged_in'])) {
        if ($args['user_logged_in'] == 'true' && !isset($_SESSION['PROPS_USER']['authenticated'])) {
            return FALSE;
        } elseif ($args['user_logged_in'] == 'false' && isset($_SESSION['PROPS_USER']['authenticated'])) {
            return FALSE;
        }
    }

    // Tag is registered but the function was not successfully defined
    // when we loaded the tag file.  Either the file is missing or
    // there is a PHP error in the tag function which is
    // preventing it from being defined.  This is a fatal error,
    // so output an error and exit.
    if (!function_exists('tag_' . $tag)) {
        trigger_error('Unable to load ' . $tag . ' tag.', E_USER_ERROR);
        exit;
    }

    // Split the tag's attributes into an array containing
    // name/value pairs for all tag attributes, e.g.
    // {sectionlist depth="2"} would result in
    // $params["depth"] = 2
    $params = template_args_to_array($args);

    // Prevent notices
    if (!isset($params['prepend'])) $params['prepend'] = '';
    if (!isset($params['append'])) $params['append'] = '';
    if (!isset($params['altoutput'])) $params['altoutput'] = '';

    // Call tag function
    // Tag functions must have this format: tag_name(&$params)
    // $params must be a reference value, so we can set default
    // values in the tag function and handle output results here.
    $tag_function = 'tag_'.$tag;
    $output = $tag_function($params);

    // Output results
    if ($output) {
        return $params['prepend'] . $output . $params['append'];
    } else {
        return $params['altoutput'];
    }
}

/*

$GLOBALS['PROPS_TEMPLATE_SKIP'] = FALSE;
$GLOBALS['PROPS_TEMPLATE_LEVEL'] = 0;

// Something to try out
function template_parse_($input)
{
    $regex = '/\{([^\}]+)\}/';

echo '<pre>'.BR;
print_r($GLOBALS['CONTROLSTACK']);
echo '</pre>'.BR;

    if (is_array($input)) {

        // Split each match apart into tagname and args-string
        $segment = explode(' ', $input[1], 2);
        $tag = $segment[0];
        $params = (isset($segment[1])) ? $segment[1] : '';
echo "TAG $tag :: ".htmlspecialchars($params).BR;
        switch ($tag) {
            case 'if':

                $GLOBALS['PROPS_TEMPLATE_LEVEL']++;
echo "[".__LINE__."] ".$GLOBALS['PROPS_TEMPLATE_LEVEL']." $tag $params".BR;

                if (!template_evaluate_if_tag($params)) {
                    array_push($GLOBALS['CONTROLSTACK'], array('if',TRUE,$GLOBALS['PROPS_TEMPLATE_LEVEL']));
                    return 'DELETE_'.$GLOBALS['PROPS_TEMPLATE_LEVEL'];
                } else {
                    array_push($GLOBALS['CONTROLSTACK'], array('if',FALSE,$GLOBALS['PROPS_TEMPLATE_LEVEL']));
                    return;
                }

                break;

            case 'elseif':

echo "[".__LINE__."] ".$GLOBALS['PROPS_TEMPLATE_LEVEL']." $tag $params".BR;

                if (!template_evaluate_if_tag($params)) {
                    array_push($GLOBALS['CONTROLSTACK'], array('elseif',TRUE,$GLOBALS['PROPS_TEMPLATE_LEVEL']));
                    return 'DELETE_'.$GLOBALS['PROPS_TEMPLATE_LEVEL'];
                } else {
                    array_push($GLOBALS['CONTROLSTACK'], array('elseif',FALSE,$GLOBALS['PROPS_TEMPLATE_LEVEL']));
                    return;
                }

                break;

            case 'else':

echo "[".__LINE__."] ".$GLOBALS['PROPS_TEMPLATE_LEVEL']." $tag $params".BR;

                array_push($GLOBALS['CONTROLSTACK'], array('else',FALSE,$GLOBALS['PROPS_TEMPLATE_LEVEL']));

                return;
                break;

            case 'endif':

echo "[".__LINE__."] ".$GLOBALS['PROPS_TEMPLATE_LEVEL']." $tag $params".BR;

                $GLOBALS['PROPS_TEMPLATE_LEVEL']--;
                array_push($GLOBALS['CONTROLSTACK'], array('endif',FALSE,$GLOBALS['PROPS_TEMPLATE_LEVEL']));

                return;
                break;

            default:
                if (isset($GLOBALS['TAG'][$tag])) {
                    return template_exec_tag($tag, $params);
                } else {
                    return "\{$tag $params\}";
                }
                break;
        }

        //if (isset($TAG["$tag"])) {
        //    return template_exec_tag($tag, $params);
        //}
        //$input = "";

        //return "$tag :: ".htmlspecialchars($params).BR;
    } else {
        $input = template_parse_includes($input);
    }

    return preg_replace_callback($regex, 'template_parse', $input);
}
/**/

/**
 * Parse template
 *
 * This is where the content of the site meets the look and feel of the
 * site. This is, in other words, the abstraction layer between the site's
 * data and presentation. Pass this function a string containing a template,
 * and it will merge data from the registry into the template and return a
 * string containing page contents, ready for output to the end user.
 *
 * @param   string  $template_content  content of a template
 */
function template_parse($template_content)
{
    $stack = array();
    $level = 0;
    $output = '';

    // First, expand all {include} tags to snippets of code
    // contained in .inc files
    $template_content = template_parse_includes($template_content);

    // Now go through the template content left-to-right and process
    // each tag encountered
    while(strlen($template_content)) {
        // Locate position of next tag
        $result = preg_match(
            '/\{([^\}]+)\}/',
            $template_content,
            $matches_array,
            PREG_OFFSET_CAPTURE);

        // If no tag match was found, output remaining content and break
        // from this while loop
        if (!$result) {
            $output .= $template_content;
            break;
        }

        // If we reach this point it means we did find a match
        $match_pos = $matches_array[0][1];              // beginning of {tag}
        $match_length = strlen($matches_array[0][0]);   // length of {tag}
        $match_string = $matches_array[0][0];           // Includes the outer {}
        $match_content = $matches_array[1][0];          // Everything inside the {}

        // Otput all template content which comes before the tag match
        $output .= substr($template_content, 0, $match_pos);

        // Trim the beginning of the string to get rid of everything we just output,
        // as well as the tag (which we will process below)
        $template_content = substr($template_content, $match_pos + $match_length);

        // Now figure out what type of tag we encountered, and act accordingly

        // Is it a comment?
        if (preg_match('/^!--/', $match_content)) {
            $output .= ''; // Ignore comment and don't output it
        } else {
            // Split each match apart into tagname and args-string
            $segment = explode(' ', $match_content, 2);
            $tag = $segment[0];
            $args_string = (isset($segment[1])) ? $segment[1] : '';

            // If it's a registered tag, execute it
            if (template_valid_tag($tag)) {
                $output .= template_exec_tag($tag, $args_string);
            } else {
                // It's not registered, so maybe it's an if/else/endif tag
                switch($tag) {

                    case 'if':

                        // Evaluate the if
                        $result = template_evaluate_if_tag($args_string);
                        $level++;
                        // If it evaluated FALSE, skip to the next {elseif}, {else} or {endif}
                        if (!$result) {
                            // Update the stack and skip to next elseif/else/endif
                            array_push($stack, $level.':SKIP');
                            $skip_pos = template_parse_control_structures($template_content);
                            if ($skip_pos === FALSE) {
                                trigger_error('{if} tag is missing matching {elseif}, {else} or {endif} tag.', E_USER_ERROR);
                            } else {
                                $template_content = substr($template_content, $skip_pos + 0);
                            }
                        } else {
                            // Else execute th if tag and update the stack
                            array_push($stack, $level.':EXEC');
                        }

                        break;

                    case 'elseif':

                        // Evaluate the elseif if no other if/elseif was executed before
                        $last = end($stack);
                        if ($last == $level.':SKIP') {
                            $result = template_evaluate_if_tag($args_string);
                            // If it evaluated FALSE, skip to the next {elseif}, {else} or {endif}
                            if (!$result) {
                                $skip_pos = template_parse_control_structures($template_content);
                                if ($skip_pos === FALSE) {
                                    trigger_error('{if} tag is missing matching {elseif}, {else} or {endif} tag.', E_USER_ERROR);
                                } else {
                                    $template_content = substr($template_content, $skip_pos + 0);
                                }
                            } else {
                                // Update stack
                                array_pop($stack);
                                array_push($stack, $level.':EXEC');
                            }
                        } else {
                            // Already executed an if/elseif tag, skip to the next {elseif}, {else} or {endif}
                            $skip_pos = template_parse_control_structures($template_content);
                            if ($skip_pos === FALSE) {
                                trigger_error('{if} tag is missing matching {elseif}, {else} or {endif} tag.', E_USER_ERROR);
                            } else {
                                $template_content = substr($template_content, $skip_pos + 0);
                            }
                        }

                        break;

                    case 'else':

                        // Skip if we already executed an if/elseif tag
                        $last = end($stack);
                        if ($last == $level.':EXEC') {

                            // Skip to next {endif} tag
                            $endif_pos = template_parse_control_structures($template_content);

                            if (!$endif_pos) {
                                trigger_error('{else} tag is missing matching {endif} tag.', E_USER_ERROR);
                            } else {
                                $template_content = substr($template_content, $endif_pos);
                            }
                        }

                        break;

                    case 'endif':

                        $last = explode(':', end($stack));
                        if ($last[0] == $level) {
                            // Update level and stack
                            $level--;
                            array_pop($stack);
                        }

                        // Don't output anything
                        break;

                    default:
                        // It is not a comment, registered PROPS tag, or
                        // if/else/endif tag, so just regurgitate it
                        $output .= $match_string;
                        break;
                }
            }
        }
    }

    // Return content
    return $output;
}

/**
 * Split the tag's attributes
 *
 * The preference for single or double quotes can be set in the config option:
 * 'config.template.singlequotes'.
 *
 * Split the tag's attributes into an array containing name/value pairs for all
 * tag attributes.
 *
 * Example:
 * <code>
 * // Set $params['format'] = '<a href="blah">blah</a>'
 * // and $params['depth'] = '2'
 * {tag_name format='<a href="blah">blah</a>' depth='2'}
 *
 * // Escape JavaScript quotes
 * {storylist format='<a onclick="addMyStory(\'%i\',\'%u\',\'%h\')" href="javascript:void(0)">test</a>'}
 *
 * @access  private
 */
function template_args_to_array($args)
{
    // Set empty array. Otherwise you get errors.
    $params = array();

    // Don't do empty args
    if (!empty($args)) {
        // Use single quotes '
        if (props_getkey('config.template.singlequotes')) {
            // Replace escaped quotes \'
            $argsb = str_replace("\'","<=REP=>",$args);
            // Get params
            preg_match_all("/([^=]+)=\'([^\']+)\'/", $argsb, $nvpairs_array);
            // Assign params and restore original params
            $max = count($nvpairs_array[0]);
            for ($i=0; $i<$max; $i++) {
                $params[trim($nvpairs_array[1][$i])] = str_replace("<=REP=>","'",$nvpairs_array[2][$i]);
            }
        // Use double quotes "
        } else {
            // Replace escaped quotes \"
            $argsb = str_replace("\\\"","<=REP=>",$args);
            // Get params
            preg_match_all("/([^=]+)=\"([^\"]+)\"/", $argsb, $nvpairs_array);
            // Assign params and restore original params
            $max = count($nvpairs_array[0]);
            for ($i=0; $i<$max; $i++) {
                $params[trim($nvpairs_array[1][$i])] =
                str_replace("<=REP=>","\"",$nvpairs_array[2][$i]);
            }
        }
    }

    return $params;
}

/**
 * evaluate the if tag
 *
 * Parameter 'user_logged_in' can be added to a comparison or as standalone.
 * Options are true and false. If set to true, it executes when a user is
 * logged in. If set to false, it executes when no user is logged in.
 *
 * Example:
 * <code>
 * // evaluate the if tag and return TRUE or FALSE with a request var
 * {if var='anonymous_comments_allowed' comparison='eq' value='1'}
 *
 * // evaluate the if tag and return TRUE or FALSE with a registry key
 * {if key='poll.comments' comparison='eq' value='1'}
 *
 * // evaluate the if tag and return TRUE if user is logged in
 * {if user_logged_in='true'}
 *
 * // evaluate the if tag and return TRUE if user has the priv
 * {if user_has_priv='module-function'}
 *
 * // display login form when not logged in
 * {if user_logged_in='false'}
 *   display login form
 * {else}
 *   display user info
 * {endif}
 * </code>
 */
function template_evaluate_if_tag($args_string)
{
    $params = template_args_to_array($args_string);
    if (!isset($params['value'])) $params['value'] = '';

    // Check logged_in option
    if (isset($params['user_logged_in'])) {
        if ($params['user_logged_in'] == 'true' && !isset($_SESSION['PROPS_USER']['authenticated'])) {
            return FALSE;
        } elseif ($params['user_logged_in'] == 'false' && isset($_SESSION['PROPS_USER']['authenticated'])) {
            return FALSE;
        }

        if (!isset($params['comparison'])) {
            // Only asked for a login check. Return TRUE at this point.
            return TRUE;
        }
    }

    // Check if user has a priv
    if (isset($params['user_has_priv'])) {
        list($module, $function) = explode('-', $params['user_has_priv'], 3);

        return user_has_priv($module, $function);
    }

    // Check if admin has a priv
    if (isset($params['admin_has_priv'])) {
        list($module, $function) = explode('-', $params['admin_has_priv'], 3);

        return admin_has_priv($module, $function);
    }

    if (!isset($params['key']) && !isset($params['var'])) {
        trigger_error("Missing 'key' or 'var' for if comparison tag.", E_USER_ERROR);
    }
    $var = (isset($params['key'])) ? props_getkey($params['key']) : props_getkey('request.'.$params['var']);

    // Make the requested comparison between $var and value
    switch($params['comparison']) {
        case '==':
        case 'eq':
            $result = ( $var == $params['value'] );
            break;
        case '>=':
        case 'ge':
            $result = ( $var >= $params['value'] );
            break;
        case '>':
        case 'gt':
            $result = ( $var > $params['value'] );
            break;
        case '<=':
        case 'le':
            $result = ( $var <= $params['value'] );
            break;
        case '<':
        case 'lt':
            $result = ( $var < $params['value'] );
            break;
        case '!=':
        case 'ne':
            $result = ( $var != $params['value']);
            break;
    }

    return $result;
}

/**
 * detect control structures
 *
 * Whenever template_parse finds an {if} tag
 * which evaluates as FALSE, it calls this function to find
 * the end of the block. Given the current $template_content
 * string, this function returns the position of the next
 * {else} or {endif} tag. It takes into account any
 * nested if/else/endif structures it may encounter along the
 * way.
 */
function template_parse_control_structures($template_content)
{
    $level = 0;
    $match_pos = 0;

    // Find next if/else/endif tag
    $result = preg_match_all("/\{if[^\}]+\}|\{elseif[^\}]+\}|\{else\}|\{endif\}/",
        $template_content,
        $matches_array,
        PREG_OFFSET_CAPTURE,
        $match_pos);

    if (!$result) {
        return FALSE; // Nothing matched
    }

    foreach ($matches_array[0] as $match) {

        $match_pos = $match[1];
        $match_length = strlen($match[0]);
        $match_string = $match[0];       // Includes the outer {}
        //$match_content = substr($match[0], 1, ($match_length - 2));     // Everything inside the {}
        $match_content = substr($match[0], 1, -1);     // Everything inside the {}

        if (substr($match_content, 0, 3) == 'if ') {
//echo "[".__LINE__."] - $level:IF".BR;
            $level++;
        }

        if (substr($match_content, 0, 7) == 'elseif ') {
//echo "[".__LINE__."] - $level:ELSEIF".BR;
            if ($level == 0) {
                return ($match_pos);
            }
        }

        if ($match_content == 'else') {
//echo "[".__LINE__."] - $level:ELSE".BR;
            if ($level == 0) {
                return ($match_pos);
            }
        }

        if ($match_content == 'endif') {
//echo "[".__LINE__."] - $level:ENDIF".BR;
            if ($level == 0) {
                return ($match_pos);
            } else {
                $level--;
            }
        }
    }
}

/**
 * expand includes
 *
 * Given the initial contents of the template, replace {include} tags
 * with content from the corresponding .inc file.  Repeat up to 30 times
 * so that any nested {includes} can be processed.
 */
function template_parse_includes($template_content)
{
    $reps = 0;
    while(preg_match_all('/\{(include[^\}]+)\}/', $template_content, $matches_array)) {

        if (++$reps >29) {
            trigger_error('Too many nested includes in template.', E_USER_ERROR);
        }

        //  Iterate through all matching {include} tags
        for ($i = 0; $i < (count($matches_array[0])); $i++) {

            $match_string = $matches_array[0][$i];

            // Split each match apart into tagname and args-string
            $segment = explode(" ", $matches_array[1][$i], 2);
            $tag = $segment[0];
            $args_string = $segment[1];

            // If this is a registered tag, execute the corresponding function and replace
            // the tag with the returned dynamic content
            $output = template_do_include($tag, $args_string);
            $template_content = str_replace($match_string, $output, $template_content);
        }
    }

    return $template_content;
}

/**
 * do include
 *
 * @access  private
 */
function template_do_include($tag, $args_string)
{
    // get params
    $params  = template_args_to_array($args_string);

    // sanitize filename
    $params['snippet'] = ereg_replace('[^a-zA-Z0-9_-]', '', $params['snippet']);

    $filepath = props_getkey('config.dir.includes') . '/' . $params['snippet'] . '.inc';

    if (!isset($params['prepend'])) $params['prepend'] = '';
    if (!isset($params['append'])) $params['append'] = '';
    if (!isset($params['altoutput'])) $params['altoutput'] = '';

    if (file_exists($filepath)) {
        return $params['prepend'] . file_get_contents($filepath) . $params['append'];
    } else {
        return $params['altoutput'];
    }
}

?>
