<?php
/**
 * Lib - common functions
 *
 * Main libarary that initiates all basic functions and constants.
 *
 * @package     api
 * @subpackage  common
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
 * @version     $Id: common.php,v 1.66 2008/04/24 09:44:31 roufneck Exp $
 */

// Init some vars
$GLOBALS['str'] = array();
$GLOBALS['PROPS_ERRORSTACK'] = array();
$GLOBALS['PROPS_DEBUGSTACK'] = array();
$GLOBALS['PROPS_REGISTRY'] = array();

// Extend include_path
$libs_path = dirname(__FILE__);
$libs_path = str_replace('\\', '/', $libs_path);
ini_set('include_path', $libs_path . PATH_SEPARATOR . ini_get('include_path'));

// Construct base path & base URL
$base_path = dirname($libs_path) . '/';

// Construct base path & base URL
$base_url = explode('?', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/', 2);
$base_url = explode('content/', 'http://'.preg_replace('/[\/]+/', '/', $base_url[0]));
$base_url = $base_url[0];
$base_url = str_replace('/index.php/', '/', $base_url);
$base_url = str_replace('/index.php', '/', $base_url);
$base_url = str_replace('/admin', '', $base_url);

// Set the argument separator to full XHTML conformance
// Can't use that because some configurations don't accept this.
//ini_set('arg_separator', '&amp;');

/**
 * Defines
 */
// Validation constants
define('VALIDATE_NUM',          '0-9');
define('VALIDATE_SPACE',        '\s');
define('VALIDATE_ALPHA_LOWER',  'a-z');
define('VALIDATE_ALPHA_UPPER',  'A-Z');
define('VALIDATE_ALPHA',        VALIDATE_ALPHA_LOWER . VALIDATE_ALPHA_UPPER);
define('VALIDATE_EALPHA_LOWER', VALIDATE_ALPHA_LOWER . 'áéíóúàèìòùäëïöüâêîôûñçþæð');
define('VALIDATE_EALPHA_UPPER', VALIDATE_ALPHA_UPPER . 'ÁÉÍÓÚÀÈÌÒÙÄËÏÖÜÂÊÎÔÛÑÇÞÆÐ');
define('VALIDATE_EALPHA',       VALIDATE_EALPHA_LOWER . VALIDATE_EALPHA_UPPER);
define('VALIDATE_PUNCTUATION',  VALIDATE_SPACE . '\.,;\:&"\'\?\!\(\)');
define('VALIDATE_NAME',         VALIDATE_EALPHA . VALIDATE_SPACE);
define('VALIDATE_STREET',       VALIDATE_NAME . '/\\ºª');
define('VALIDATE_CURRENCY',     VALIDATE_NUM . '\.');
define('VALIDATE_USERNAME',     '\._\-a-zA-Z0-9');
define('VALIDATE_MD5',          '0-9a-f');
define('VALIDATE_INT',          -1); // Force return intval($value)
define('VALIDATE_BOOL',         -2); // Force return TRUE/FALSE
define('VALIDATE_ARRAY',        -3); // Check and force return array
define('VALIDATE_DATE',         -4); // Checks for valid date
define('VALIDATE_EMAIL',        -5); // Checks for valid email address
define('VALIDATE_TEXT',         -6); // Returns string with html tags stripped out
define('VALIDATE_HTML',         -7); // Everything is allowed ???

// Props constants
define('PROPS_ROOT',            $base_path);
define('PROPS_LIBS',            $libs_path.'/');
define('PROPS_URL',             $base_url);
define('PROPS_VERSION',         '0.8');
define('PROPS_POWEREDBY',       '<a title="PROPS - Publishing Platform" href="http://props.sourceforge.net/" style="font-size: 10px; text-decoration: none; font-family: Arial, Helvetica, sans-serif;"><span style="border: 1px solid #000066; background-color: #000066; color: #ffffff;">&nbsp;<strong>Powered by</strong>&nbsp;</span><span style="border: 1px solid #000066; background-color: #d8eef9; color: #000066;">&nbsp;<strong>PROPS</strong>&nbsp;</span></a>');

// Get magic quotes
define('PROPS_MAGIC_QUOTES',    get_magic_quotes_gpc());

// Constants for the various publication statuses
define('PUBSTATUS_INEDITQUEUE', 1);
define('PUBSTATUS_STAGED',      2);
define('PUBSTATUS_PUBLISHED',   3);
define('PUBSTATUS_ARCHIVED',    4);

// Constants for the various article access levels
define('ACCESS_FREE', 1);
define('ACCESS_REG_REQUIRED',   2);
define('ACCESS_PAID_ARCHIVES',  3);

// The root (front page) section_id will always be 1
define('FRONTPAGE_SECTION_ID',  1);

// The user ID used for anonymous comments, if they are enabled
define('ANONYMOUS_USER_ID',     0);

// Line feeds
define('BR', "<br />\n");
define('LF', "\n");

// Define error codes
if (!defined('E_STRICT')) define('E_STRICT',  0x00000800); // PHP5 compatibility
define('ERROR_USER',    E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
define('ERROR_NOTICE',  E_NOTICE | E_USER_NOTICE | E_STRICT);
define('ERROR_WARNING', E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING);
define('ERROR_DIE',     E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
define('ERROR_LOG',     ERROR_DIE | ERROR_WARNING | ERROR_USER);
define('E_SQL',           0x00010000); // SQL error
define('E_404',           0x00020000); // 404 Error
define('E_DEBUG',         0x00040000); // Message for debug system only (ex. benchmark and test stuff)
define('PROPS_E_NOTICE',  0x00080000);
define('PROPS_E_WARNING', 0x00100000);

$GLOBALS['PROPS_ERROR_CODES'] = array(
        PROPS_E_WARNING => 'MESSAGE WARNING',
        PROPS_E_NOTICE  => 'MESSAGE NOTICE',
        E_ERROR         => 'PHP FATAL ERROR',
        E_WARNING       => 'PHP WARNING',
        E_PARSE         => 'PHP PARSING ERROR',
        E_NOTICE        => 'PHP NOTICE',
        E_CORE_ERROR    => 'PHP CORE ERROR',
        E_CORE_WARNING  => 'PHP CORE WARNING',
        E_COMPILE_ERROR => 'PHP COMPILE ERROR',
        E_COMPILE_WARNING=>'PHP COMPILE WARNING',
        E_USER_ERROR    => 'PROPS ERROR',
        E_USER_WARNING  => 'PROPS WARNING',
        E_USER_NOTICE   => 'PROPS NOTICE',
        E_STRICT        => 'STRICT ERROR', // PHP 5.0.0RC3+
        E_SQL           => 'SQL ERROR',
        E_404           => '404 ERROR',
        E_DEBUG         => 'DEBUG INFO',
);

/**
 * Dynamic library loader
 *
 * Loads function/class definitions on the fly as needed. This way we don't need
 * to take the performance hit of loading in a bunch of junk with every page
 * load if we don't really need it.
 *
 * Example:
 * <code>
 * // Load libs
 * props_loadLib('editions,templates');
 * </code>
 *
 * @param  string  $libs  comma seperated list of libs that needs to be loaded.
 * @param  string  $dir   overrules the default libs dir
 */
function props_loadLib($libs, $dir = FALSE)
{
    foreach (split(',', $libs) AS $lib) {
        //props_timerStart('loadLib');
        if ($lib == 'database') {
            require_once(PROPS_LIBS.'database/'.props_getkey('config.db.type').'.php');
        } elseif (!empty($dir)) {
            require_once($dir.$lib.'.php');
        } else {
            require_once(PROPS_LIBS.$lib.'.php');
        }
        props_debug('LOADLIB: '.$lib);
    }
}

/**
 * Set the value of a given registry key
 *
 * Example:
 * <code>
 * props_setkey('module_name.path.to.key', 'value');
 * </code>
 *
 * @param   string  $key  registry key
 * @param   string  $val  value
 */
function props_setkey($key, $val)
{
    $key = str_replace('.', '\'][\'', $key);
    eval('$GLOBALS[\'PROPS_REGISTRY\'][\'' . $key . '\'] = $val;');
}

/**
 * Returns the value of a given registry key
 *
 * @param   string  $key  registry key
 * @return  string  registry key value
 */
function props_getkey($key)
{
    $key = str_replace('.', '\'][\'', $key);
    $value = @eval('return $GLOBALS[\'PROPS_REGISTRY\'][\'' . $key . '\'];');
/*
    eval('
        if ( isset($GLOBALS[\'PROPS_REGISTRY\'][\'' . $key . '\']) ) {
            $value = $GLOBALS[\'PROPS_REGISTRY\'][\'' . $key . '\'];
        } else {
            $value = FALSE;
            trigger_error("REGISTRY_KEY `[\'' . $key . '\']` not set.", E_USER_NOTICE);
        }');
/**/
    return $value;
}

/**
 * Returns a translated string if exists, otherwise the given string.
 * Strings that are captured with "" are detected by system_update_i18n().
 *
 * Limitations: You can't use "double quotes" in translation strings.
 *
 * Example:
 * <code>
 * // Auto added to local files
 * echo props_gettext(&quot;String to translate&quot;);
 * // Don't add to locales, just translate
 * echo props_gettext('.adminmain.mainmenu');
 * </code>
 *
 * @see function system_update_i18n
 * @param   string  $string  String to translate
 * @return  string  translated or original string
 */
function props_gettext($string)
{
    if (!empty($GLOBALS['str'][$string])) {
        return $GLOBALS['str'][$string];
    } else {
        return $string;
    }
}

/**
 * Returns an errormessage of a request (POST/GET) variable checked by
 * props_getrequest (if any).
 *
 * Example:
 * <code>
 * $output .='<input name="fieldname" type="text" value="' . $fieldname . '" />' . props_geterror('fieldname') . '</td>'.LF;
 * </code>
 *
 * @see function props_getrequest
 * @param   string  $var  field name
 * @return  string  error in html format: <p class="formerror" title="Valid format [a-Z]">Error message</p>
 */
function props_geterror($var)
{
    if (isset($GLOBALS['PROPS_ERRORSTACK'][$var])) {
        $title = (isset($GLOBALS['PROPS_ERRORSTACK'][$var]['title'])) ? 'title="'.htmlspecialchars($GLOBALS['PROPS_ERRORSTACK'][$var]['title']).'"' : '';
        return '<span class="formerror" ' . $title . '>'.$GLOBALS['PROPS_ERRORSTACK'][$var]['message'].'</span>';
    } else {
        return '';
    }
}

/**
 * Process request values
 *
 * @param   mixed  $value  Array or string to process
 * @return  mixed  Processed array or string
 */
function props_grab_request($value)
{
    if (is_array($value)) {
        $value = array_map('props_grab_request', $value);
    } else {
        // Strip slashes
        if (PROPS_MAGIC_QUOTES) {
            $value = stripslashes($value);
        }

        // Trim value
        $value = trim($value);
    }

    // Return and trim value
    return $value;
}

/**
 * Validate a value of a request (POST/GET) variable.
 * The variable is checked when $validate is set.
 *
 * Possible options:
 * - <b>!EMPTY</b>   - Value may not be empty
 * - <b>MINxxx</b>   - Minimum length of xxx required
 * - <b>MAXxxx</b>   - Maximum length of xxx allowed
 * - <b>SANITIZE</b> - Return sanitized value. VALIDATE_INT always returns sanitized value.
 *
 * Example:
 * <code>
 * $edition_id = props_getrequest('edition_id', VALIDATE_INT);
 * $username = props_getrequest('username', VALIDATE_USERNAME, '!EMPTY,MIN2,MAX14,SANITIZE');
 * $headline = props_getrequest('headline', VALIDATE_TEXT, '!EMPTY');
 * $body_content = props_getrequest('body_content', VALIDATE_HTML, '!EMPTY');
 * $media = props_getrequest('media', VALIDATE_ARRAY);
 * </code>
 *
 * @param   string  $var       field name
 * @param   string  $validate  pre-defined 'VALIDATE_*' or a self brewed regexp
 * @param   string  $options   comma seperated list of options
 * @return  mixed   checked/sanitized value
 *
 * @todo add length and validate check for arrays???
 */
function props_getrequest($var, $validate = '', $options = '')
{
    // Process options
    $empty = $min = $max = $sanitize = FALSE;
    if ($options) {
        $options = explode(',', $options);
        foreach ($options as $id => $option) {
            switch(substr($option, 0 ,3)) {
                case '!EM':
                    $empty = TRUE;
                    break;

                case 'MIN':
                    $min = intval(trim(substr($option, 3)));
                    break;

                case 'MAX':
                    $max = intval(trim(substr($option, 3)));
                    break;

                case 'SAN':
                    $sanitize = TRUE;
                    break;

                default:
                    trigger_error('props_getrequest() '.props_gettext("invalid option").': '.$option, E_USER_WARNING);
                    break;
            }
        }
    }

    $val = props_getkey('request.'.$var);

    // Do some checks
    if ($empty && (strlen($val) == 0) && (isset($_POST['op']) || isset($_GET['op']))) {
        // Empty field not allowed when posting form data
        $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = props_gettext("This field may not be empty.");
    }
    if ($min && (strlen($val) != 0) && (strlen($val) < $min)) {
        // Invalid min length
        $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = sprintf(props_gettext("Must be at least %s characters long."), $min);
    }
    if ($max && (strlen($val) > $max)) {
        // Invalid max length
        $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = sprintf(props_gettext("Only a length of %s character(s) is allowed."), $max);
    }

    if (empty($val)) {
        if ($validate == VALIDATE_ARRAY) {
            // Always return an array
            return array();
        } elseif ($validate == VALIDATE_BOOL) {
            // Always return something
            return 0;
        }
        // Return empty values
        return '';
        //$val = '';
    }

    // Check module and function
    if ($var == 'module' || $var == 'function') {
        return preg_replace('|[^_a-zA-Z0-9]|', '', $val);
    }

    // Array check
    if (is_array($val) && $validate != VALIDATE_ARRAY) {
        if (props_getkey('config.debug_mode') == TRUE) {
            trigger_error(sprintf("You are trying to Validate array '%s'. This is not builtin yet. Please use 'VALIDATE_ARRAY'.", $var), E_USER_NOTICE);
        }
        return $val;
    }

    // Return value if we don't need to check it
    if ($validate == '' || ($val == '' && $validate != VALIDATE_BOOL)) {
        return $val;
    }

    // Check values
    switch ($validate) {

        case VALIDATE_INT:
            // Extra check
            $int = intval($val);
            settype($int, 'string');
            if ($int != $val) {
                $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = sprintf(props_gettext("Invalid integer for field '%s'."), $var);
            }

            // Allways return sanitized value
            return intval($val);
            break;

        case VALIDATE_BOOL:
            if (!empty($val)) {
                return '1';
            } else {
                return '0';
            }
            break;

        case VALIDATE_ARRAY:
            if (!is_array($val)) {
                $val = array();
            }
            break;

        case VALIDATE_DATE:
            // Checks date 'YYYY-MM-DD HH:ii
            $val = preg_replace('|[^0-9\-: ]|', '', $val);
            $datetime = explode(' ', $val, 2);

            $error = FALSE;

            if (isset($datetime['0'])) {
                // Check date
                $date = explode('-', $datetime[0], 3);

                if (!$empty && $datetime[0] == '0000-00-00') {
                    // This is not an error.
                    $error = FALSE;
                } elseif (!isset($date['0']) || !isset($date['1']) || !isset($date['2']) || !checkdate($date['1'], $date['2'], $date['0'])) {
                    // Error found
                    $error = TRUE;
                }

                // Check time if there is one
                if (isset($datetime['1'])) {
                    $time = explode(':', $datetime[1], 2);
                    if (!isset($time['0']) || !isset($time['1']) || $time['0'] > 23 || $time['0'] < 0 || $time['1'] > 59 || $time['1'] < 0) {
                        $error = TRUE;
                    }
                }
            } else {
                // No date found
                $error = TRUE;
            }

            if ($error == TRUE) {
                // There is a problem
                $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = props_gettext("Invalid date.");
            }

            return $val;

            break;

        case VALIDATE_EMAIL:
            // partially "Borrowed" from PEAR::HTML_QuickForm and refactored
            $regex = '&^(?:                                               # recipient:
             ("\s*(?:[^"\f\n\r\t\v\b\s]+\s*)+")|                          #1 quoted name
             ([-\w!\#\$%\&\'*+~/^`|{}]+(?:\.[-\w!\#\$%\&\'*+~/^`|{}]+)*)) #2 OR dot-atom
             @(((\[)?                     #3 domain, 4 as IPv4, 5 optionally bracketed
             (?:(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.){3}
                   (?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))))(?(5)\])|
             ((?:[a-z0-9](?:[-a-z0-9]*[a-z0-9])?\.)*[a-z](?:[-a-z0-9]*[a-z0-9])?))  #6 domain as hostname
             $&xi';
            if (!preg_match($regex, $val)) {
                $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = props_gettext("Invalid Email address.");
            }
            break;

        case VALIDATE_TEXT:
            // Strip html
            $cleaned = props_strip_tags($val);

            if ($cleaned != $val) {
                $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = sprintf(props_gettext("Stripped out html tags."), $validate);
            }
            return $cleaned;
            break;

        case VALIDATE_HTML:
            return $val;
            break;

        default:
            if (!preg_match('|^['.$validate.']*$|s', $val)) {
                $GLOBALS['PROPS_ERRORSTACK'][$var]['message'] = sprintf(props_gettext("Invalid format."), $validate);
                $GLOBALS['PROPS_ERRORSTACK'][$var]['title'] = props_gettext("Valid format") . ': ' . $validate;
            }

            if ($sanitize) {
                // Sanitize username/password/variables
                return preg_replace('|[^'.$validate.']|', '', $val);
            }

            break;
    }

    return $val;
}

/**
 * Strip html tags from a given string
 *
 * @param   string  $string      string to be processed
 * @param   string  $valid_tags  valid tags. If empty all html tags will be stripped.
 *                               e.g.: 'p[align],b,i'
 * @return  mixed   stripped string
 *
 * @todo  add element attribute processing
 */
function props_strip_tags($string, $valid_tags = '')
{
    $invisible_elements = array('head', 'style', 'script', 'object', 'embed', 'applet',
                                'noframes', 'noscript', 'noembed');
    $block_elements = '(address)|(blockquote)|(center)|(del)|(div)|(h[1-9])|(ins)|(isindex)|(p)|(pre)|(dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul)|(table)|(th)|(td)|(caption)|(form)|(button)|(fieldset)|(legend)|(input)|(label)|(select)|(optgroup)|(option)|(textarea)|(frameset)|(frame)|(iframe)';

    $valid_el = '';
    $valid_el_array = array();

    // Generate tag list
    $tag_array = explode(',', $valid_tags);
    foreach ($tag_array as $key => $tag) {
        $tag = explode('[', $tag,2);
        $element = $tag[0];
        $attribute = (isset($tag[1])) ? explode('|',trim($tag[1], '[]')) : array();
        $valid_el_array[$element] = $attribute;
        $valid_el .= '<'.$element.'>';
    }

    // Strip invisible elements
    foreach ($invisible_elements as $key => $element) {
        if (!array_key_exists($element, $valid_el_array)) {
            $string = preg_replace('@<'.$element.'[^>]*?>.*?</'.$element.'>@si', '', $string);
        }
    }

    //foreach ($block_elements as $key => $element) {
    $string = preg_replace('@(\s?)(</?('.$block_elements.'))@si', "\n\\2", $string);

    // Strip invalid tags
    $string = strip_tags($string, $valid_el);

    // Cleanup tag attributes

    return $string;
}

/**
 * Initialize the frontpage engine
 *
 * Initially populate registry with general information about the
 * current request (i.e. set the 'core' values)
 */
function props_init_frontpage()
{
    // loadLibs
    props_loadLib('editions,sections');

    // Get command
    $cmd = props_getrequest('cmd', '-_a-zA-Z0-9', 'SANITIZE');
    if (empty($cmd)) $cmd = 'displaysection';
    props_setkey('request.cmd', $cmd);

    // Get template
    props_setkey('request.template', props_getrequest('template', '\.\-_a-zA-Z0-9', 'SANITIZE'));

    // Default format is HTML
    $format = props_getrequest('format');
    if (!empty($format)) {
        // Make sure this output format is defined in config.php
        if (array_key_exists($format, props_getkey('config.mime_types'))) {
            props_setkey('request.format', $format);
        } else {
            trigger_error("Invalid format '$format'", E_USER_ERROR);
        }
    } else {
        props_setkey('request.format', 'html');
    }

    // MIME type is set based upon requested format
    props_setkey('request.mime_type', props_getkey('config.mime_types.'.props_getkey('request.format')));

    // If edition_id was not passed in URL, default to
    // most recent (current) edition
    $edition_id = props_getrequest('edition_id', VALIDATE_INT);
    if (!empty($edition_id)) {
        props_setkey('request.edition_id', $edition_id);
    } else {
        props_setkey('request.edition_id', edition_current_id());
    }

    // If section_id is set, use that, otherwise use front page section_id
    $section_id = props_getrequest('section_id', VALIDATE_INT);
    if (!empty($section_id)) {
        // Display error if section_id is invalid
        if (!section_is_valid($section_id)) {
            props_setkey('request.cmd', 'error-404');
            props_setkey('request.section_id', FRONTPAGE_SECTION_ID);
        } else {
            props_setkey('request.section_id', $section_id);
        }
    } else {
        props_setkey('request.section_id', FRONTPAGE_SECTION_ID);
    }

    // Edition preview
    $preview = props_getrequest('preview');
    if ($preview == TRUE && ((PROPS_USERTYPE_FOUNDER|PROPS_USERTYPE_ADMIN) & $_SESSION['PROPS_USER']['user_type'])) {
        props_setkey('request.preview', TRUE);
    } else {
        props_setkey('request.preview', FALSE);
    }

    // Set request_uri
    props_setkey('request.request_uri', $_SERVER['REQUEST_URI']);
}

/**
 * Redirect to a referer page.
 *
 * Only to be used for the admin panel.
 * Don't use the exit function after this.
 *
 * If the error_stack is set, PROPS will not redirect so the errors will be
 * displayed. Make sure you don't use the exit function after a redirect so if
 * an error is found the form and errors will be displayed.
 *
 * Example:
 * <code>
 * props_redirect('goto', 'set'); // Redirect and don't touch the current referer
 * props_redirect(TRUE);          // Redirect
 * props_redirect(TRUE, 'set');   // Set referer and redirect
 * props_redirect(FALSE, 'set');  // Set referer
 * props_redirect(FALSE, array('id' => $id, $var => $val));   // Set referer
 * </code>
 *
 * @param  mixed  $redirect  'goto'|TRUE|FALSE
 * @param  mixed  $option    'set'|array()
 */
function props_redirect($redirect, $option = FALSE)
{
    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
        $scheme .= 's';
    }

    $url = sprintf("$scheme://%s:%s%s/",
               $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'],
               dirname($_SERVER['PHP_SELF']));

    // Set referer
    if ($option) {
        $uri_array = (is_array($option)) ? $option : array();

        // Add current module
        if (defined('PROPS_ACP') && !isset($uri_array['module'])) {
            $uri_array['module'] = $GLOBALS['PROPS_MODULE'];
        }

        // Add current function
        if (defined('PROPS_ACP') && !isset($uri_array['function'])) {
            $uri_array['function'] = $GLOBALS['PROPS_FUNCTION'];
        }

        if ($redirect === 'goto') {
            // Just redirect
            header('Location: ' . $url . '?' . http_build_query($uri_array));
            exit;
        }

        // Set referer to array
        $_SESSION['REFERER'] = $url . '?' . http_build_query($uri_array);
    }

    // Redirect
    if ($redirect === TRUE) {
        // Stop on errors. If we redirect when there are errors, we cannot see
        // those on the next page.
        if (!empty($GLOBALS['PROPS_ERRORSTACK']) && (isset($_POST['cmd']) && $_POST['cmd'] != props_gettext("Cancel"))) {
            trigger_error('Errors found. Cannot redirect.', E_USER_WARNING);
            return FALSE;
        }

        // Get referer
        $referer = (isset($_SESSION['REFERER'])) ? $_SESSION['REFERER'] : $url;
        unset($_SESSION['REFERER']);

        // Redirect to referer
        header('Location: ' . $referer);
        exit;
    }

    return FALSE;
}

/**
 * Generates a unique pageID. This can be used to check if a submitted form
 * really came from that page.
 *
 * Example:
 * <code>
 * $output =
 *      '<form action="./" method="post">'.LF
 *     .'<input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
 *     .'</form>'.LF;
 *
 * // Check pageID
 * if (!isset($_POST['pageID']) || ($_POST['pageID'] != $_SESSION['pageID'])) {
 *     props_error("Invalid page referer. Please submit this form again.");
 *     break;
 * }
 * </code>
 *
 * @param   int  $length  length of output
 * @return  string  pageID
 */
function props_pageID($length = '10')
{
    if (!defined('PROPS_PAGEID')) {
        static $string;
        for ($i = 1; $i <= $length; $i++) {
            $string .= chr(rand(33,126));
        }

        define('PROPS_PAGEID', strtoupper(md5(time().$string)));
        $_SESSION['pageID'] = PROPS_PAGEID;
    }

    return PROPS_PAGEID;
}

/**
 * Props error message
 *
 * Used to trigger an error condition.
 *
 * These messages won't be logged to the system error log.
 * Messages in double quotes will be translated and added to the locale files.
 *
 * Syntax: props_error($errstr, $errno);
 *
 * @param  string   $errstr  error message
 * @param  integer  $errno   level of the error raised
 */
function props_error($errstr, $errno = PROPS_E_NOTICE, $errfile = '', $errline = '')
{
    if (props_getkey('config.debug_mode') === TRUE) {
        $err = debug_backtrace();
        $errfile = $err[0]['file'];
        $errline = $err[0]['line'];
    }

    // Don't show base dir. Must be shown in general php error log. Could be
    // handy when you have several props installations running.
    $errfile = str_replace('\\', '/', $errfile);
    $errfile = str_replace(PROPS_ROOT, '', $errfile);

    $GLOBALS['PROPS_ERRORSTACK'][] = array('errtime'=>props_timerSplit(), 'errno'=>$errno, 'errstr'=>props_gettext($errstr), 'errfile'=>$errfile, 'errline'=>$errline);
}

/*
__LINE__ The current line number of the file.
__FILE__ The full path and filename of the file. If used inside an include, the name of the included file is returned. Since PHP 4.0.2, __FILE__ always contains an absolute path whereas in older versions it contained relative path under some circumstances.
__FUNCTION__ The function name. (Added in PHP 4.3.0) As of PHP 5 this constant returns the function name as it was declared (case-sensitive). In PHP 4 its value is always lowercased.
__CLASS__ The class name. (Added in PHP 4.3.0) As of PHP 5 this constant returns the class name as it was declared (case-sensitive). In PHP 4 its value is always lowercased.
*/

/**
 * Props debug message
 *
 * Used to trigger an error condition.
 *
 * Syntax: props_error($errstr, $errno);
 *
 * @param  string   $errstr  error message
 * @param  integer  $errno   level of the error raised
 */
function props_debug($errstr, $errno = E_DEBUG, $errfile = '', $errline = '')
{
    if (props_getkey('config.debug_mode')) {

        if ($errfile == '' && $errline == '' && substr($errstr, 0, 3) != 'SQL') {
            $err = debug_backtrace();
            $errfile = $err[0]['file'];
            $errline = $err[0]['line'];
        }

        // Don't show base dir.
        $errfile = str_replace('\\', '/', $errfile);
        $errfile = str_replace(PROPS_ROOT, '', $errfile);

        $GLOBALS['PROPS_DEBUGSTACK'][] = array('errtime'=>props_timerSplit(), 'errno'=>$errno, 'errstr'=>$errstr, 'errfile'=>$errfile, 'errline'=>$errline);
    }
}

/**
 * Props error handler
 *
 * Captures notices, warnings and errors and outputs the error.html template
 * located in de templates dir when a critical error is detected.
 *
 * Do not call this finction directly, but use trigger_error() instead.
 *
 * Syntax: trigger_error($message, $level);
 *
 * $level can be:
 * - <b>E_USER_NOTICE</b>  - displays user_message
 * - <b>E_USER_WARNING</b> - also writes system_message to the log
 * - <b>E_USER_ERROR</b>   - also outputs the error template and exits.
 *
 * System errors are logged to the log file and appended to the error message
 * when in debug mode. It is not recommended to translate the message. This way
 * you can always read the English message in the log in stead of some foreign
 * language.
 *
 * If you want to add an critical error message description for the user, set
 * $GLOBALS['PROPS_ERRORDESC'] before calling trigger_error(). This wont be
 * logged and can be translated.
 *
 * Example:
 * <code>
 * // Critical error message for the user (optional)
 * $GLOBALS['PROPS_ERRORDESC'] = props_gettext("Database error. Please try again later.");
 * // trigger error added to the log file and screen when in debug mode.
 * trigger_error('SQL error (' . mysql_errno() . '): '.mysql_error().' - : '.$query, E_USER_ERROR);
 * </code>
 *
 * @param  integer  $errno       level of the error raised
 * @param  string   $errstr      error message
 * @param  string   $errfile     filename that the error was raised in
 * @param  integer  $errline     line number the error was raised at
 * @param  array    $errcontext  any variable that existed in the scope
 *                               the error was triggered in.
 */
function props_error_handler($errno, $errstr, $errfile = '', $errline = '', $errcontext = '')
{
    // If the @ error suppression operator was used, error_reporting is
    // temporarily set to 0.
    if (error_reporting() == 0) {
        return;
    }

    // Add error to error log file
    if (((ERROR_LOG) & $errno) && !empty($message[1])) {
        // Format error
        $format = '[' . date("Y-m-d H:i:s") . ']'
                . ' ' . $GLOBALS['PROPS_ERROR_CODES'][$errno] . ': '
                . ' ' . $errstr
                . ' in ' . $errfile
                . ' on line ' . $errline . LF;

        if (is_writable(props_getkey('config.error_log'))) {
            // Append to custom error_log
            error_log($format, 3, props_getkey('config.error_log'));
        } else {
            // Append to default error_log
            error_log($format);
        }
    }

        $format = '[' . date("Y-m-d H:i:s") . ']'
                . ' ' . $GLOBALS['PROPS_ERROR_CODES'][$errno] . ': '
                . ' ' . $errstr
                . ' in ' . $errfile
                . ' on line ' . $errline . LF;

//echo "[".__LINE__."] $format".BR;

    // Don't show base dir. Must be shown in general php error log. Could be
    // handy when you have several props installations running.
    $errfile = str_replace('\\', '/', $errfile);
    $errfile = str_replace(PROPS_ROOT, '', $errfile);

    // Construct PROPS_ERRORSTACK error string
    $logstr = (isset($GLOBALS['PROPS_ERRORDESC'])) ? $GLOBALS['PROPS_ERRORDESC'] : '';
    // Add system error info when there is no description or in debug mode
    if (props_getkey('config.debug_mode') === TRUE || empty($logstr)) {
        if (!empty($logstr)) {
            $logstr .= ' - '.$errstr;
        } else {
            $logstr .= $errstr;
        }
    }

    // Add to PROPS_ERRORSTACK
    $GLOBALS['PROPS_ERRORSTACK'][] = array('errtime'=>props_timerSplit(), 'errno'=>$errno, 'errstr'=>$logstr, 'errfile'=>$errfile, 'errline'=>$errline);

    // You can't handle the... error.
    if ((ERROR_DIE) & $errno) {
        $error['title'] = props_gettext("Internal Server Error");
        $error['text1'] = props_gettext("While responding to your request an error encountered in the application.") . ' ' . props_gettext("The administrator has been notified.");
        $error['text2'] = props_gettext("Sorry for the inconvenience, we correct the error as soon as possible.");
        $error['url'] = '<a href="'.(isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']): PROPS_URL).'">Go back</a>';
        $error['description'] = (isset($GLOBALS['PROPS_ERRORDESC'])) ? '<b>'.$GLOBALS['PROPS_ERRORDESC'].'</b>' : '';

        // Output debug info when in debug mode or user is founder or admin.
        if (props_getkey('config.debug_mode') === TRUE ||
           ((PROPS_USERTYPE_FOUNDER|PROPS_PRIVTYPE_ADMIN) & $_SESSION['PROPS_USER']['user_type'])) {
            $error['description'] .='<p><i><b>' . $GLOBALS['PROPS_ERROR_CODES'][$errno] . '</b></i> in <strong>'.$errfile.'</strong> on line <strong>'.$errline.'</strong><br />'.LF
                                  . '<strong>Description:</strong> '.$errstr.'</p>'.LF;
        }

        // Get the error template
        if (is_file(props_getkey('config.dir.templates') . 'error500.html')) {
            include(props_getkey('config.dir.templates') . 'error500.html');
            exit;
        }

        // Just an extra message to make sure that there is some output.
        die("Internal Server Error\n\n$errstr");
    }

    // Cleanup
    unset($GLOBALS['PROPS_ERRORDESC']);
}

/**
 * Start timer
 *
 * @param  string  $name  timer name
 */
function props_timerStart($name = 'PROPS')
{
    props_setkey("timer.$name.start", explode(' ', microtime()));
}
/**
 * Stop timer
 *
 * @param  string  $name  timer name
 */
function props_timerStop($name = 'PROPS')
{
    props_setkey("timer.$name.stop", explode(' ', microtime()));
}
/**
 * Get current elapsed time.
 *
 * @param   string  $name  timer name
 * @return  string  elapsed time
 */
function props_timerSplit($name = 'PROPS')
{
    if (!props_getkey("timer.$name.start")) {
        return 0;
    }

    if (!props_getkey("timer.$name.stop")) {
        $stop_time = explode(' ', microtime());
    } else {
        $stop_time = props_getkey("timer.$name.stop");
    }

    // Do the big numbers first so the small ones aren't lost
    $current = $stop_time[1] - props_getkey("timer.$name.start.1");
    $current += $stop_time[0] - props_getkey("timer.$name.start.0");

    // Return a numeric var (must have a period and not a comma).
    // Needed for filtering ERROR_STACK.
    if (version_compare(PHP_VERSION, '4.3.10', '>=')) {
        // PHP4.3.10+ needed for the 'F' option.
        return sprintf('%.5F',$current);
    } else {
        return str_replace(',', '.', sprintf('%.5f',$current));
    }
}

/**
 * Generate password
 *
 * PLEASE NOTE: The numbers 0 and 1 (zero and one) and the letters
 * l and o, ('el' and 'oh' in lowercase) and the letters I and O
 * ('eye' and 'oh' in uppercase) have been removed from the random
 * generator, so as not to cause confusion when writing them down.
 *
 * @param   int     $length  length of generated password
 * @return  string  generated password
 */
function props_random_password($length = '8')
{
    $possible = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $str = '';

    while (strlen($str) < $length) {
        $str.=substr($possible, (rand() % strlen($possible)),1);
    }

    return($str);
}

/**
 * Recursive creation of directories. Creates the complete directory structure.
 */
function props_mkdirs($strPath, $mode = 0755)
{
    if (is_dir($strPath)) return TRUE;
    $pStrPath = dirname($strPath);
        if (!props_mkdirs($pStrPath, $mode)) return FALSE;
    return mkdir($strPath, $mode);
}

/**
 * Recursive trim value/array
 *
 * @param   mixed  $value  Array or string to process
 * @return  mixed  Processed array or string
 */
function props_trim($value)
{
   $value = is_array($value) ? array_map('props_trim', $value): trim($value);

   return $value;
}

/**
 * Recursive sorts an array
 *
 * @param  array  &$array  Array to process
 */
function props_arraysort(&$array)
{
    uksort($array, 'strnatcasecmp');
    foreach(array_keys($array) as $k) {
        if(gettype($array[$k])=="array") {
            props_arraysort($array[$k]);
        }
    }
}

/**
 * Gets the "true" IP address of the current user
 *
 * @return  string  the IP of the user
 */
function props_get_ipaddress()
{
    $direct_ip = '';
    $proxy_ip = '';

    // Gets the default ip sent by the user
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $direct_ip = $_SERVER['REMOTE_ADDR'];
    }

    // Gets the proxy ip sent by the user
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (empty($HTTP_X_FORWARDED) && isset($_SERVER['HTTP_X_FORWARDED'])) {
        $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (empty($HTTP_FORWARDED_FOR) && isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (empty($HTTP_FORWARDED) && isset($_SERVER['HTTP_FORWARDED'])) {
        $proxy_ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (empty($HTTP_VIA) && isset($_SERVER['HTTP_VIA'])) {
        $proxy_ip = $_SERVER['HTTP_VIA'];
    } elseif (empty($HTTP_X_COMING_FROM) && isset($_SERVER['HTTP_X_COMING_FROM'])) {
        $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
    } elseif (empty($HTTP_COMING_FROM) && isset($_SERVER['HTTP_COMING_FROM'])) {
        $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
    }

    // Returns the true IP if it has been found, else false
    if (empty($proxy_ip)) {
        // True IP without proxy
        return $direct_ip;
    } else {
        $is_ip = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxy_ip, $regs);
        if ($is_ip && (count($regs) > 0)) {
            // True IP behind a proxy
            return $regs[0];
        } else {
            // Can't define IP: there is a proxy but we don't have
            // information about the true IP
            return FALSE;
        }
    }
}

/**
 * Word censoring
 */
function props_censor_text($text)
{
    static $censor_list;

    if (!isset($censor_list) || !is_array($censor_list)) {
        // Obtain censored word list
        $q = "SELECT pattern, replacement FROM props_censored_words";
        $result = sql_query($q);

        $censor_list = array();
        while ($row = sql_fetch_assoc($result)) {
            $censor_list['pattern'][] = '#(?<!\w)(' . str_replace('\*', '\w*?', preg_quote($row['pattern'], '#')) . ')(?!\w)#i';
            $censor_list['replacement'][] = $row['replacement'];
        }
        sql_free_result($result);
    }

    if (!empty($censor_list)) {
        return preg_replace($censor_list['pattern'], $censor_list['replacement'], $text);
    }

    return $text;
}

/**
 * Shutdown props and update some vars
 *
 * This function can't be registered as a shutdown function because the session
 * variables won't be updated then
 */
function props_shutdown()
{
    // Set last page
    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
        $scheme .= 's';
    }
    $query = ($_SERVER['QUERY_STRING'] != '') ? '?'.$_SERVER['QUERY_STRING'] : '';

    $_SESSION['LAST_URL'] = sprintf("$scheme://%s:%s%s/%s",
                           $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'],
                           dirname($_SERVER['PHP_SELF']), $query);
}

function props_debug_info()
{
    if (props_getkey('config.debug_mode') && $_SESSION['PROPS_USER']['user_type'] == PROPS_USERTYPE_FOUNDER) {
        $debug_info = $error_info = '';
        $debug_counter = $error_counter = $warning_counter = $notice_counter = 0;

        $debug_info .=
             '<div class="msgDebug">SERVER: '.$_SERVER['SERVER_SOFTWARE'].'</div>'
            .'<div class="msgDebug">PHP: '.phpversion().'</div>'
            .'<div class="msgDebug">DATABASE: ' . props_getkey('config.db.type') . ' ' . sql_server_version() . '</div>'
            .'<div class="msgDebug">OS: '.php_uname().'</div>'
            .'<div class="msgDebug">PROPS: ' . PROPS_VERSION . ' (' . PROPS_ROOT . ')</div><hr />';

        foreach ($GLOBALS['PROPS_DEBUGSTACK'] as $key => $error) {
                    $debug_info .= '<div class="msgDebug">(' . $error['errtime'] . ') '
                                . htmlspecialchars($error['errstr'])
                                . ' in "'.$error['errfile']
                                . '" ('.$error['errline'].')</div>';
                    $debug_counter++;
        }

        foreach ($GLOBALS['PROPS_ERRORSTACK'] as $key => $error) {
            if (is_numeric($key)) {
                if ((E_SQL | ERROR_DIE) & $error['errno']) {
                    $error_info .= '<div class="msgError">(' . $error['errtime'] . ') '
                                . $GLOBALS['PROPS_ERROR_CODES'][$error['errno']]
                                . ': '.htmlspecialchars($error['errstr'])
                                . ' in "'.$error['errfile']
                                . '" ('.$error['errline'].')</div>';
                    $error_counter++;
                } elseif ((ERROR_WARNING) & $error['errno']) {
                    $error_info .= '<div class="msgWarning">(' . $error['errtime'] . ') '
                                . $GLOBALS['PROPS_ERROR_CODES'][$error['errno']]
                                . ': '.htmlspecialchars($error['errstr'])
                                . ' in "'.$error['errfile']
                                . '" ('.$error['errline'].')</div>';
                    $warning_counter++;
                } else {
                    $error_info .= '<div class="msgNotice">(' . $error['errtime'] . ') '
                                . $GLOBALS['PROPS_ERROR_CODES'][$error['errno']]
                                . ': '.htmlspecialchars($error['errstr'])
                                . ' in "'.$error['errfile']
                                . '" ('.$error['errline'].')</div>';
                    $notice_counter++;
                }
            }
        }

        $total = $notice_counter + $warning_counter + $error_counter;

        $output =
              '<div id="debugBar">'.props_timerSplit().' sec.'
            . (($total > 0) ? '<span style="cursor:pointer;" onclick="debugFocus(\'debugError\')">': '')
            . ' | <span class="msgError">Errors: '.$error_counter.'</span>'
            . ' | <span class="msgWarning">Warnings: '.$warning_counter.'</span>'
            . ' | <span class="msgNotice">Notices: '.$notice_counter.'</span>'
            . (($total > 0) ? '</span>': '')
            . ' | <span style="cursor:pointer;" class="msgDebug" onclick="debugFocus(\'debugInfo\')">Debug: '.$debug_counter.' | SQL: '.$GLOBALS['PROPS_DB_QUERIES'].' </span>'
            . ' | <span style="cursor:pointer;" class="msgDebug" onclick="debugFocus(\'debugRegistry\')">Registry</span>'
            . '</div>'.LF
            . '<div id="debugError" class="msgError" '.((($warning_counter + $error_counter) > 0) ? 'style="visibility:visible;"': '').'><br />'.$error_info.'</div>'.LF
            . '<div id="debugInfo" class="msgDebug"><br />'.$debug_info.'</div>'.LF
            . '<div id="debugRegistry" class="msgDebug">'
            . '<br />$_COOKIE<pre>'.htmlspecialchars(print_r($_COOKIE, TRUE)).'</pre>'.LF
            . '<br />$_GET<pre>'.htmlspecialchars(print_r($_GET, TRUE)).'</pre>'.LF
            . '<br />$_POST<pre>'.htmlspecialchars(print_r($_POST, TRUE)).'</pre>'.LF
            . '<br />$_SESSION<pre>'.htmlspecialchars(print_r($_SESSION, TRUE)).'</pre>'.LF
            . '<br />REGISTRY<pre>'.htmlspecialchars(print_r($GLOBALS['PROPS_REGISTRY']['request'], TRUE)).'</pre></div>'.LF;

        return $output;
    }
}

/**
 * Formats value to byte view
 *
 * Original code from phpMyAdmin.
 *
 * @param  double   the value to format
 * @param  integer  the sensitiveness
 * @param  integer  the number of decimals to retain
 *
 * @return  array    the formatted value and its unit
 *
 * @access  public
 */
function props_formatByteDown($value, $limes = 6, $comma = 0)
{
    $number_thousands_separator = '.';
    $number_decimal_separator = ',';
    $byteUnits    = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
    $dh           = pow(10, $comma);
    $li           = pow(10, $limes);
    $return_value = $value;
    $unit         = $byteUnits[0];

    for ($d = 6, $ex = 15; $d >= 1; $d--, $ex-=3) {
        if (isset($byteUnits[$d]) && $value >= $li * pow(10, $ex)) {
            // use 1024.0 to avoid integer overflow on 64-bit machines
            $value = round($value / (pow(1024.0, $d) / $dh)) /$dh;
            $unit = $byteUnits[$d];
            break 1;
        } // end if
    } // end for

    if ($unit != $byteUnits[0]) {
        $return_value = number_format($value, $comma, $number_decimal_separator, $number_decimal_separator);
    } else {
        $return_value = number_format($value, 0, $number_decimal_separator, $number_decimal_separator);
    }

    return array($return_value, $unit);
}

/******************************************************************************
 * Compatibility functions
 *****************************************************************************/

if (!function_exists("htmlspecialchars_decode")) {
    function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT)
    {
        return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    }
}

if (!defined('FILE_USE_INCLUDE_PATH')) {
    define('FILE_USE_INCLUDE_PATH', 1);
}

if (!defined('LOCK_EX')) {
    define('LOCK_EX', 2);
}

if (!defined('FILE_APPEND')) {
    define('FILE_APPEND', 8);
}

/**
 * Add function file_put_contents()
 * @version   1.27
 * @internal  resource_context is not supported
 * @access    private
 */
if (!function_exists('file_put_contents')) {
    /**
     * @access  private
     */
    function file_put_contents($filename, $content, $flags = null, $resource_context = null)
    {
        // If $content is an array, convert it to a string
        if (is_array($content)) {
            $content = implode('', $content);
        }

        // If we don't have a string, throw an error
        if (!is_scalar($content)) {
            user_error('file_put_contents() The 2nd parameter should be either a string or an array',
                E_USER_WARNING);
            return false;
        }

        // Get the length of data to write
        $length = strlen($content);

        // Check what mode we are using
        $mode = ($flags & FILE_APPEND) ?
                    'a' :
                    'wb';

        // Check if we're using the include path
        $use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
                    true :
                    false;

        // Open the file for writing
        if (($fh = @fopen($filename, $mode, $use_inc_path)) === false) {
            user_error('file_put_contents() failed to open stream: Permission denied',
                E_USER_WARNING);
            return false;
        }

        // Attempt to get an exclusive lock
        $use_lock = ($flags & LOCK_EX) ? true : false ;
        if ($use_lock === true) {
            if (!flock($fh, LOCK_EX)) {
                return false;
            }
        }

        // Write to the file
        $bytes = 0;
        if (($bytes = @fwrite($fh, $content)) === false) {
            $errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
                            $length,
                            $filename);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Close the handle
        @fclose($fh);

        // Check all the data was written
        if ($bytes != $length) {
            $errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
                            $bytes,
                            $length);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Return length
        return $bytes;
    }
}

/**
 * Add function http_build_query()
 * @version  1.22
 * @access   private
 */
if (!function_exists('http_build_query')) {
    /**
     * @access  private
     */
    function http_build_query($formdata, $numeric_prefix = null)
    {
        // If $formdata is an object, convert it to an array
        if (is_object($formdata)) {
            $formdata = get_object_vars($formdata);
        }

        // Check we have an array to work with
        if (!is_array($formdata)) {
            user_error('http_build_query() Parameter 1 expected to be Array or Object. Incorrect value given.',
                E_USER_WARNING);
            return false;
        }

        // If the array is empty, return null
        if (empty($formdata)) {
            return;
        }

        // Argument seperator
        $separator = ini_get('arg_separator.output');
        if (strlen($separator) == 0) {
            $separator = '&';
        }

        // Start building the query
        $tmp = array ();
        foreach ($formdata as $key => $val) {
            if (is_null($val)) {
                continue;
            }

            if (is_integer($key) && $numeric_prefix != null) {
                $key = $numeric_prefix . $key;
            }

            if (is_scalar($val)) {
                array_push($tmp, urlencode($key) . '=' . urlencode($val));
                continue;
            }

            // If the value is an array, recursively parse it
            if (is_array($val) || is_object($val)) {
                array_push($tmp, _http_build_query($val, urlencode($key)));
                continue;
            }

            // The value is a resource
            return null;
        }

        return implode($separator, $tmp);
    }

    /**
     * http_build_query helper function
     * @access  private
     */
    function _http_build_query($array, $name)
    {
        $tmp = array ();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                array_push($tmp, _http_build_query($value, sprintf('%s[%s]', $name, $key)));
            } elseif (is_scalar($value)) {
                array_push($tmp, sprintf('%s[%s]=%s', $name, urlencode($key), urlencode($value)));
            } elseif (is_object($value)) {
                array_push($tmp, _http_build_query(get_object_vars($value), sprintf('%s[%s]', $name, $key)));
            }
        }

        // Argument seperator
        $separator = ini_get('arg_separator.output');
        if (strlen($separator) == 0) {
            $separator = '&';
        }

        return implode($separator, $tmp);
    }
}

