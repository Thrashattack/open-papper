<?php
/**
 * Lib - media functions
 *
 * @package     api
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
 * @version     $Id: media.php,v 1.18 2007/12/18 08:02:28 roufneck Exp $
 */

/**
 * Defines
 */
define('PROPS_MEDIA_GRAPHICS',  1);
define('PROPS_MEDIA_AUDIO',     2);
define('PROPS_MEDIA_VIDEO',     3);
define('PROPS_MEDIA_MISC',      4);

// Supported media types, based on getID3() - http://getid3.sourceforge.net
$GLOBALS['PROPS_MEDIA_TYPES'] = array(
    // GIF  - still image - Graphics Interchange Format
    'gif'  => array(
                'pattern'   => '^GIF',
                'group_id'  => PROPS_MEDIA_GRAPHICS,
                'mime_type' => 'image/gif',
                'icon'      => 'icon-media.png'
            ),
    // JPEG - still image - Joint Photographic Experts Group (JPEG)
    'jpg'  => array(
                'pattern'   => '^\xFF\xD8\xFF',
                'group_id'  => PROPS_MEDIA_GRAPHICS,
                'mime_type' => 'image/jpeg',
                'icon'      => 'icon-media.png'
            ),
    // PNG  - still image - Portable Network Graphics (PNG)
    'png'  => array(
                'pattern'   => '^\x89\x50\x4E\x47\x0D\x0A\x1A\x0A',
                'group_id'  => PROPS_MEDIA_GRAPHICS,
                'mime_type' => 'image/png',
                'icon'      => 'icon-media.png'
            ),
    // TIFF  - still image - Tagged Information File Format (TIFF)
    'tiff' => array (
                'pattern'   => '^(II\x2A\x00|MM\x00\x2A)',
                'group_id'  => PROPS_MEDIA_GRAPHICS,
                'mime_type' => 'image/tiff',
                'icon'      => 'icon-media.png'
            ),


    // MP3  - audio       - MPEG-audio Layer 3 (very similar to AAC-ADTS)
    'mp3'  => array (
                'pattern'   => '^(ID3|APETAGEX|TAG|LYRICS|\xFF[\xE2-\xE7\xF2-\xF7\xFA-\xFF][\x00-\xEB])',
                'group_id'  => PROPS_MEDIA_AUDIO,
                'mime_type' => 'audio/mpeg',
                'icon'      => 'icon-audio.png'
            ),


    // FLV  - audio/video - FLash Video
    'flv' => array(
                'pattern'   => '^FLV\x01',
                'group_id'  => PROPS_MEDIA_VIDEO,
                'mime_type' => 'video/x-flv',
                'icon'      => 'icon-video.png'
            ),
    // MPEG - audio/video - MPEG (Moving Pictures Experts Group)
    'mpeg' => array (
                'pattern'   => '^\x00\x00\x01(\xBA|\xB3)',
                'group_id'  => PROPS_MEDIA_VIDEO,
                'mime_type' => 'video/mpeg',
                'icon'      => 'icon-video.png'
            ),
    // SWF - audio/video - ShockWave Flash
    'swf' => array (
                'pattern'   => '^(F|C)WS',
                'group_id'  => PROPS_MEDIA_VIDEO,
                'mime_type' => 'application/x-shockwave-flash',
                'icon'      => 'icon-video.png'
            ),


    // PDF  - data - Portable Document Format
    'pdf' => array(
                'pattern'   => '^\x25PDF',
                'group_id'  => PROPS_MEDIA_MISC,
                'mime_type' => 'application/pdf',
                'icon'      => 'icon-pdf.png'
            )
    );

function media_supported_types()
{
    $list = '';
    foreach ($GLOBALS['PROPS_MEDIA_TYPES'] as $type => $details) {
        if (empty($list)) {
            $list = '.'.$type;
        } else {
            $list .= ', .'.$type;
        }
    }

    return $list;
}

/**
 * Process uploaded media file
 *
 * @param   int     $media_id
 * @param   string  $path      media sub path
 * @param   string  $source    source file
 * @return  bool    TRUE on success, FALSE on failure
 */
