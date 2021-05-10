<?php
/**
 * Lib - system functions
 *
 * @package     api
 * @subpackage  system
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
 * @version     $Id: system.php,v 1.8 2007/11/02 16:53:49 roufneck Exp $
 */

/**
 * Defines
 */
define('ADD', 1);
define('DELETE', 2);
define('UPDATE', 3);

$GLOBALS['PROPS_SYSTEM_IGNORE'] = array('.', '..', 'CVS');
$GLOBALS['PROPS_SYSTEM_TAGS'] = array();
$GLOBALS['PROPS_SYSTEM_PRIVS'] = array();
$GLOBALS['PROPS_SYSTEM_STRINGS'] = array();

/**
 * Get dir size
 *
 * Scan recursive a dir and calculate the size.
 */
function system_get_dir_size($dir, $size = 0)
{
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false ) {
                if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'readme.txt') {
                    if (is_dir($dir . $file)) {
                        $size = system_get_dir_size($dir . $file);
                    } elseif (is_file($dir . $file)) {
                        $size += filesize($dir . $file);
                    }
                }
            }
            closedir($dh);
            return $size;
        }
    } else {
        trigger_error("Not a valid dir: $dir", E_USER_ERROR);
    }
}

/**
 * Update tags
 *
 * Scans for tags in de modules dir and updates tag_registry.php in case of
 * changes.
 */
function system_update_tags()
{
    $output = 'Updating tags...'.BR;

    if (!is_writable(PROPS_ROOT.'modules/tag_registry.php')) {
        trigger_error('No write access for: '.PROPS_ROOT.'modules/tag_registry.php', E_USER_ERROR);
    }

    ksort($GLOBALS['PROPS_SYSTEM_TAGS']);

    $output .= sprintf('Found %s tags', count($GLOBALS['PROPS_SYSTEM_TAGS'])).BR;

    include_once(PROPS_ROOT.'modules/tag_registry.php');
    if ($GLOBALS['PROPS_SYSTEM_TAGS'] == $GLOBALS['TAG']) {
        $output .= 'No differences found.'.BR;
        return $output;
    }

    $tag_list = '';
    foreach ($GLOBALS['PROPS_SYSTEM_TAGS'] as $key => $value) {
        $tag_list .= '$TAG["'.$key.'"] = "'.$value.'";'.LF;
    }

    // Get current locale contents
    $content = file_get_contents(PROPS_ROOT.'modules/tag_registry.php');
    if ($content) {
        // Replace everything between
            //BEGIN:TAGS
        // and
            //END:TAGS
        $content = preg_replace('/\/\/BEGIN:TAGS([\w\W]*)\/\/END:TAGS/m', '//BEGIN:TAGS'.LF.$tag_list.'//END:TAGS', $content);
        file_put_contents(PROPS_ROOT.'modules/tag_registry.php', $content);
    } else {
        trigger_error("Could not load file: $file", E_USER_ERROR);
        return $output;
    }

    // Cleanup
    unset($tag_list);
    unset($content);

    $output .= '<ul style="color: gray; font-size: .8em; margin: 1em;">'.LF;
    foreach ($GLOBALS['PROPS_SYSTEM_TAGS'] as $tag => $module) {
        $output .= "<li>$tag :: $module</li>".LF;
    }
    $output .= '</ul>'.LF;

    return $output;
}

/**
 * Update admin privs
 *
 * Scans for admin privileges in the modules dir and updates the
 * props_administrator_functions and props_administrator_group_privs tables.
 *
 * Since version 0.8 props uses admin_group_id 1 as the main administrators
 * group. Every time you update the privs, all existing (new and old) privs
 * are added to the administrators group. (ge)
 */
