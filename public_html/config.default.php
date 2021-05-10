<?php
/**
 * Default config file
 *
 * Rename this file to config.php and copy it to the base_dir (where readme.txt
 * is / better security) or the public_html (in some configurations www) dir.
 *
 * NOTE: In several section you can use auto detected defined values:
 * <ul>
 *   <li><b>PROPS_URL</b> - Detected URL with trailing slash. You can use this
 *      to allow props serving several URL's form one installation.<br />
 *      e.g. "http://www.example.com/props/"
 *   </li>
 *   <li><b>PROPS_ROOT</b> -  Base installation path with trailing slash.<br />
 *      e.g. "/var/www/htdocs/www.yoursite.com"
 *   </li>
 * </ul>
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
 * @version     $Id: config.default.php,v 1.37 2008/01/07 15:35:07 roufneck Exp $
 */

/******************************************************************************
 * General site settings
 *****************************************************************************/

    /**
     * Publication name (site name)
     */
    props_setkey('config.publication.name', 'PROPS Gazette');

    /**
     * Default copyright
     *
     * If you want the copyright field on the "add story" admin screen to be
     * filled in by default, fill in the value here. Otherwise leave it blank.
     */
    props_setkey('config.default.copyright', 'PROPS Gazette');

    /**
     * Default story access level [free|reg_required|paid_archives]
     *
     * This setting indicates which access level should be selected by default
     * on the "add story" admin screen. Possible values:
     * - <b>free</b> - most new articles will be freely available
     * - <b>reg_required</b> - most new articles will require user registration
     * - <b>paid_archives</b> - most new articles will be sent directly to the
     *      paid archives
     */
    props_setkey('config.default.story_access_level', 'free');

    /**
     * Default timezone
     *
     * Needed for date/time functions when using PHP 5.1+. For more info see:
     * {@link  http://www.php.net/manual/en/function.date-default-timezone-set.php  date_default_timezone_set}.
     * For the right value see {@link  http://www.php.net/manual/en/timezones.php}
     */
    props_setkey('config.default.timezone', 'GMT');

    /**
     * Internationalization
     *
     * Default site language settings and strings. Look in the locale dir for
     * the possible values.
     */
    props_setkey('config.i18n', 'en_US');

    /**
     * OpenID login support [TRUE|FALSE]
     *
     * Enable OpenID support.
     * {@link  http://openid.net/  More information about OpenID}.
     *
     * PHP requirements for OpenID support:
     * - Enable either the GMP extension or Bcmath extension. (GMP is STRONGLY
     *   recommended because it's MUCH faster!)
     * - Enable the CURL extension. (This is recommended)
     */
    props_setkey('config.openid.enable', TRUE);

    /**
     * Path to the randomness source
     *
     * To set a source of randomness, set this to the path to the randomness
     * source. If your platform does not provide a secure randomness source, the
     * library can operate in pseudorandom mode, but it is then vulnerable to
     * theoretical attacks. If you wish to operate in pseudorandom mode, define
     * Auth_OpenID_RAND_SOURCE to NULL.
     */
    props_setkey('config.openid.rand_source', NULL);

    /**
     * Log statistics
     */
    props_setkey('config.stats.log', TRUE);

    /**
     * Keep statistics for maximum x days
     */
    props_setkey('config.stats.max_days', 730);

    /**
     * Single quotes template parameters [TRUE|FALSE]
     *
     * <code>
     * // With single quotes (TRUE)
     * {storylist minweight='50' startrow='3' format='<li><a href="%u">%h</a></li>' prepend='<ul>' append='</ul>'}
     * // With double quotes (FALSE / old behaviour)
     * {storylist minweight="50" startrow="3" format="<li><a href=\"%u\">%h</a></li>" prepend="<ul>" append="</ul>"}
     * </code>
     */
    props_setkey('config.template.singlequotes', TRUE);

    /**
     * Compress TinyMCE WYSIWYG editor [TRUE|FALSE]
     *
     * This will compress all files need for the editor. It will save bandwith
     * and requests. The directory ./scripts/tiny_mce/ must have write access.
     */
    props_setkey('wysiwyg.tiny_mce.compress', FALSE);

    /**
     * TinyMCE WYSIWYG editor default buttons
     *
     * This will set the default buttons of TinyMCE
     */
    props_setkey('wysiwyg.tiny_mce.buttons1', 'bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,separator,cut,copy,paste,pastetext,pasteword,separator,undo,redo,separator,bullist,numlist,separator,link,unlink,separator,hr,removeformat,cleanup,code');
    props_setkey('wysiwyg.tiny_mce.buttons2', '');

    /**
     * TinyMCE WYSIWYG editor default plugins
     *
     * This will set the default plugins of TinyMCE
     */
    props_setkey('wysiwyg.tiny_mce.plugins', 'paste');

    /**
     * Custom PHP error log.
     *
     * Leave it blank to use the system default.
     * The log file must have write access.
     */
    props_setkey('config.error_log', '');

    /**
     * Debug mode [TRUE|FALSE]
     *
     * Gives extra information on error messages. For production sites 'FALSE'
     * is recommended.
     */
    props_setkey('config.debug_mode', FALSE);