function media_upload($media_id, $path, $source)
{
    $max_width = props_getkey('config.media.image.max_width');
    $max_height = props_getkey('config.media.image.max_height');

    // Identify
    $info = media_identify($source);
    $target = media_path($media_id, $path, $info['type'], '', '', 'original', TRUE);

    $resize = FALSE;
    // Resize media if needed
    if ($GLOBALS['PROPS_MEDIA_TYPES'][$info['type']]['group_id'] == PROPS_MEDIA_GRAPHICS
        && $max_width > 0 && $max_height > 0
        && ($info['width'] > $max_width || $info['height'] > $max_height))
    {
        list($width, $height) = media_dimensions($source_width, $source_height, $width, $height, 'constrain');

        if (media_resize($source, $info, $target, $width, $height)) {
            props_debug('MEDIA: resized uploaded media file');
            $resize = TRUE;
            // Update info
            $info = media_identify($target);
        } else {
            props_error("The uploaded image dimensions are too big. Unable to resize the image.");
            return FALSE;
        }
    }

    // Move uploaded media file to the media dir
    if ($resize == FALSE && move_uploaded_file($source, $target) == FALSE) {
        props_error("Error moving uploaded file.");
        return FALSE;
    }

    // Change file mode
    @chmod($target, 0755);

    // Delete old thumbnails, keep original
    media_delete($media_id, $path, TRUE);

    // Update database
    $q  = "UPDATE props_media SET "
        . "size = '" . $info['size'] . "', "
        . "width = '" . $info['width'] . "', "
        . "height = '" . $info['height'] . "', "
        . "duration = '" . $info['duration'] . "', "
        . "type = '" . sql_escape_string($info['type']) . "', "
        . "group_id = '" . $info['group_id'] . "' "
        . "WHERE media_id = " . $media_id . "";
    $result = sql_query($q);

    return TRUE;
}

/**
 * Indentify media file
 *
 * Checks for a valid uploaded file and returns an array with file info.
 *
 * @param   string  $source  media file to be processed
 * @return  array   media file info
 * - width
 * - height
 * - size
 * - duration
 * - group_id
 * - type
 * - mime_type
 * - iptc
 */
function media_identify($source)
{
    $size = $width = $height = $duration = $group_id = $iptc = NULL;
    $type = FALSE;

    // Determine file format by magic bytes in file header
    if (!$fp = @fopen($source, 'rb')) {
        trigger_error("Could not open file: '$source_file'", E_USER_NOTICE);
        return FALSE;
    }

    // Read file data
    fseek($fp, 0, SEEK_SET);
    $filedata = fread($fp, 128);
    fclose($fp);

    // Identify file format - loop through $format_info and detect with reg expr
    foreach ($GLOBALS['PROPS_MEDIA_TYPES'] as $media_type => $info) {
        // The /s switch on preg_match() forces preg_match() NOT to treat
        // newline (0x0A) characters as special chars but do a binary match
        if (preg_match('/'.$info['pattern'].'/s', $filedata)) {
            $type = $media_type;
            break;
        }
    }

    // Free memory
    unset($filedata);

    if ($type === FALSE) {
        props_error("This media file format is not supported.", PROPS_E_WARNING);
        return FALSE;
    }

    $group_id = $GLOBALS['PROPS_MEDIA_TYPES'][$type]['group_id'];
    $mime_type = $GLOBALS['PROPS_MEDIA_TYPES'][$type]['mime_type'];
    $size = filesize($source);

    switch ($group_id) {
        case PROPS_MEDIA_GRAPHICS:
            // Get dimensions
            $imagesize = @getimagesize($source, $iptc);
            $width = $imagesize[0];
            $height = $imagesize[1];
            break;

        case PROPS_MEDIA_AUDIO:
            if (props_getkey('config.media.ffmpeg_path')) {
                $successfulRun = FALSE;
                list ($ret, $results, $stderr) = media_ffmpeg_exec($source, array('-vstats'));

                foreach ($stderr as $resultLine) {
                    if (preg_match("/Unknown format/", $resultLine, $regs)) {
                        $successfulRun = FALSE;
                    }

                    if (preg_match("/Duration: (\d+):(\d+):(\d+\.\d+)/", $resultLine, $regs)) {
                        $successfulRun = TRUE;
                        $duration = floor(3600*$regs[1] + 60*$regs[2] + $regs[3]);
                    }
                }

                if (!$successfulRun) {
                    props_error("This audio file format is not supported.", PROPS_E_WARNING);
                    return FALSE;
                }
            }
            break;

        case PROPS_MEDIA_VIDEO:
            if (props_getkey('config.media.ffmpeg_path')) {
                $successfulRun = FALSE;
                list ($ret, $results, $stderr) = media_ffmpeg_exec($source, array('-vstats'));

                foreach ($stderr as $resultLine) {
                    if (preg_match("/Unknown format/", $resultLine, $regs)) {
                        $successfulRun = FALSE;
                    }

                    if (preg_match("/Duration: (\d+):(\d+):(\d+\.\d+)/", $resultLine, $regs)) {
                        $successfulRun = TRUE;
                        $duration = floor(3600*$regs[1] + 60*$regs[2] + $regs[3]);
                    }

                    if (preg_match("/Stream.*?Video:.*?(\d+)x(\d+)/", $resultLine, $regs)) {
                        $successfulRun = TRUE;
                        list ($width, $height) = array($regs[1], $regs[2]);
                    }
                }

                if (!$successfulRun) {
                    props_error("This video file format is not supported.", PROPS_E_WARNING);
                    return FALSE;
                }
            }
            break;
    }

    return array('width'=>$width, 'height'=>$height, 'size'=>$size, 'duration'=>$duration, 'group_id'=>$group_id, 'type'=>$type, 'mime_type'=>$mime_type, 'iptc'=>$iptc);
}