function system_update_privs()
{
    $output = 'Updating privs...'.BR;

    // Check privs in database
    $q = "SELECT * FROM props_users_privs";
    $result = sql_query($q);
    while ($row = sql_fetch_object($result)) {
        // Check if function exists
        if (isset($GLOBALS['PROPS_SYSTEM_PRIVS'][$row->type][$row->module][$row->function])) {
            if ($GLOBALS['PROPS_SYSTEM_PRIVS'][$row->type][$row->module][$row->function]['in_menu'] != $row->in_menu) {
                // Update function
                $GLOBALS['PROPS_SYSTEM_PRIVS'][$row->type][$row->module][$row->function]['status'] = UPDATE;
            } else {
                // Do nothing
                $GLOBALS['PROPS_SYSTEM_PRIVS'][$row->type][$row->module][$row->function]['status'] = FALSE;
            }
        } else {
            // Delete function
            $GLOBALS['PROPS_SYSTEM_PRIVS'][$row->type][$row->module][$row->function]['status'] = DELETE;
            $GLOBALS['PROPS_SYSTEM_PRIVS'][$row->type][$row->module][$row->function]['priv_id'] = $row->priv_id;
        }
    }
    sql_free_result($result);

    // Update functions
    $total = 0;
    $add = 0;
    $delete = 0;
    $update = 0;
    $debug_info = '';
    foreach ($GLOBALS['PROPS_SYSTEM_PRIVS'] as $type => $value) {
        foreach ($GLOBALS['PROPS_SYSTEM_PRIVS'][$type] as $module => $value) {
            foreach ($GLOBALS['PROPS_SYSTEM_PRIVS'][$type][$module] as $function => $value) {
                switch ($GLOBALS['PROPS_SYSTEM_PRIVS'][$type][$module][$function]['status']) {
                    case ADD:
                        $add++; $total++;
                        $debug_info .= "<li>(ADD) $module :: $function :: $type</li>".LF;

                        // Add function
                        $q  = "INSERT INTO props_users_privs SET "
                            . "module = '$module', "
                            . "function = '$function', "
                            . "type = '$type', "
                            . "in_menu = '" . $GLOBALS['PROPS_SYSTEM_PRIVS'][$type][$module][$function]['in_menu'] . "' ";
                        sql_query($q);

                        break;

                    case DELETE:
                        $delete++; $total++;
                        $debug_info .= "<li>(DELETE) <strike>$module :: $function :: $type</strike></li>".LF;

                        sql_query("DELETE FROM props_users_privs WHERE priv_id = ".$GLOBALS['PROPS_SYSTEM_PRIVS'][$type][$module][$function]['priv_id']);
                        sql_query("DELETE FROM props_users_groupprivs WHERE priv_id = ".$GLOBALS['PROPS_SYSTEM_PRIVS'][$type][$module][$function]['priv_id']);

                        break;

                    case UPDATE:
                        $update++; $total++;
                        $debug_info .= "<li>(UPDATE) $module :: $function :: $type</li>".LF;

                        // Update
                        $q  = "UPDATE props_users_privs SET "
                            . "in_menu = '" . $GLOBALS['PROPS_SYSTEM_PRIVS'][$type][$module][$function]['in_menu'] . "' "
                            . "WHERE module = '$module' AND function = '$function' AND type = $type";
                        sql_query($q);

                        break;

                    default:
                        $total++;
                        break;
                }
            }
        }
    }

    if (!empty($debug_info)) {
        $debug_info =
             '<ul style="color: gray; font-size: .8em; margin: 1em;">'.LF
            .$debug_info
            .'</ul>'.LF;
    }

    $output .= "Found $total privs (ADD:$add / DEL:$delete / UPD:$update)".BR;

    return $output.$debug_info;
}

/**
 * Update locales
 */
