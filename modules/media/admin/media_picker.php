<?php
/**
 * Admin function
 *
 * @package     modules
 * @subpackage  media
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
 * @version     $Id: media_picker.php,v 1.14 2008/01/07 16:25:01 roufneck Exp $
 */

// loadLibs
props_loadLib('media');

/**
 * @admintitle  Assign media
 * @adminnav    0
 * @return  string  admin screen html content
 */
function admin_media_picker()
{
    $op = props_getrequest('op', VALIDATE_TEXT);
    $status = props_getrequest('status', VALIDATE_INT);
    $group = props_getrequest('group', VALIDATE_ARRAY);
    $search_string = props_getrequest('search_string', VALIDATE_EALPHA.VALIDATE_SPACE, 'SANITIZE');
    $position = props_getrequest('position', VALIDATE_INT);

    if (empty($status)) $status = 1;
    if (empty($group)) $media_group[] = PROPS_MEDIA_GRAPHICS;

    $output =
         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.LF
        .'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en">'.LF
        .'<head>'.LF
        .'  <title>PROPS Admin - ' . props_gettext('.media.media_picker') . '</title>'.LF
        .'  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.LF
        .'  <meta http-equiv="Content-Language" content="en" />'.LF
        .'  <meta name="robots" content="noindex,nofollow" />'.LF
        .'  <meta name="keywords" content="PROPS - Open Source News Publishing Platform" />'.LF
        .'  <link rel="shortcut icon" href="favicon.ico" />'.LF
        .'  <link rel="stylesheet" type="text/css" media="all" href="props.admin.css" />'.LF
        .'  <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="props.iefix.css" /><!{endif]-->'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'props.admin.js"></script>'.LF
        .'</head>'.LF
        .'<body>'.LF
        .'<br />'.LF
        .'<form action="./" method="get">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="op" type="hidden" value="search" />'.LF
        .'<fieldset style="width: 550px;">'.LF
        .'  <legend>' . props_gettext(".media.media_picker") . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Status") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <input class="checkbox" type="radio" name="status" value="1" ' . (($status == 1) ? 'checked="checked"': '') . ' />' . props_gettext("Any") . '&nbsp;'.LF
        .'      <input class="checkbox" type="radio" name="status" value="2" ' . (($status == 2) ? 'checked="checked"': '') . ' />' . props_gettext("Assigned") . '&nbsp;'.LF
        .'      <input class="checkbox" type="radio" name="status" value="3" ' . (($status == 3) ? 'checked="checked"': '') . ' />' . props_gettext("Unassigned").LF
        .'    </dd>'.LF
        .((props_geterror('status')) ? '<dd>' . props_geterror('status') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Type") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <input class="checkbox" type="checkbox" name="group[]" value="'.PROPS_MEDIA_GRAPHICS.'" ' . ((in_array(PROPS_MEDIA_GRAPHICS, $group)) ? 'checked="checked"': '') . ' />' . props_gettext("graphics") . '&nbsp;'.LF
        .'      <input class="checkbox" type="checkbox" name="group[]" value="'.PROPS_MEDIA_AUDIO.'" ' . ((in_array(PROPS_MEDIA_AUDIO, $group)) ? 'checked="checked"': '') . ' />' . props_gettext("audio") . '&nbsp;'.LF
        .'      <input class="checkbox" type="checkbox" name="group[]" value="'.PROPS_MEDIA_VIDEO.'" ' . ((in_array(PROPS_MEDIA_VIDEO, $group)) ? 'checked="checked"': '') . ' />' . props_gettext("audio/video") . '&nbsp;'.LF
        .'    </dd>'.LF
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Caption or credit line contains") . '</label></dt>'.LF
        .'    <dd>'.LF
        .'      <input class="medium" type="text" id="search_string" name="search_string" value="' . htmlspecialchars($search_string) . '" />&nbsp;'
        .'<input class="button" type="submit" value="' . props_gettext("Search") . '" />'.LF
        .'    </dd>'.LF
        .((props_geterror('search_string')) ? '<dd>' . props_geterror('search_string') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'</fieldset>'.LF
        .'</form><br />'.LF;

    // Handle POST form submissions here
    switch($op) {

        case 'search':

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK'])) {

                $search_string = ereg_replace(' +', ' ', $search_string); // Squash repeated whitespaces

                // Construct SQL
                $q  = "SELECT props_media.* "
                    . "FROM props_media "
                    . "LEFT JOIN props_media_story_xref "
                    . "ON props_media.media_id = props_media_story_xref.media_id "
                    . "WHERE (1 = 1) ";

                if (!empty($search_string)) {
                    $q .= "AND (caption LIKE '%$search_string%' "
                        . "OR subcaption LIKE '%$search_string%' "
                        . "OR credit_line LIKE '%$search_string%' "
                        . "OR credit_suffix LIKE '%$search_string%' "
                        . "OR keywords LIKE '%$search_string%') ";
                }

                $itemcount = 0;
                $pagination_url = '';
                if (!empty($group)) {
                    $max = sizeof($group);
                    $q .= "AND (";
                    foreach($group as $item) {
                        $q .= "group_id = '" . sql_escape_string($item) . "' ";
                        if (++$itemcount < $max) $q .= " OR ";
                        $pagination_url .= htmlspecialchars('&group_id='.$item);
                    }
                    $q .= ") ";
                }

                $q .= "GROUP BY media_id DESC ";
                if ($status == "2") {
                    $q .= "HAVING (COUNT(story_id) > 0) ";
                } elseif ($status == "3") {
                    $q .= "HAVING (COUNT(story_id) = 0) ";
                }
                
                // Get total results
                $result = sql_query($q);
                $result_rows = sql_num_rows($result);
                if (!$position) {
                    $position = 0;
                }

                // Construct page navigation
                $pagination = props_pagination($result_rows, $position, 10, './?module=' . $GLOBALS['PROPS_MODULE'] . '&amp;function=' . $GLOBALS['PROPS_FUNCTION'] . '&amp;op=search&amp;search_string='.$search_string.'&amp;status='.$status.$pagination_url);

                // Get results for this page
                $q .= "LIMIT $position, 10";
                $result = sql_query($q);

                // Generate the search result
                $output .=
                     '<p>'.sprintf(props_gettext("%s results found"), $result_rows).' - '.$pagination.'</p>'
                    .'<table>'.LF;

                if (!sql_num_rows($result)) {
                    $output .= '  <tr class="row1"><td style="text-align: center;">'.props_gettext("No results found.").'</td></tr>'.LF;
                } else {
                    $row_num = 1;
                    while($row = sql_fetch_assoc($result)) {
                        media_get_details($row);

                        $output .=
                             '  <tr class="row'.$row_num.'">'.LF
                            .'    <td><img onclick="javascript:addMedia(this);" id="media' . $row['media_id'] . '" src="' . $row['thumb_url'] . '?nocache=' . time() . '" alt="' . htmlspecialchars($row['caption']) . '" /></td>'.LF
                            .'    <td>'.LF
                            .'      <b>' . props_gettext("Caption") . ':</b> ' . htmlspecialchars($row['caption']) . '<br />'.LF
                            .'      <b>' . props_gettext("Sub caption") . ':</b> ' . htmlspecialchars($row['subcaption']) . '<br />'.LF
                            .'      <b>' . props_gettext("Credit line") . ':</b> ' . htmlspecialchars($row['credit_line']) . '<br />'.LF
                            .'      <b>' . props_gettext("Keywords") . ':</b> ' . htmlspecialchars($row['keywords']) . '<br />'.LF
                            .'      <b>' . props_gettext("Size") . ':</b> ' . $row['media_width'] . 'x' . $row['media_height'] . ' (' . $row['media_size'] . ' bytes)<br />'.LF
                            .'    </td>'.LF
                            .'  </tr>'.LF;
                        $row_num = ($row_num == 1) ? 2 : 1;
                    }
                }
                $output .= '</table>'.LF;
            }
            break;

    } // END switch

    $output .=
         '</body>'.LF
        .'</html>'.LF;

    echo $output;
    exit;
}

?>