/******************************************************************************
 * Database parameters
 *****************************************************************************/

    /**
     * Database server type [MySQL]
     *
     * Currently only MySQL is supported.
     */
    props_setkey('config.db.type', 'mysql');

    /**
     * Database name
     *
     * Name of the PROPS database.
     */
    props_setkey('config.db.name', 'props');

    /**
     * Database server
     *
     * Use 'localhost' if the database server is on the same  machine
     */
    props_setkey('config.db.host', 'localhost');

    /**
     * Database connection login settings
     */
    props_setkey('config.db.user', 'props');
    props_setkey('config.db.password', 'password');

    /**
     * Database sessions [TRUE|FALSE]
     *
     * Saves sessions in the database for extra security and functionality.
     */
    props_setkey('config.db.sessions', FALSE);

    /**
     * UTF8 detection [TRUE|FALSE]
     *
     * This option detects the collation of the database and will output the
     * data to UTF-8. UTF-8 is a standard mechanism used by Unicode for
     * encoding wide character values into a byte stream.
     *
     * PROPS will send the correct 'Content-Type' and 'charset' headers.
     * Don't add <meta http-equiv="Content-Type" content="...; charset=..." />
     * to your templates if you set this option to true.
     */
    props_setkey('config.UTF8-detection', FALSE);
    
/******************************************************************************
 * Directory paths
 *****************************************************************************/

    /**
     * Base directory
     *
     * Base directory in which PROPS is installed, WITH trailing slash. Not the
     * public_html directory, but the one containing README, lib, etc. Use
     * PROPS_ROOT for auto detection.
     */
    props_setkey('config.dir.root', PROPS_ROOT);

    /**
     * Cache dir
     *
     * Directory where cached files are stored. Make sure the web server user
     * account has permissions to write to the this directory.
     *
     * This directory is needed for OpenID, template caching and video support.
     */
    props_setkey('config.dir.cache', PROPS_ROOT.'cache/');

    /**
     * Modules dir
     *
     * Directory which contains application modules.
     */
    props_setkey('config.dir.modules', PROPS_ROOT.'modules/');

    /**
     * Templates dir
     *
     * Directory in which templates are located.
     */
    props_setkey('config.dir.templates', PROPS_ROOT.'templates/');

    /**
     * Includes dir
     *
     * Directory in which .inc include files are stored.
     */
    props_setkey('config.dir.includes', PROPS_ROOT.'includes/');

    /**
     * Media dir
     *
     * Directory where media files will be stored. If you will be uploading
     * media files via the web-based admin screens, make sure the web server
     * user account has permissions to write to this directory.
     */
    props_setkey('config.dir.media', PROPS_ROOT.'public_html/media/');

    /**
     * Auto create template dirs [TRUE|FALSE]
     *
     * If this is set to TRUE, section subdirectories in the templates
     * directory will be automatically created and removed as site sections are
     * added and deleted. (Section subdirectories which contain files will not
     * be deleted.) Make sure the web server user account has permissions to
     * write to the this directory if you enable this.
     */
    props_setkey('config.dir.auto_create', FALSE);

