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
 * @version     $Id: media_upload.php,v 1.14 2007/12/11 15:46:31 roufneck Exp $
 */

// loadLibs
props_loadLib('media,sections');

/**
 * @admintitle  Upload media
 * @adminnav    2
 * @return  string  admin screen html content
 */
function admin_media_upload()
{
    // Get the needed posted vars here
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    $caption = props_getrequest('caption', VALIDATE_TEXT, 'MAX255');
    $subcaption = props_getrequest('subcaption', VALIDATE_TEXT, 'MAX255');
    $credit_line = props_getrequest('credit_line', VALIDATE_TEXT, 'MAX64');
    $credit_suffix = props_getrequest('credit_suffix', VALIDATE_TEXT, 'MAX64');
    $credit_url = props_getrequest('credit_url', VALIDATE_TEXT, 'MAX128');
    $keywords = props_getrequest('keywords', VALIDATE_TEXT, 'MAX64');

    // Handle POST form submissions here
    // REQUEST_METHOD must be POST (protection against url hacking)
    $op = (isset($_POST['op'])) ? $_POST['op'] : '';
    switch($op) {

        case props_gettext("Cancel"):
            // Redirect
            props_redirect(TRUE);
            break;

        case props_gettext("Save"):

            // Check pageID. An extra check against URL hacking.
            if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
                props_error("Invalid page referer. Please submit this form again.");
                break;
            }

            // Check for upload errors
            switch ($_FILES['mediafile']['error']) {
                case 0;
                    // No error
                    break;
                case 1:
                    props_error(sprintf(props_gettext("The uploaded file exceeds the maximum size '%s'."), ini_get('upload_max_filesize')));
                    break;
                case 2:
                    props_error(sprintf(props_gettext("The uploaded file exceeds the maximum size '%s'."),$_POST['MAX_FILE_SIZE']));
                    break;
                case 3:
                    props_error("The file was only partially uploaded.");
                    break;
                case 4:
                    props_error("No file was uploaded.");
                    break;
                default:
                    props_error("Unkown upload error.");
                    break;
            }

            if (!empty($GLOBALS['PROPS_ERRORSTACK'])) {
                break;
            }

            $media_file = media_identify($_FILES['mediafile']['tmp_name']);

            // If no errors, do update, otherwise drop through and display errors
            if (empty($GLOBALS['PROPS_ERRORSTACK']) && $media_file !== FALSE) {

                $path = date("Y/m/d");

                // Add to DB
                $q  = "INSERT INTO props_media SET "
                    . "section_id = '" . $section_id . "', "
                    . "type = '" . $media_file['type'] . "', "
                    . "group_id = '" . $media_file['group_id'] . "', "
                    . "size = '" . $media_file['size'] . "', "
                    . "width = '" . $media_file['width'] . "', "
                    . "height = '" . $media_file['height'] . "', "
                    . "duration = '" . $media_file['duration'] . "', "
                    . "path = '" . sql_escape_string($path) . "', "
                    . "caption = '" . sql_escape_string($caption) . "', "
                    . "subcaption = '" . sql_escape_string($subcaption) . "', "
                    . "credit_line = '" . sql_escape_string($credit_line) . "', "
                    . "credit_suffix = '" . sql_escape_string($credit_suffix) . "', "
                    . "credit_url = '" . sql_escape_string($credit_url) . "', "
                    . "keywords = '" . sql_escape_string($keywords) . "'";
                $media_id = sql_identity_insert($q);

                // Upload file
                if (media_upload($media_id, $path, $_FILES['mediafile']['tmp_name'])) {
                    props_error(sprintf(props_gettext("Uploaded file '%s'"), $_FILES['mediafile']['name']));
                }
            }
            break;

    } // END switch

    // Generate the function output.
    // Use htmlspecialchars() for all vars, for compatibility and XSS prevention.
    $output =
         '<p><em>' . props_gettext("All text fields are optional.") . ' ' . props_gettext("Click cancel when you finished uploading media files.") . '</em></p>'.LF
        .'<form action="./" enctype="multipart/form-data" method="post">'.LF
        .'<input name="module" type="hidden" value="' . $GLOBALS['PROPS_MODULE'] . '" />'.LF
        .'<input name="function" type="hidden" value="' . $GLOBALS['PROPS_FUNCTION'] . '" />'.LF
        .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
        .'<fieldset>'.LF
        .'  <legend>' . props_gettext("Media details") . '</legend>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Select file") . '</label><br />' . props_gettext("Valid media types") . ' : ' . media_supported_types() . '</dt>'.LF
        .'    <dd><input class="large" type="file" name="mediafile" /></dd>'.LF
        .((props_geterror('mediafile')) ? '<dd>' . props_geterror('mediafile') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Section") . '</label></dt>'.LF
        .'    <dd>' . section_select($section_id, 'section_id', TRUE) . '</dd>'.LF
        .((props_geterror('section_id')) ? '<dd>' . props_geterror('section_id') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Caption") . '</label></dt>'.LF
        .'    <dd><textarea class="large" style="height: 5em;" id="caption" name="caption" rows="3" cols="30">' . htmlspecialchars($caption) . '</textarea></dd>'.LF
        .((props_geterror('caption')) ? '<dd>' . props_geterror('caption') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Sub caption") . '</label></dt>'.LF
        .'    <dd><textarea class="large" style="height: 5em;" id="subcaption" name="subcaption" rows="3" cols="30">' . htmlspecialchars($subcaption) . '</textarea></dd>'.LF
        .((props_geterror('subcaption')) ? '<dd>' . props_geterror('subcaption') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Credit line") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="credit_line" name="credit_line" value="' . htmlspecialchars($credit_line) . '" /></dd>'.LF
        .((props_geterror('credit_line')) ? '<dd>' . props_geterror('credit_line') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Credit suffix") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="credit_suffix" name="credit_suffix" value="' . htmlspecialchars($credit_suffix) . '" /></dd>'.LF
        .((props_geterror('credit_suffix')) ? '<dd>' . props_geterror('credit_suffix') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Credit URL") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="credit_url" name="credit_url" value="' . htmlspecialchars($credit_url) . '" /></dd>'.LF
        .((props_geterror('credit_url')) ? '<dd>' . props_geterror('credit_url') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'  <dl>'.LF
        .'    <dt><label>' . props_gettext("Keywords") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="keywords" name="keywords" value="' . htmlspecialchars($keywords) . '" /></dd>'.LF
        .((props_geterror('keywords')) ? '<dd>' . props_geterror('keywords') . '</dd>'.LF : '')
        .'  </dl>'.LF

        .'</fieldset>'.LF
        .'  <p>'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Cancel") . '" />&nbsp;&nbsp;'.LF
        .'    <input class="button" name="op" type="submit" value="' . props_gettext("Save") . '" />'.LF
        .'  </p>'.LF
        .'</form>'.LF;

    return $output;
}

?>
