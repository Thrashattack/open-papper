<?php

$debug = FALSE;
$cache = TRUE;
$image_functions = 'auto';
$image_quality = '75';

require_once('../../lib/media.php');

// Init
$root = dirname(__FILE__);
$root = str_replace('\\', '/', $root).'/';

$id = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : '';

$path = (isset($_REQUEST['path'])) ? str_replace('-', '/', $_REQUEST['path']).'/' : '';
$path = preg_replace('|[^0-9/]|', '', $path);
$path = str_replace('-', '/', $path);

$mode = (isset($_REQUEST['mode'])) ? $_REQUEST['mode'] : '';
$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : '';

$width = (isset($_REQUEST['width'])) ? intval($_REQUEST['width']) : '';
$height = (isset($_REQUEST['height'])) ? intval($_REQUEST['height']) : '';

$source = sprintf('%s%s%08d-%s.%s', $root, $path, $id, 'original', $type);

// Force valid mode
switch ($mode) {
    case 'absolute':
        break;

    default:
        // constrain
        $mode = 'constrain';
        break;
}

$thumb_path = sprintf('%s%s%s%08d-%s-%sx%s.%s', $root, $path, 'cache-', $id, $mode, $width, $height, 'jpg');

// Error checks
if (empty($id) || empty($type) || !array_key_exists($type, $GLOBALS['PROPS_MEDIA_TYPES'])) {
    header("HTTP/1.0 404 Not Found");
    if ($debug == TRUE) {
        echo __LINE__.' '.'EMPTY ID or INVALID TYPE!';
    }
    exit;
}

// Output original when there are no given dimensions
if (empty($width) && empty($height)) {
    // Get original
    if ($debug == TRUE) {
        echo __LINE__.' '."SHOW ORIGINAL: '$source'<br />\n";
    } else {
        // Log view





        // Set content type
        if (isset($_REQUEST['download'])) {
            // Force download
            if ($debug == TRUE) {
                echo __LINE__.' '."SET CONTENT TYPE / FORCE DOWNLOAD: 'application/octet-stream'<br />\n";
            } else {
                header('Content-Type: application/octet-stream');
            }
        } else {
            // Display image in the browser
            if ($debug == TRUE) {
                echo __LINE__.' '."SET CONTENT TYPE FOR '$type': '".$GLOBALS['PROPS_MEDIA_TYPES'][$type]['mime_type']."'<br />\n";
            } else {
                header('Content-Type: '.$GLOBALS['PROPS_MEDIA_TYPES'][$type]['mime_type']);
            }
        }

        // Show media file
        readfile($source);
    }
    exit;
}

// Get cached thumbnail
if ($cache == TRUE && is_file($thumb_path)) {
    // Show cached thumbnail
    if ($debug == TRUE) {
        echo __LINE__.' '."SET CONTENT TYPE: '".$GLOBALS['PROPS_MEDIA_TYPES']['jpg']['mime_type']."'<br />\n";
        echo __LINE__.' '."SHOW CACHED THUMBNAIL: '$thumb_path'<br />\n";
    } else {
        // Display image in the browser
        header('Content-Type: '.$GLOBALS['PROPS_MEDIA_TYPES']['jpg']['mime_type']);
        readfile($thumb_path);
    }
    exit;
}

