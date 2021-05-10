<?php
/**
 * Lib - admin functions
 *
 * @package     api
 * @subpackage  admin
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
 * @version     $Id: admin.php,v 1.28 2007/12/11 15:46:28 roufneck Exp $
 */

$GLOBALS['PROPS_SIDEBAR'] = array();

/**
 * Returns the login screen
 *
 * @return  string  html code that displays login screen
 */
function admin_login_screen()
{
    $GLOBALS['PROPS_PAGETITLE'] = props_gettext("Restricted area.");

    // Load MD5
    $GLOBALS['javascript'] = '<script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'md5.js"></script>'.LF;

    $output =
         '<br />'.LF
        .'<form method="post" action="./">'.LF
        .'<input name="cmd" type="hidden" value="login" />'.LF
        .'<fieldset style="width: 500px; margin: 0 auto;">'.LF
        .'  <legend>' . props_gettext("Basic login") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label for="username">' . props_gettext("Username") . '</label></dt>'.LF
        .'    <dd><input class="large" type="text" id="username" name="username" value="' . htmlspecialchars(props_getkey('request.username')) . '" /></dd>'.LF
        .'  </dl>'.LF
        .'  <dl>'.LF
        .'    <dt><label for="password">' . props_gettext("Password") . '</label></dt>'.LF
        .'    <dd><input class="large" type="password" id="password" name="password" value="" /></dd>'.LF
        .'  </dl>'.LF
        .'  <p>'.LF
        .'    <input class="button" type="submit" value="' . props_gettext("Login") . '" />'.LF
        .'  </p>'.LF
        .'</fieldset>'.LF
        .'</form>'.LF
        .'<br />'.LF;

    if (props_getkey('config.openid.enable')) {
        $output .=
         '<form method="post" action="./">'.LF
        .'<input name="cmd" type="hidden" value="login" />'.LF
        .'<fieldset style="width: 500px; margin: 0 auto;">'.LF
        .'  <legend>' . props_gettext("OpenID login") . '</legend>'.LF
        .'  <dl>'.LF
        .'    <dt><label for="openid_url">OpenID</label></dt>'.LF
        .'    <dd><input class="openid medium" type="text" id="openid_url" name="openid_url" value="' . htmlspecialchars(props_getkey('request.openid_url')) . '" /></dd>'.LF
        .'  </dl>'.LF
        .'  <p>'.LF
        .'    <input class="button" type="submit" value="' . props_gettext("Login") . '" />'.LF
        .'  </p>'.LF
        .'</fieldset>'.LF
        .'</form>'.LF
        .'<br />'.LF;
    }

    $output .=
        // Focus to login field
         '<script type="text/javascript">'.LF
        .'    login_focus();'.LF
        .'</script>'.LF;

    return $output;
}

/**
 * Returns html restricted area notice
 *
 * Called when an admin tries to access a module/function they don't have
 * permission to.
 *
 * @return  string  html code that displays restricted area notice
 */