/**
 * Resize media file
 *
 * @param   string  $source  source file
 * @param   string  $target  target file
 * @param   int     $width
 * @param   int     $height
 * @return  bool    TRUE on success, FALSE on failure
 *
 * @todo  Use ImageMagick convert instead of ImageMagick mogrify
 */
function media_resize($source, $source_info, $target, $width, $height)
{
    if (!is_array($source_info)) {
        $source_info = media_identify($source);
    }

    if ($source_info === FALSE) {
        return FALSE;
    }

    $target_type = substr(strrchr($target, '.'), 1);

    switch ($GLOBALS['PROPS_MEDIA_TYPES'][$source_info['type']]['group_id']) {

        case PROPS_MEDIA_GRAPHICS:

            // Detect which image functions to use
            switch (props_getkey('config.media.image.functions')) {

                case "auto":
                case "gd":
                    // Try the GD library
                    if (function_exists('gd_info')) {
                        $source_id = FALSE;
                        if (($source_info['type'] == 'jpg') && (imagetypes() & IMG_JPG)) {
                            $source_id = imagecreatefromjpeg($source);
                        } elseif (($source_info['type'] == 'png') && (imagetypes() & IMG_PNG)) {
                            $source_id = imagecreatefrompng($source);
                        } elseif (($source_info['type'] == 'gif') && (imagetypes() & IMG_GIF)) {
                            $source_id = imagecreatefromgif($source);
                        }

                        if ($source_id) {
                            // Create a new image object (not neccessarily true colour)
                            // The imagecreatetruecolor function is available from GD2.
                            if (function_exists("imagecreatetruecolor")) {
                                $img_create_function = 'imagecreatetruecolor';
                            } else {
                                $img_create_function = 'imagecreate';
                            }
                            $target_id = $img_create_function($width, $height);

                            // Resize the original image and copy it into the just created image object
                            imagecopyresampled($target_id, $source_id, 0,0,0,0, $width, $height, $source_info['width'], $source_info['height']);

                            // Free up memory
                            imagedestroy($source_id);

                            // Create the final image
                            $status = FALSE;
                            if (($target_type == 'png') && (imagetypes() & IMG_PNG)) {
                                $status = imagepng($target_id, $target);
                            } elseif (($target_type == 'gif') && (imagetypes() & IMG_GIF)) {
                                $status = imagegif($target_id, $target);
                            } elseif (imagetypes() & IMG_JPG) {
                                $status = imagejpeg($target_id, $target, props_getkey('config.media.image.quality'));
                            }

                            // Free up more memory
                            imagedestroy($target_id);

                            // If success return TRUE, otherwise fallback to imagemagick
                            if ($status == TRUE) {
                                return TRUE;
                            }
                        }
                    }
                    // There where probably errors. Fallback to ImageMagick.

                default:

                    if (!is_file(props_getkey('config.media.imagemagick.mogrify'))) {
                        // Silently return, imagemagick is not configured
                        return FALSE;
                    }
/**/
                    // Copy the original file to the new one
                    copy($source, $target);

                    $cmd  = "cd " . props_getkey('config.dir.media') . ";\n";
                    $cmd .= props_getkey('config.media.imagemagick.mogrify') . " -geometry $width" . "x" . "$height! -format $target_type $target \n";

                    if (!system($cmd)) {
                        // Cleanup
                        @unlink($target);
                        return FALSE;
                    }
/*
                    //copy($source, $target);
                    list($status, $results, $stderr) = props_exec(array(
                            array(
                                props_getkey('config.media.imagemagick.mogrify'),
                                $source,
                                '-resize', $width.'x'.$height.'!',
                                '-format', $target_type,
                                $target
                            )));
echo "[".__LINE__."] status: $status".BR;

echo '<pre>'.BR;
print_r($results);
print_r($stderr);
echo '</pre>'.BR;

/**/
                    return TRUE;
                    break;
            }

            break;

        case PROPS_MEDIA_VIDEO:
            // If valid ffmpeg_path, create snapshot
            $tmp_dir = props_getkey('config.dir.cache');
            $tmp_file = @tempnam($tmp_dir, 'ffmpg_');

            if (empty($tmp_file)) {
                props_error("Can't write to the cache dir.", PROPS_E_WARNING);
                return FALSE;
            }

            // Extract jpeg
            $args = array('-f', 'mjpeg', '-t', '0.001', '-y', $tmp_file);
            if ($source_info["duration"] > 5) {
                array_unshift($args, '-ss', 5);
            }

            // Take a snapshot
            list ($ret, $results) = media_ffmpeg_exec($source, $args);

            // Somehow we get a FALSE returned and the file is there,
            // so check if there is a file
            if (filesize($tmp_file) > 0) {
                $info = getimagesize($tmp_file);
                if ($info['mime'] == 'image/jpeg') {
                    // Got a valid snapshot
                    $status = copy($tmp_file, $target);
                    @unlink($tmp_file);
                    return $status;
                }
            }

            // Cleanup
            @unlink($tmp_file);
            return FALSE;

            break;
    }
}