/******************************************************************************
 * URLs and URL behaviour
 *****************************************************************************/

    /**
     * Base URL
     *
     * Should point to public_html directory *with* trailing slash. Use
     * PROPS_URL for auto detection.
     *
     * Example:
     * <code>
     * 'http://www.example.com/'
     * 'http://www.example.com/~username/'
     * </code>
     */
    props_setkey('config.url.root', PROPS_URL);

    /**
     * Media URL
     *
     * Should point to the media directory you specified in the previous
     * config section.
     */
    props_setkey('config.url.media', PROPS_URL.'media/');

    /**
     * Scripts URL
     *
     * Should point to the scripts directory (JavaScript).
     */
    props_setkey('config.url.scripts', PROPS_URL.'scripts/');

    /**
     * Base SSL URL
     *
     * Should point to public_html directory *with* trailing slash. If your
     * server does not support SSL, or you are not using the paid archives
     * functionality, set this the same as the base_url.
     *
     * Example:
     * <code>
     * 'https://www.example.com/'
     * 'https://www.example.com/~username/'
     * </code>
     */
    props_setkey('config.url.ssl', PROPS_URL);

    /**
     * Base admin URL
     *
     * Should point to public_html/admin directory, *with* trailing slash.
     * HTTPS is OK. Use PROPS_URL for auto detection, otherwise enter complete
     * URL.
     */
    props_setkey('config.url.admin', PROPS_URL.'admin/');

    /**
     * Static URLs [TRUE|FALSE]
     *
     * If set to FALSE, story URLs will appear as ?op=displaystory&story_id=621&format=html.
     *
     * If set to TRUE, story URLs will appear as /content/6f6e-6c6f63616c[...]-x.html,
     * so they will be indexed by search engines. For this to work, the
     * following lines must be added to your Apache httpd.conf file:
     *
     * <code>
     * <Location /content>
     *     ForceType application/x-httpd-php
     * </Location>
     * </code>
     *
     * You must also include <base href="{base_url}"> within the HEAD section
     * of all HTML documents, to force all images to be loaded relative to the
     * home page URL rather than the faux static URL.
     */
    props_setkey('config.url.static', FALSE);

/******************************************************************************
 * Media file configuration
 *****************************************************************************/

    /**
     * Image functions [auto|gd|imagemagick]
     *
     * Image functions detection. Possible values:
     * - <b>auto</b> - Automatic detection. This checks for GD first and falls
     *               back to Imagemagick.
     * - <b>gd</b> - PHP GD functions. PHP must be compiled with
     *               the --with-gd[=DIR] option.
     * - <b>imagemagick</b> - Use Imagemagick. You need to configure the
     *               Imagemagick paths below.
     */
    props_setkey('config.media.image.functions', 'auto');

    /**
     * Thumbnail quality
     *
     * Image quality used for creating thumbnails. Set to a number between 0
     * (lowest quality) to 100 (best quality).
     */
    props_setkey('config.media.image.quality', '75');

    /**
     * Maximum image width
     *
     * Can save diskspace. Size is in pixels. Set to 0 for no maximum.
     */
    props_setkey('config.media.image.max_width', '0');

    /**
     * Maximum image height
     *
     * Can save diskspace. Size is in pixels. Set to 0 for no maximum.
     */
    props_setkey('config.media.image.max_height', '0');

    /**
     * Imagemagick mogrify path, used for resizing images
     *
     * Tip: To find the location, type 'whereis mogrify' at the Unix shell
     * prompt. If you don't have shell access, try these common locations:
     * /usr/bin, /usr/X11R6/bin, /usr/local/bin and /usr/bin/X11 . If that
     * doesn't work, ask your hosting provider.
     */
    props_setkey('config.media.imagemagick.mogrify', '/usr/bin/mogrify');

    /**
     * Ffmpeg path, used for video processing
     *
     * To find the location, type 'whereis ffmpeg' at the Unix shell.
     */
    props_setkey('config.media.ffmpeg_path', '');