function admin_restricted_area($die = FALSE)
{
    if ($die === FALSE) {
        $GLOBALS['PROPS_PAGETITLE'] = props_gettext("Permission denied");
        props_error("You don't have permission to access this screen.", PROPS_E_WARNING);
        return '<p><a href="javascript:history.go(-1)">&laquo;&nbsp;' . props_gettext("Go back") . '&nbsp;&raquo;</a></p>';
    }

    $error['title'] = props_gettext("Permission denied");
    $error['text1'] = props_gettext("You don't have permission to access this screen.");
    $error['text2'] = '';
    $error['url'] = '<a href="'.(isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']): PROPS_URL).'">Go back</a>';
    $error['description'] = '';

    // Get the error template
    if (is_file(props_getkey('config.dir.templates') . 'error.html')) {
        include(props_getkey('config.dir.templates') . 'error.html');
        exit;
    }

    // Just an extra message to make sure that there is some output.
    die("Permission denied");
}

function admin_sidebar_add($module, $function, $append = '')
{
    $GLOBALS['PROPS_SIDEBAR'][] = array('module'=>$module, 'function'=>$function, 'append'=>$append);
}

/**
 * Builds the admin screen
 *
 * Possible options:
 * - <b>$GLOBALS['PROPS_WYSIWYG']</b> - contains the id for a textarea to be
 *                                      transformed to a wysiwyg editor
 * - <b>$GLOBALS['JavaScript']</b>    - contains JavaScript to be included
 *
 * @param   string  &$content  content generated by an admin function
 * @return  string  html code with admin screen
 */
function admin_display_screen(&$content)
{
    $output =
         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.LF
        .'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en">'.LF
        .'<head>'.LF
        .'  <title>'.$_SERVER['HTTP_HOST'].' admin :: '.$GLOBALS['PROPS_PAGETITLE'].'</title>'.LF
        //.'  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />'.LF
        .'  <meta http-equiv="Content-Language" content="en" />'.LF
        .'  <meta name="robots" content="noindex,nofollow" />'.LF
        .'  <meta name="keywords" content="PROPS - Open Source Publishing Platform ' . PROPS_VERSION . ', http://props.sourceforge.net/" />'.LF
        .'  <link rel="stylesheet" type="text/css" media="all" href="props.admin.css" />'.LF
        .'  <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="props.iefix.css" /><!{endif]-->'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'props.admin.js"></script>'.LF
        .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'swfobject.js"></script>'.LF;

    if (isset($GLOBALS['PROPS_WYSIWYG'])) {
        if (props_getkey('wysiwyg.tiny_mce.compress')) {
            $output .=
             '  <!-- BEGIN: TINY_MCE COMPRESSED -->'.LF
            .'  <script type="text/javascript" src="' . props_getkey('config.url.scripts') . 'tiny_mce/tiny_mce_gzip.js"></script>'.LF
            .'  <script type="text/javascript">'.LF
            .'    tinyMCE_GZ.init({'.LF
            .'      plugins: "'.props_getkey('wysiwyg.tiny_mce.plugins').'",'.LF
           // .'    plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,"+'.LF
          //  .'      "searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",'.LF
            .'      themes : "advanced",'.LF
            .'      languages : "en",'.LF
            .'      disk_cache : true,'.LF
            .'      debug : false'.LF
            .'    });'.LF
            .'  </script>'.LF;
        } else {
            $output .=
                 '  <!-- BEGIN: TINY_MCE -->'.LF
                .'  <script language="javascript" type="text/javascript" src="' . props_getkey('config.url.scripts') . 'tiny_mce/tiny_mce.js"></script>'.LF;
        }

        $output .=
         '  <script language="javascript" type="text/javascript">'.LF
        .'    tinyMCE.init({'.LF
        .'      mode : "exact",'.LF
        .'      elements : "' . $GLOBALS['PROPS_WYSIWYG'] . '",'.LF
        .'      plugins: "'.props_getkey('wysiwyg.tiny_mce.plugins').'",'.LF
        .'      theme: "advanced",'.LF
        .'      theme_advanced_toolbar_align : "left",'.LF
        .'      theme_advanced_toolbar_location: "top",'.LF
        .'      theme_advanced_buttons1: "'.props_getkey('wysiwyg.tiny_mce.buttons1').'",'.LF
        .'      theme_advanced_buttons2: "'.props_getkey('wysiwyg.tiny_mce.buttons2').'",'.LF
        .'      theme_advanced_buttons3: "'.props_getkey('wysiwyg.tiny_mce.buttons3').'",'.LF
        .'      verify_html: false,'.LF
        .'      cleanup_on_startup: false,'.LF
        .'      relative_urls: false,'.LF
        .'      invalid_elements: "",'.LF
        .'      entity_encoding : "raw"'.LF
        .'    });'.LF
        .'  </script>'.LF
        .'  <!-- END: TINY_MCE -->'.LF;
    }

    if (isset($GLOBALS['JavaScript'])) {
        $output .= $GLOBALS['JavaScript'];
    }

    $output .=
         '</head>'.LF
        .'<body>'.LF
        .'<div id="container">'.LF
        .'  <div id="header">'.LF
        .'    <img src="./images/props_logo.png" alt="PROPS publishing platform" width="210" height="25" />'.LF
        .'  </div>'.LF;

    if (!empty($_SESSION['PROPS_USER']['authenticated'])) {

        // Set menu status
        $select = ($GLOBALS['PROPS_MODULE'] == 'adminmain') ? 'tab-select' : 'tab';
        $hide = ($GLOBALS['PROPS_MODULE'] != 'adminmain') ? 'class="hide"' : '';

        // Build the navigation menu
        //$mainnav = '      <li id="t:nav:0" ' . $selected . '><a href="javascript:tabSelect(\'t:nav:0\');">Props</a></li>'.LF;
        $mainnav = '          <li id="t:nav:0" onclick="selectTab(this)" class="' . $select . '">Props</li>'.LF;

        $subnav =  '      <ul id="c:nav:0" ' . $hide . '>'.LF
                 . '        <li><a href="./?module=adminmain&amp;function=mainmenu">'.props_gettext('.adminmain.mainmenu').'</a></li>'.LF
                 . '        <li>|&nbsp;&nbsp;<a href="./?module=adminmain&amp;function=about">'.props_gettext('.adminmain.about').'</a></li>'.LF
                 . '        <li>|&nbsp;&nbsp;<a href="./?module=users&amp;function=preferences">'.props_gettext('.users.preferences').'</a></li>'.LF
                 . '      </ul>'.LF;
        $c = 0; // Sub nav counter
        if (isset($GLOBALS['PROPS_USERPRIVS'][PROPS_PRIVTYPE_ADMIN])) {
            foreach ($GLOBALS['PROPS_USERPRIVS'][PROPS_PRIVTYPE_ADMIN] as $mod => $val) {
                $i = 0;
                foreach ($val as $func => $status) {
                    if ($status > 0) {
                        if ($i == 0) {
                            $c++;
                            // Set menu status
                            $select = ($GLOBALS['PROPS_MODULE'] == $mod) ? 'tab-select' : 'tab';
                            $hide = ($GLOBALS['PROPS_MODULE'] != $mod) ? 'class="hide"' : '';
                            // Start section
                            $mainnav .= '          <li id="t:nav:'.$c.'" onclick="selectTab(this)" class="' . $select . '">'.props_gettext('.'.$mod).'</li>'.LF;
                            $subnav .= '      <ul id="c:nav:'.$c.'" ' . $hide . '>'.LF;
                            // First functions
                            $subnav .= '        <li><a href="./?module='.$mod.'&amp;function='.$func.'">'.props_gettext('.'.$mod.'.'.$func).'</a></li>'.LF;
                        } else {
                            // Other functions
                            $subnav .= '        <li>|&nbsp;&nbsp;<a href="./?module='.$mod.'&amp;function='.$func.'">'.props_gettext('.'.$mod.'.'.$func).'</a></li>'.LF;
                        }
                        $i++;
                    }
                }

                // Close section
                if ($i > 0) {
                    $subnav .= '        </ul>'.LF;
                }
            }
        }

        $output .=
         '  <div id="navigation">'.LF
        .'    <div id="bar">'.LF
        .'      <div>'.LF
        .'        <ul id="contenttypes">'.LF
        . $mainnav
        .'        </ul>'.LF
        .'      </div>'.LF
        .'    </div>'.LF
        .'    <div id="subbar">'.LF
        . $subnav
        .'    </div>'.LF
        .'  </div>'.LF;
    }

    $output .=
         '  <div id="acp">'.LF
        .'    <div id="content">'.LF;

    if (!empty($_SESSION['PROPS_USER']['authenticated'])) {
        $output .=
         '      <div id="toggle">'.LF
        .'        <a id="toggle-handle" accesskey="m" title="' . props_gettext("Hide or display the side menu") . '" onclick="toggle_sidebar(); return false;" href="#">&laquo;</a>'.LF
        .'      </div>'.LF
        .'      <div id="sidebar">'.LF

        .'        <p>' . props_gettext("You are logged in as") . ':<br /><a href="./?module=users&amp;function=preferences"><strong>' . htmlspecialchars($_SESSION['PROPS_USER']['username']) . '</strong></a> [<a href="./?cmd=logout">' . props_gettext("Logout") . '</a>]</p>'.LF
        .'        <ul>'.LF
        .'          <li class="header">' . props_gettext("Shortcuts") . '</li>'.LF
        .'          <li><a href="../">' . props_gettext("Frontpage") . '</a></li>'.LF;

        if (!empty($GLOBALS['PROPS_SIDEBAR'])) {
            foreach($GLOBALS['PROPS_SIDEBAR'] as $key) {
                if (admin_has_priv($key['module'], $key['function'])) {
                    $output .= '          <li><a href="./?module=' . $key['module'] . '&amp;function=' . $key['function'] . $key['append'] . '">' . props_gettext('.'.$key['module'].'.'.$key['function']) . '</a></li>'.LF;
                }
            }
        }

        $output .=
         '        </ul>'.LF
        .'        <br />'.LF
        .'        <ul>'.LF;

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $output .=
             '          <li class="header">' . props_gettext("Bookmarks") . '&nbsp;<a href="#" onclick="toggle_element(\'bookmark_form\'); return false;" title="' . props_gettext("Add bookmark") . '">[+]</a></li>'.LF
            .'          <li id="bookmark_form" style="display: none;">'.LF
            .'            <form action="./" method="post">'.LF
            .'              <input name="module" type="hidden" value="users" />'.LF
            .'              <input name="function" type="hidden" value="bookmark_add" />'.LF
            .'              <input name="pageID" type="hidden" value="' . props_pageID() . '" />'.LF
            .'              <input class="large" type="text" name="bookmark_name" value="" /><br />'.LF
            .'              <input class="button" type="submit" name="op" value="' . props_gettext("Add bookmark") . '" />'.LF
            .'            </form>'.LF
            .'          </li>'.LF;
        } else {
            $output .= '          <li class="header">' . props_gettext("Bookmarks") . '</li>'.LF;
        }

        $q = "SELECT * from props_users_bookmarks WHERE user_id = ".$_SESSION['PROPS_USER']['user_id']." ORDER BY bookmark_name";
        $result = sql_query($q);
        while ($row = sql_fetch_object($result)) {
            $output .= '          <li><a href="'.htmlspecialchars($row->bookmark_url).'">'.$row->bookmark_name.'</a></li>'.LF;
        }

        $output .=
         '        </ul>'.LF
        .'        <br />'.LF;
    }

    $output .=
         '      </div>'.LF
        .'      <div id="main">'.LF;

    if (!empty($_SESSION['PROPS_USER']['authenticated'])) {
        $help = (isset($GLOBALS['PROPS_FUNCTION_INFO'])) ? '<a href="#" onclick="toggle_element(\'function_info\'); return false;"><img src="./images/button_help.png" title="' . props_gettext("Hide or display help") . '" alt="[' . props_gettext("help") . ']" /></a>&nbsp;' : '';
        $output .= '        <div id="breadcrum">' . $help . props_gettext('.'.$GLOBALS['PROPS_MODULE']) . '&nbsp;&gt;&nbsp;' . props_gettext('.'.$GLOBALS['PROPS_MODULE'].'.'.$GLOBALS['PROPS_FUNCTION']) . '</div>'.LF;
    }

    if (isset($GLOBALS['PROPS_ERRORSTACK'])) {
        foreach ($GLOBALS['PROPS_ERRORSTACK'] as $key => $val) {
            if (is_numeric($key)) {
                if (props_getkey('config.debug_mode') === TRUE) {
                    $output .= '<p class="error"><i><b>' . $GLOBALS['PROPS_ERROR_CODES'][$GLOBALS['PROPS_ERRORSTACK'][$key]['errno']] . '</b></i> in <strong>'.$GLOBALS['PROPS_ERRORSTACK'][$key]['errfile'].'</strong> on line <strong>'.$GLOBALS['PROPS_ERRORSTACK'][$key]['errline'].'</strong><br />'.LF
                                  . '<strong>Description:</strong> '.$GLOBALS['PROPS_ERRORSTACK'][$key]['errstr'].'</p>'.LF;
                } else {
                    $output .= '<p class="error">'.$GLOBALS['PROPS_ERRORSTACK'][$key]['errstr'].'</p>'.LF;
                }
            }
        }
    }

    if (isset($GLOBALS['PROPS_FUNCTION_INFO'])) {
        $output .=
         '<fieldset id="function_info" style="display: none;">'.LF
        .'  <legend>' . props_gettext("Function info") . '</legend>'.LF
        .$GLOBALS['PROPS_FUNCTION_INFO']
        .'</fieldset>'.LF;
    }

    $output .=
         '<!-- BEGIN: Main content -->'.LF.LF
        .$content.LF
        .'<!-- END: Main content -->'.LF
        .'      </div>'.LF
        .'    </div>'.LF
        .'  </div>'.LF
        .'  <div id="footer">'.LF
        .'    <ul>'.LF
        .'      <li>' . PROPS_POWEREDBY . '</li>'.LF
        .'      <li>' . sprintf(props_gettext('Render time: %s'), props_timerSplit()) . '</li>'.LF
        .'    </ul>'.LF
        .'  </div>'.LF
        .'</div>'.LF;
    if (isset($GLOBALS['JavaScriptPageEnd'])) {
        $output .= $GLOBALS['JavaScriptPageEnd'];
    }
    $output .=
         props_debug_info()
        .'</body>'.LF
        .'</html>'.LF;

    print $output;
}