/**
 * Calculate image dimensions depending upon the scaling mode requested
 *
 * @param   int     $source_width
 * @param   int     $source_height
 * @param   int     $target_width
 * @param   int     $target_height
 * @param   string  $mode  absolute or constrain
 * @return  array   width, height
 */
function media_dimensions($source_width, $source_height, $target_width, $target_height, $mode = 'constrain')
{
    if (!empty($source_width) && !empty($source_height) && !empty($target_width) && !empty($target_height)) {
        if ($mode == 'absolute') {
            $dest_x = $target_width;
            $dest_y = $target_height;
        } else {
            // Default: 'constrain'
            $scale = min($target_width/$source_width, $target_height/$source_height);
            $dest_x = (int)($source_width*$scale);
            $dest_y = (int)($source_height*$scale);
        }
    } elseif (!empty($target_width) && !empty($target_height)) {
        $dest_x = $target_width;
        $dest_y = $target_height;
    } elseif (!empty($source_width) && !empty($source_height)) {
        $dest_x = $source_width;
        $dest_y = $source_height;
    } else {
        $dest_x = 110;
        $dest_y = 110;
    }

    return array($dest_x, $dest_y);
}

/**
 * Construct a path to a media file
 *
 * @param   string  $media_id
 * @param   string  $path
 * @param   int     $width
 * @param   int     $height
 * @param   string  $type
 * @param   string  $mode
 * @param   bool    $create_dir
 * @return  string  media file path
 */
function media_path($media_id, $path, $type, $width, $height, $mode = 'constrain', $create_dir = FALSE)
{
    if (!empty($path)) {
        $path = $path.'/';
    }

    if ($create_dir == TRUE) {
        $dir = sprintf('%s%s', props_getkey('config.dir.media'), $path);
        props_mkdirs($dir, 0755);
    }

    if ($width > 0 && $height > 0 && $mode != 'original') {
        return sprintf('%s%s%08d-%s-%sx%s.%s', props_getkey('config.dir.media'),
                        $path, $media_id, $mode, $width, $height, $type);
    } else {
        return sprintf('%s%s%08d-%s.%s', props_getkey('config.dir.media'),
                        $path, $media_id, 'original', $type);
    }
}

/**
 * Construct an url to a media file
 *
 * @param   string  $media_id
 * @param   string  $path
 * @param   int     $width
 * @param   int     $height
 * @param   string  $type
 * @param   string  $mode
 * @return  string  media file path
 */