// Generate thumbnail
switch ($GLOBALS['PROPS_MEDIA_TYPES'][$type]['group_id']) {
    case PROPS_MEDIA_GRAPHICS:
        // Get source dimensions
        $source_size = @getimagesize($source);
        $source_width = $source_size[0];
        $source_height = $source_size[1];

        // Calculate thumb dimensions
        list($thumb_width, $thumb_height) = media_dimensions($source_width, $source_height, $width, $height, $mode);

        // Generate thumbnail
        switch ($image_functions) {

            case "auto":
            case "gd":
                // Try the GD library
                if (function_exists('gd_info')) {
                    $source_id = FALSE;
                    if (($type == 'jpg') && (imagetypes() & IMG_JPG)) {
                        $source_id = imagecreatefromjpeg($source);
                    } elseif (($type == 'png') && (imagetypes() & IMG_PNG)) {
                        $source_id = imagecreatefrompng($source);
                    } elseif (($type == 'gif') && (imagetypes() & IMG_GIF)) {
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
                        $target_id = $img_create_function($thumb_width, $thumb_height);

                        // Resize the original image and copy it into the just created image object
                        imagecopyresampled($target_id, $source_id, 0,0,0,0, $thumb_width, $thumb_height, $source_width, $source_height);

                        // Free up memory
                        imagedestroy($source_id);

                        if ($cache == TRUE) {
                            $status = imagejpeg($target_id, $thumb_path, $image_quality);
                            // Free up more memory
                            imagedestroy($target_id);

                            if ($status == TRUE) {
                                if ($debug == TRUE) {
                                    echo __LINE__.' '."SET CONTENT TYPE: '".$GLOBALS['PROPS_MEDIA_TYPES']['jpg']['mime_type']."'<br />\n";
                                    echo __LINE__.' '."SHOW CACHED THUMBNAIL: '$thumb_path'<br />\n";
                                } else {
                                    header('Content-Type: '.$GLOBALS['PROPS_MEDIA_TYPES']['jpg']['mime_type']);
                                    readfile($thumb_path);
                                }

                                exit;
                            }
                        } else {
                            if ($debug == TRUE) {
                                echo __LINE__.' '."SET CONTENT TYPE: '".$GLOBALS['PROPS_MEDIA_TYPES']['jpg']['mime_type']."'<br />\n";
                                echo __LINE__.' '."SHOW GENERATED THUMBNAIL FROM MEMORY<br />\n";
                            } else {
                                header('Content-Type: '.$GLOBALS['PROPS_MEDIA_TYPES']['jpg']['mime_type']);
                                $status = imagejpeg($target_id, NULL, $image_quality);
                            }

                            // Free up more memory
                            imagedestroy($target_id);

                            if ($status == TRUE) {
                                exit;
                            }
                        }
                    }
                }
                // There where probably errors. Fallback to ImageMagick.

            default:
        }

        // Save thumbnail if needed

        //break;

    case PROPS_MEDIA_VIDEO:
        // Only generate tumbnail if cache is TRUE
        if ($cache == TRUE) {
            // Get source dimensions
            // Calculate thumb dimensions
            // Generate thumbnail
            // Save thumbnail if needed

            //break;
        } else {
            if ($debug == TRUE) {
                echo __LINE__.' '."SKIPPED THUMBNAIL GENERATION FOR '$type'<br />\n";
            }
        }

    default:
        // Show default thumbnail
        if ($debug == TRUE) {
            echo __LINE__.' '."SET CONTENT TYPE: '".$GLOBALS['PROPS_MEDIA_TYPES']['png']['mime_type']."'<br />\n";
            echo __LINE__.' '."SHOW DEFAULT THUMBNAIL: '".$GLOBALS['PROPS_MEDIA_TYPES'][$type]['icon']."'<br />\n";
        } else {
            // Set content type
            header('Content-Type: '.$GLOBALS['PROPS_MEDIA_TYPES']['png']['mime_type']);
            readfile($root . $GLOBALS['PROPS_MEDIA_TYPES'][$type]['icon']);
        }
        break;
}

// Delete thumbnail if needed
if ($cache != TRUE && is_file($thumb_path)) {
    if ($debug == TRUE) {
        echo __LINE__.' '."DELETE THUMBNAIL: '$thumb_path'<br />\n";
    } else {
        unlink($thumb_path);
    }
}

exit;











// We'll be outputting a PDF
header('Content-type: application/pdf');

// It will be called downloaded.pdf
header('Content-Disposition: attachment; filename="downloaded.pdf"');

// The PDF source is in original.pdf
readfile('original.pdf');
/*
Your code looks fine to me. Unluckily, Internet Explorer is a deprecated
buggy browser that'll force you to many workarounds. Some people recommend
to use a generic content type to force download:

application/octet-stream

Also, check whether you're using sessions, cookies or something else that's
adding headers: IE often gets confused.
*/



// readfile and fpassthru are about 55% slower than doing
// a loop with "feof/echo fread".




header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // some day in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-type: application/x-download");
header("Content-Disposition: attachment; filename={$new_name}");
header("Content-Transfer-Encoding: binary");





    //Let's generate a totally random string using md5
    $md5 = md5(rand(0,999));
    //We don't need a 32 character long string so we trim it down to 5
    $pass = substr($md5, 10, 5);

    //Set the image width and height
    $width = 100;
    $height = 20;

    //Create the image resource
    $image = ImageCreate($width, $height);

    //We are making three colors, white, black and gray
    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 204, 204, 204);

    //Make the background black
    ImageFill($image, 0, 0, $black);

    //Add randomly generated string in white to the image
    ImageString($image, 3, 30, 3, $pass, $white);

    //Throw in some lines to make it a little bit harder for any bots to break
    ImageRectangle($image,0,0,$width-1,$height-1,$grey);
    imageline($image, 0, $height/2, $width, $height/2, $grey);
    imageline($image, $width/2, 0, $width/2, $height, $grey);

    //Tell the browser what kind of file is come in
    header("Content-Type: image/jpeg");

    //Output the newly created image in jpeg format
    ImageJpeg($image);

    //Free up resources
    ImageDestroy($image);
?>