/**
 * Construct page navigation
 */
function props_pagination($total, $position, $max_results = 25, $url = '')
{
    $output = '';
    if (empty($position)) {
        $position = 0;
    }

    $current_page = 1 + ceil(($position) / $max_results);
    $total_pages = ceil($total / $max_results);
    $prev_page = $position - $max_results;
    $next_page = $position + $max_results;
    $last_page = $total - $max_results;

    if ($position > 0) {
        $output .= '&nbsp;<a href="' . $url . '&amp;position=0">' . props_gettext("First") . '</a>&nbsp;';
    }

    if ($prev_page > 0) {
        $output .= '&nbsp;<a href="' . $url . '&amp;position=' . $prev_page . '">' . props_gettext("Previous") . '</a>&nbsp;';
    }

    $pages = ($total_pages == 0) ? 1 : $total_pages;
    $output .= '&nbsp;' . sprintf("page %s of %s", $current_page, $pages) . '&nbsp;';

    if ($next_page < $last_page) {
        $output .= '&nbsp;<a href="' . $url . '&amp;position=' . $next_page . '">' . props_gettext("Next") . '</a>&nbsp;';
    }

    if ($total > $position + $max_results) {
        $output .= '&nbsp;<a href="' . $url . '&amp;position=' . $last_page . '">' . props_gettext("Last") . '</a>&nbsp;';
    }

    return $output;
}

?>