/******************************************************************************
 * Email configuration
 *****************************************************************************/

    /**
     * Mailer type [smtp|sendmail|qmail|mail]
     */
    props_setkey('config.mail.mailer', 'mail');

    /**
     * Mail host
     *
     * Default is localhost. Use 'ssl://smtp.gmail.com' for gmail.
     */
    props_setkey('config.mail.host', 'localhost');

    /**
     * Mail port
     *
     * Default is 25. Use '465' for gmail.
     */
    props_setkey('config.mail.port', '25');

    /**
     * SMTP authentication
     *
     * Leave empty for no authentication.
     */
    props_setkey('config.mail.smtp.user', '');
    props_setkey('config.mail.smtp.password', '');

    /**
     * Mail WordWrap
     *
     * Wrap words after xx characters. Default is 0 (no WordWrap).
     */
    props_setkey('config.mail.wordwrap', '0');

    /**
     * Email from name
     *
     * This name will be used as the "From" name whenever an email is sent out.
     */
    props_setkey('config.mail.from_name', 'The PROPS Gazette team');

    /**
     * Email from address
     *
     * This email address will be used as the "From" address whenever an email
     * is sent out.
     *
     * Related Note: On *nix systems which run sendmail, you'll want to add the
     * user that the webserver runs as (ex: apache, nobody, httpd) to
     * /etc/mail/trusted-users. Otherwise Sendmail will add a warning header to
     * outgoing bulletins, and spam filters will be more likely to tag them as
     * Spam.
     */
    props_setkey('config.mail.from_address', 'info@example.com');

    /**
     * Email bounce address
     *
     * This email address will receive the bounce messages whenever an email is
     * sent out.
     */
    props_setkey('config.mail.bounce_address', 'info@example.com');

/******************************************************************************
 * Commerce configuration
 *****************************************************************************/

    /**
     * Paid archives [TRUE|FALSE]
     *
     * If this is set to TRUE, users will be requested to pay for access to
     * stories which are marked as "paid archives items". In order for this to
     * work your server must support SSL, the appropriate secure https: URL
     * must be listed in the above section of this config file, you must have
     * an account with a credit card payment gateway (currently only VeriSign
     * Payflow Pro is supported). It also helps if PHP is compiled with the
     * --with-pfpro=[DIR] option, however that is not required.
     */
    props_setkey('config.archives.paid', TRUE);

    /**
     * Credit Card gateway
     *
     * The credit card processing gateway that PROPS uses. Currently only
     * Verisign Payflow Pro (verisign-pfp) is supported.
     */
    props_setkey('config.commerce.gateway', 'verisign-pfp');

    /**
     * PayFlow Pro path
     *
     * If this is left blank, PROPS will attempt to use PHP's built-in VeriSign
     * PayFlow Pro support, which is only available when PROPS is compiled with
     * --with-pfpro.
     *
     * If you would like to instead use the command line pfpro utility (part of
     * the VeriSign SDK) enter the path to it here. Note that this is slightly
     * less secure than the built-in one to PHP. See {@link pfpro.php} for
     * details.
     */
    props_setkey('config.pfpro.path', '');

    /**
     * PayFlow Pro certs
     *
     * If you set the above option, you will need to specify the path to the
     * VeriSign 'certs' directory here. (The certs dir is included as part of
     * the PayFlow Pro SDK.)
     */
    props_setkey('config.pfpro.certs', '');

    /**
     * PayFlow Pro hostname
     *
     * Use test-payflow.verisign.com while you are testing, and
     * payflow.verisign.com when you are ready to go live.
     */
    props_setkey('config.pfpro.hostname', 'test-payflow.verisign.com');

    /**
     * PayFlow Pro partner code
     */
    props_setkey('config.pfpro.partner', 'VeriSign');

    /**
     * PayFlow Pro username
     */
    props_setkey('config.pfpro.user', 'your verisign pfp username');

    /**
     * PayFlow Pro password
     */
    props_setkey('config.pfpro.password', 'your verisign pfp password');

/******************************************************************************
 * Do not edit below this line unless you *fully* understand what
 * you are doing
 *****************************************************************************/

    /**
     * MIME types
     *
     * This array contains a list of MIME types PROPS can serve, and their
     * associated template extension.
     */
    props_setkey('config.mime_types', array(
        'html'=>'text/html',
        'text'=>'text/plain',
        'print'=>'text/html',
        'rss'=>'application/rss+xml'
    ));
?>