function system_update_locales($dir)
{
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false ) {
                if (!in_array($file, $GLOBALS['PROPS_SYSTEM_IGNORE'])) {
                    if (substr($file, -4) == '.php') {
                        //echo "[".__LINE__."] Updating: $dir/$file".BR;
                        unset($str);
                        unset($content);
                        $output = '';
                        $obsolete = '';

                        // Get contents
                        include("$dir/$file");

                        // Add missing strings
                        foreach ($GLOBALS['PROPS_SYSTEM_STRINGS'] as $key => $value) {
                            // Handle default file
                            if ($file == 'default.php') {
                                if (isset($GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default'])) {
                                    $output .= '$DEFAULT["'.$key.'"] = "'.$GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default'].'";'.LF;
                                } elseif ($GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['translate']) {
                                    $output .= '$DEFAULT["'.$key.'"] = ""; // Needs translation'.LF;
                                } else {
                                    $output .= '$DEFAULT["'.$key.'"] = "";'.LF;
                                }

                                // Add filenames to default.php
                                if (isset($GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['file'])) {
                                    foreach ($GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['file'] as $filename) {
                                        $output .= '// '.$filename.LF;
                                    }
                                }

                            // If $str == PROPS_SYSTEM_STRINGS, add // Needs translation
                            } elseif (isset($GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default']) && isset($str[$key]) && $str[$key] == $GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default']) {
                                $output .= '$str["'.$key.'"] = "'.$GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default'].'"; // Needs translation'.LF;
                            // If !empty, string is already translated
                            } elseif (!empty($str[$key])) {
                                $output .= '$str["'.$key.'"] = "'.$str[$key].'";'.LF;
                            // Get default translation
                            } elseif (isset($GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default'])) {
                                $output .= '$str["'.$key.'"] = "'.$GLOBALS['PROPS_SYSTEM_STRINGS'][$key]['default'].'"; // Needs translation'.LF;
                            // Output empty string
                            } else {
                                $output .= '$str["'.$key.'"] = ""; // Needs translation'.LF;
                            }
                        }

                        if ($file == 'default.php') {
                            $str = $DEFAULT;
                        }

                        // Comment '//' absolete strings
                        foreach ($str as $key => $value) {
                            if (!isset($GLOBALS['PROPS_SYSTEM_STRINGS'][$key])) {
                                if (empty($obsolete)) $obsolete = LF.LF;
                                $obsolete .= '// OBSOLETE: $str["'.$key.'"] = "'.$str[$key].'";'.LF;
                            }
                        }

                        // Get current locale contents
                        $content = file_get_contents("$dir/$file");
                        if ($content) {
                            // Replace everything between
                                //BEGIN:STRINGS
                            // and
                                //END:STRINGS

                            $content = preg_replace('/\/\/BEGIN:STRINGS([\w\W]*)\/\/END:STRINGS/m', '//BEGIN:STRINGS'.LF.$output.'//END:STRINGS'.$obsolete, $content);
                            file_put_contents("$dir/$file", $content);
                        } else {
                            echo "Could not load $dir/$file<br />";
                        }

                        // Cleanup
                        unset($str);
                        unset($output);
                        unset($content);
                        unset($obsolete);

                        if (!empty($debug_info)) {
                            $debug_info =
                                 '<ul style="color: gray; font-size: .8em; margin: 1em;">'.LF
                                .$debug_info
                                .'</ul>'.LF;
                        }
                    }
                }
            }
            closedir($dh);
        }
    } else {
        echo "Not a valid locale dir: $dir<br/>";
    }
}

/**
 * Scan system changes
 */