function media_url($media_id, $path, $type, $width, $height, $mode = 'constrain')
{
    return props_getkey('config.url.media')
            . 'media.php?id='.$media_id
            . '&amp;path='.str_replace('/', '-', $path)
            . '&amp;type='.$type
            . '&amp;mode='.$mode
            . '&amp;width='.$width
            . '&amp;height='.$height;
/*
    if (!empty($path)) {
        $path = $path.'/';
    }
    
    if ($width > 0 && $height > 0 && $mode != 'original') {
        return sprintf('%s%s%08d-%s-%sx%s.%s', props_getkey('config.url.media'),
                        $path, $media_id, $mode, $width, $height, $type);
    } else {
        return sprintf('%s%s%08d-%s.%s', props_getkey('config.url.media'),
                        $path, $media_id, 'original', $type);
    }
*/
}

/**
 * Delete media files
 *
 * @param   int     $media_id       id of media file
 * @param   string  $path           sub dir of target file
 * @param   bool    $keep_original
 */
function media_delete($media_id, $path, $keep_original = FALSE)
{
    $media_id = intval($media_id);
    $mediaprefix = sprintf('%08d', $media_id);

    $dir = props_getkey('config.dir.media') .'/'.$path;

    // Open a known directory, and proceed to read its contents
    if (is_dir($dir)) {
       if ($dh = opendir($dir)) {
           while (($file = readdir($dh)) !== FALSE) {
                if (ereg("^$mediaprefix-", $file)) {
                    if ($keep_original == TRUE && ereg("^$mediaprefix-original.", $file)) {
                        props_debug('MEDIA: keep original file '.$file);
                        //if ($original == TRUE && $file == "$mediaprefix-original.$original") {
                        // Keep file
                    } else {
                        // Delete file
                        props_debug('MEDIA: delete '.$file);
                        unlink($dir . '/' . $file);
                    }
                }
           }
           closedir($dh);
       }
    }
}

/**
 * Construct media locations and details
 */
function media_get_details(&$media, $width = '110', $height = '110', $mode = 'constrain')
{
    // Set source_path, source_url
    $media['source_url'] = media_url($media['media_id'], $media['path'], $media['type'], '', '', 'original');

    // Get dimensions
    list($dest_x, $dest_y) = media_dimensions($media['width'], $media['height'], $width, $height, $mode);

    // Set thumb_path and thumb_url
    switch ($GLOBALS['PROPS_MEDIA_TYPES'][$media['type']]['group_id']) {
        case PROPS_MEDIA_GRAPHICS:

            $media['thumb_url'] = media_url($media['media_id'], $media['path'], $media['type'], $width, $height, $mode);
            
            $media['thumb_html'] = $media['embedded'] =
                 sprintf('<img src="%s" width="%s" height="%s" alt="%s" />',
                        $media['thumb_url'], $dest_x, $dest_y,
                        htmlspecialchars($media['caption']));
            
            break;

        case PROPS_MEDIA_AUDIO:

            $media['thumb_url'] = props_getkey('config.url.media').$GLOBALS['PROPS_MEDIA_TYPES'][$media['type']]['icon'];
            
            $media['thumb_html'] =
                 sprintf('<img src="%s" width="%s" height="%s" alt="%s" />',
                        $media['thumb_url'], $dest_x, $dest_y,
                        htmlspecialchars($media['caption']));

            $media['embedded'] =
                 '<p id="media'.$media['media_id'].'"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</p>'.LF
                .'<script type="text/javascript">'.LF
                .'  var playerObj'.$media['media_id'].' = new SWFObject("'.props_getkey('config.url.scripts').'mediaplayer.swf","single","'.$dest_x.'","'.$dest_y.'","7");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("file","'.$media['source_url'].'");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("image","'.$media['thumb_url'].'");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("displayheight","'.$dest_y.'");'.LF
                .'  playerObj'.$media['media_id'].'.addParam("allowfullscreen","false");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("showeq","true");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("backcolor","0x000000");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("frontcolor","0xCCCCCC");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("lightcolor","0x996600");'.LF
                .'  playerObj'.$media['media_id'].'.write("media'.$media['media_id'].'");'.LF
                .'</script>'.LF;

            break;

        case PROPS_MEDIA_VIDEO:

            // Construct locations
            $media['thumb_url'] = media_url($media['media_id'], $media['path'], $media['type'], $width, $height, $mode);

            $media['thumb_html'] = 
                 sprintf('<img src="%s" width="%s" height="%s" alt="%s" />',
                        $media['thumb_url'], $dest_x, $dest_y,
                        htmlspecialchars($media['caption']));

            $media['embedded'] =
                 '<p id="media'.$media['media_id'].'"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</p>'.LF
                .'<script type="text/javascript">'.LF
                .'  var playerObj'.$media['media_id'].' = new SWFObject("'.props_getkey('config.url.scripts').'mediaplayer.swf","single","'.$dest_x.'","'.$dest_y.'","7");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("file","'.$media['source_url'].'");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("image","'.$media['thumb_url'].'");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("displayheight","'.$dest_y.'");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("backcolor","0x000000");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("frontcolor","0xCCCCCC");'.LF
                .'  playerObj'.$media['media_id'].'.addVariable("lightcolor","0x996600");'.LF
                .'  playerObj'.$media['media_id'].'.write("media'.$media['media_id'].'");'.LF
                .'</script>'.LF;

            break;
    }
}

