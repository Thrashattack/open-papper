<?php
/**
 * Tag function
 *
 * @package     tags
 * @subpackage  example
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
 * @version     $Id: tag_skeleton.php,v 1.7 2006/10/27 09:03:20 roufneck Exp $
 */

/**
 * Tag template (place short one line description here)
 *
 * Long multi line description (optional)
 *
 * NOTE: If you create a tag you must enter it in modules/tag_registry.php
 * or it will not be recognized. You can auto add the tag with the
 * 'Update tags' button on the main screen (if you have the right privs).
 *
 * If you plan to submit your code the props community, remember to code conform
 * the {@link  http://pear.php.net/manual/en/standards.php  PEAR Coding Standards}.
 *
 * PARAMETERS REFERENCE
 * Always use a reference to params, so default values (like: prepend,
 * append and altoutput) can be set within the function.
 *
 * RETRIEVING TAG ATTRIBUTES
 * $params is an associative array containing name/value pairs
 * of any tag attributes that were set.  For example, if the tag appears
 * in a template as {tagname prefix="blah"}, $params["prefix"] would be
 * equal to "blah"
 *
 * STANDARD TAG ATTRIBUTES
 * There are certain attributes which are implemented by default
 * (see {@link  function  template_exec_tag}):
 *
 * <ul>
 *   <li><b>prepend="string"</b> - If the tag generates output, this will be
 *      added to the beginning of the output. If the tag does not generate
 *      any output (for example, if there are no stories for a given day)
 *      this will not be output. Altoutput is  returned instead.
 *   </li>
 *   <li><b>append="string"</b> - Same as prepend, except this is added at
 *      the end.
 *   </li>
 *   <li><b>altoutput="string"</b> - This specifies the text to be output when
 *      the tag doesn't produce any results. e.g.: if there are no stories in
 *      a given section, you might set altoutput to "There are no stories in
 *      this section today." The default is an empty string ("").
 *   </li>
 *   <li><b>user_logged_in="true|false"</b> - If set to true, it executes when
 *      a user is logged in. If set to false, it executes when no user is
 *      logged in.
 *
 *      Example:
 *      <code>
 *      // Display username only when logged in
 *      {user_name user_logged_in="true"}
 *      </code>
 *   </li>
 * </ul>
 *
 * format="formatstring"
 * This attribute should be formatted for all tags which output
 * multiple items, or lists.  The format string works very much the
 * same way as printf() or date().  Special metacharacters within
 * the formatstring are replaced with dynamic information about
 * each item in the list.  Example:
 *
 * Example:
 * <code>
 * // output a list of story headlines, each preceded by a dash,
 * // and followed by a <br />
 * {storylist format="-%h<br />"}
 * </code>
 *
 * @tag    {tag_name}
 * @param  array  &$params  parameters
 * <ul>
 *   <li><b>parameter</b> - description</li>
 *   <li><b>parameter</b> - valid tokens:
 *     <ul>
 *       <li>%n - desc</li>
 *       <li>%n - desc</li>
 *       <li>%n - desc</li>
 *       <li>%n - desc</li>
 *     </ul>
 *   </li>
 * </ul>
 *
 * @return  string  generated html code
 */
function tag_tagname(&$params)
{
    return "content output by tag";
}

?>
