<?php
/**
 * Frontpage index file
 *
 * @package     PROPS
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
 * @version     $Id: index.php,v 1.31 2008/01/07 16:17:07 roufneck Exp $
 */

// Load common functions
require_once('../lib/common.php');

// We'll need the template lib
props_loadLib('templates');

// Initialize the frontpage
props_init_frontpage();

// Roughly once out of every 500 page views, run the auto-archiving
// function. (this is a crude technique for running periodic
// housekeeping functions without having access to cron)
if (rand(1,500) == 250) {
    props_loadLib('sections');
    sections_auto_archive();
}

// If a request-handler script exists for this module, call it
list($module) = explode('-', props_getkey('request.cmd'));
$request_handler = props_getkey('config.dir.modules') . $module . '/requesthandler.php';
if (is_file($request_handler)) {
    require_once($request_handler);
}

// Log statistics
if (props_getkey('config.stats.log') && props_getkey('request.preview') != TRUE) {
    require_once(props_getkey('config.dir.modules') . '/stats/requesthandler.php');
}

/**
 * Load the appropriate template based on the specified section (if any),
 * request module, command, and format (MIME type), then replace all
 * props-tags with dynamic data returned by a corresponding function and
 * output the page content
 */

// Retrieve contents of template into a string
$output_buffer = template_get_content(
    props_getkey('request.section_id'),
    props_getkey('request.cmd'),
    props_getkey('request.format'),
    props_getkey('request.template')
    );

// Merge the template with the glossary to get the final output
$output_buffer = template_parse($output_buffer);

// Output headers
header('X-PUBLISHER: PROPS ' . PROPS_VERSION . ' - Open Source News Publishing Platform');
header('X-PUBLISHER-URL: http://props.sourceforge.net/');
if (props_getkey('db.charset') == 'utf8') {
    header('Content-Type: ' . props_getkey('request.mime_type') . '; charset=UTF-8');
} else {
    header('Content-Type: ' . props_getkey('request.mime_type') . '; charset=ISO-8859-1');
}

// Finaly output the content
print $pre_output;
print $output_buffer;

// Update pageID
props_pageID();

?>