/**
 * Execute ffmpeg
 *
 * Run a given ffmpeg command on the source file name and return the command
 * line results.
 *
 * @param   string  $source_file  the input file name
 * @param   array   $args         the command line arguments
 * @return  array   First element is TRUE on success, FALSE on failure.
 *                  The second is the result of the execution.
 *                  The third element is the command output string.
 */
function media_ffmpeg_exec($source_file, $args)
{
    // Get error output back, because ffmpeg 0.4.6 returns some useful info
    // only to stderr!
    list ($success, $results, $error) =
        props_exec(array(array_merge(array(props_getkey('config.media.ffmpeg_path'), '-i', $source_file), $args)));

    if (!$success) {
        // Return the output even if there's a failure
        return array(FALSE, $results, $error);
    }

    return array(TRUE, $results, $error);
}

/**
 * Execute a command
 *
 * Execute a system command, returning the result and the output (both stdout and
 * stderr)
 *
 * @param   array  $cmdArray  command to execute
 * @return  array  first element is true if the command was properly executed,
 *                 next two array elements are standard output and stderr.
 */
function props_exec($cmdArray)
{
    /* Assemble the command array into a pipeline */
    $command = '';
    foreach ($cmdArray as $cmdAndArgs) {
        if (strlen($command)) {
            $command .= ' | ';
        }

        foreach ($cmdAndArgs as $arg) {
            if ($arg === '>') {
                $command .= '>';
            } else {
                $command .= ' "' . $arg . '" ';
            }
        }
    }

    /* Redirect STDERR to a file */
    $tmp_dir = props_getkey('config.dir.cache');
    $debug_file = @tempnam($tmp_dir, 'execdbg_');
    $command = "($command) 2>$debug_file";

    $results = array();
    exec($command, $results, $status);

    $stderr = array();
    if (@file_exists($debug_file)) {
        if (filesize($debug_file) > 0) {
            if ($fd = @fopen($debug_file, "r")) {
                while (!feof($fd)) {
                    $buf = fgets($fd, 4096);
                    $buf = rtrim($buf);
                    if (!empty($buf)) {
                        $stderr[] = $buf;
                    }
                }
                @fclose($fd);
            }
        }
        @unlink($debug_file);
    }

    return array($status, $results, $stderr);
}

/**
 * Returns TRUE if a story has at least one video clip associated with it, otherwise returns FALSE.
 * @param   int   $story_id
 * @return  bool  TRUE if story has video, FALSE if it does not
 */
function story_has_video($story_id = '')
{
    $q  = 'SELECT COUNT(*) AS media_count '
        . 'FROM props_media_story_xref AS pmsx, props_media AS pm '
        . 'WHERE story_id = ' . $story_id . ' '
        . '  AND pmsx.media_id = pm.media_id '
        . '  AND pm.group_id = ' . PROPS_MEDIA_VIDEO;
    $result = sql_query($q);
    $row = mysql_fetch_array($result);
    
    if ($row['media_count']) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Returns TRUE if a story has at least one audio clip associated with it, otherwise returns FALSE.
 * @param   int   $story_id
 * @return  bool  TRUE if story has audio, FALSE if it does not
 */
function story_has_audio($story_id = '')
{
    $q  = 'SELECT COUNT(*) AS media_count '
        . 'FROM props_media_story_xref AS pmsx, props_media AS pm '
        . 'WHERE story_id = ' . $story_id . ' '
        . '  AND pmsx.media_id = pm.media_id '
        . '  AND pm.group_id = ' . PROPS_MEDIA_AUDIO;
    $result = sql_query($q);
    $row = mysql_fetch_array($result);
    
    if ($row['media_count']) {
        return TRUE;
    } else {
        return FALSE;
    }
}

?>