/**
 * Startup the system
 */

// Auto detection of the config file. This will check for config.php in the
// base_dir (where readme.txt is) or in the public_html dir.
if (file_exists(PROPS_ROOT.'config.php')) {
    require_once(PROPS_ROOT.'config.php');
} elseif (file_exists(PROPS_ROOT.'public_html/config.php')) {
    require_once(PROPS_ROOT.'public_html/config.php');
} elseif (file_exists(PROPS_ROOT.'www/config.php')) {
    require_once(PROPS_ROOT.'www/config.php');
} else {
    die('Your configuration is not correctly configured. Please check the config file.');
}

// Start timer
props_timerStart();

// Use props sessions
props_loadLib('database,sessions,users');

// Init PHP session - must be called for every page
session_start();

// Use the props error handler
error_reporting(E_ALL ^ E_NOTICE);
if (version_compare(PHP_VERSION, '5.0.0', '>=') == TRUE) {
    $old_error_handler = set_error_handler('props_error_handler', E_ALL);
} else {
    $old_error_handler = set_error_handler('props_error_handler');
}

// Get request values
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        props_setkey('request', props_grab_request($_POST));
        break;

    case 'GET':
    default:
        props_setkey('request', props_grab_request($_GET));
        break;
}

// Check language
$lng = '';
if (defined('PROPS_ACP')) {
    if (isset($_SESSION['PROPS_USER']['language']) && !empty($_SESSION['PROPS_USER']['language'])) {
        $lng = $_SESSION['PROPS_USER']['language'];
    } elseif (!empty($_POST['language'])) {
        $lng = ereg_replace("[^a-zA-Z_]", "", $_POST['language']);
    } elseif (!empty($_COOKIE['PROPS_LANGUAGE'])) {
        $lng = ereg_replace("[^a-zA-Z_]", "", $_COOKIE['PROPS_LANGUAGE']);
    }
}

if (!empty($lng) && is_file(PROPS_ROOT.'locale/'.$lng.'.php')) {
    // Load detect locale
    require_once(PROPS_ROOT.'locale/'.$lng.'.php');
} else {
    // Load default
    require_once(PROPS_ROOT.'locale/'.props_getkey('config.i18n').'.php');
}

// Needed for date and time functions in PHP 5.1+
if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set(props_getkey('config.default.timezone'));
}

// Initialize the user
user_init();

props_debug('Init PROPS ready');

?>