function system_scan($dir)
{
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false ) {
                if (!in_array($file, $GLOBALS['PROPS_SYSTEM_IGNORE'])) {
                    if (is_dir($dir . '/' . $file)) {
                        //echo "Processing: $dir/$file".BR;
                        system_scan($dir . '/' . $file);
                    } elseif (substr($file, -4) == '.php') {
                        //echo "Processing: $dir/$file".BR;

                        $path = str_replace('\\', '/', $dir);
                        $path = str_replace(PROPS_ROOT, '', $path);
                        $path = explode('/', $path, 4);

                        // Get contents
                        $content = file_get_contents("$dir/$file");

                        if ($content) {

                            // Detect module
                            if (isset($path[0]) && $path[0] == 'modules' && isset($path[1])) {
                                $module = $path[1];

                                // Detect admin functions
                                if (isset($path[2])) {

                                    switch ($path[2]) {
                                        case 'admin':
                                            $function = substr($file, 0, -4);

                                            // Add to string list
                                            switch ($module) {
                                                case 'admincontent': $default = 'Content'; break;
                                                case 'adminmain': $default = 'Props'; break;
                                                default: $default = ucfirst(strtolower($module)); break;
                                            }

                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module]['counter'] = 1;
                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module]['translate'] = TRUE;
                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module]['file'][] = "$dir/$file";
                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module]['default'] = $default;
                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$function]['counter'] = 1;
                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$function]['translate'] = TRUE;
                                            $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$function]['file'][] = "$dir/$file";

                                            // Add to privs list
                                            $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_ADMIN][$module][$function]['in_menu'] = 0;
                                            // Add to DB as default action
                                            $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_ADMIN][$module][$function]['status'] = ADD;

                                            break;

                                        case 'tags':
                                            $tag = substr($file, 0, -4);
                                            if (isset($GLOBALS['PROPS_SYSTEM_TAGS'][$tag])) {
                                                if ($module == 'customtags') {
                                                    $GLOBALS['PROPS_SYSTEM_TAGS'][$tag] = $module;
                                                }
                                            } else {
                                                $GLOBALS['PROPS_SYSTEM_TAGS'][$tag] = $module;
                                            }

                                            break;
                                    }
                                }

                                // @adminprivs can only be in the admin functions
                                if (isset($function)) {
                                    // Grab all @adminprivs
                                    preg_match_all('/@adminprivs(.*)/', $content, $matches_array);
                                    foreach ($matches_array[1] as $fnct) {
                                        list($priv, $default) = explode(' ', trim($fnct), 2);
                                        $priv = trim($priv);

                                        $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$priv]['counter'] = 1;
                                        $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$priv]['translate'] = TRUE;
                                        $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$priv]['file'][] = "$dir/$file";
                                        $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$priv]['default'] = trim($default);

                                        // Add to priv list
                                        $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_ADMIN][$module][$priv]['in_menu'] = 0;
                                        // Add to DB as default action
                                        $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_ADMIN][$module][$priv]['status'] = ADD;
                                    }

                                    // Grab all @adminnav
                                    preg_match_all('/@adminnav(.*)/', $content, $matches_array);
                                    foreach ($matches_array[1] as $nav) {
                                        $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_ADMIN][$module][$function]['in_menu'] = trim($nav);
                                    }

                                    // Grab @admintitle
                                    preg_match_all('/@admintitle(.*)/', $content, $matches_array);
                                    foreach ($matches_array[1] as $default) {
                                        $GLOBALS['PROPS_SYSTEM_STRINGS']['.'.$module.'.'.$function]['default'] = trim($default);
                                        //$GLOBALS['DEFAULT']['.'.$module.'.'.$function] = trim($default);
                                        //echo '.'.$module.'.'.$function . ' = ' . trim($default).BR;
                                    }
                                }

                                // Grab all @userprivs
                                preg_match_all('/@userprivs(.*)/', $content, $matches_array);
                                foreach ($matches_array[1] as $fnct) {
                                    list($priv, $default) = explode(' ', trim($fnct), 2);
                                    $priv = trim($priv);

                                    $GLOBALS['PROPS_SYSTEM_STRINGS']['+'.$module.'.'.$priv]['counter'] = 1;
                                    $GLOBALS['PROPS_SYSTEM_STRINGS']['+'.$module.'.'.$priv]['translate'] = TRUE;
                                    $GLOBALS['PROPS_SYSTEM_STRINGS']['+'.$module.'.'.$priv]['file'][] = "$dir/$file";
                                    $GLOBALS['PROPS_SYSTEM_STRINGS']['+'.$module.'.'.$priv]['default'] = trim($default);

                                    // Add to priv list
                                    $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_USER][$module][$priv]['in_menu'] = 0;
                                    // Add to DB as default action
                                    $GLOBALS['PROPS_SYSTEM_PRIVS'][PROPS_PRIVTYPE_USER][$module][$priv]['status'] = ADD;
                                }
                            }

                            // Scan for translation strings
                            preg_match_all('/(props_gettext|props_error)\(\"([^\"]+)\"/', $content, $matches_array);
                            foreach ($matches_array[2] as $string) {
                                if (isset($GLOBALS['PROPS_SYSTEM_STRINGS'][$string])) {
                                    $GLOBALS['PROPS_SYSTEM_STRINGS'][$string]['counter']++;
                                } else {
                                    $GLOBALS['PROPS_SYSTEM_STRINGS'][$string]['counter'] = 1;
                                }
                                $GLOBALS['PROPS_SYSTEM_STRINGS'][$string]['file'][] = "$dir/$file";
                                $GLOBALS['PROPS_SYSTEM_STRINGS'][$string]['translate'] = FALSE;
                            }
                        }

                        // Cleanup memory
                        unset($content);
                        unset($module);
                        unset($function);
                        unset($tag);
                    }
                }
            }
            closedir($dh);
        }
    } else {
        trigger_error("Not a valid dir: $dir", E_USER_ERROR);
    }
}

?>
