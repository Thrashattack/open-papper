<?php
/**
 * Lib - common functions
 *
 * Main libarary that initiates all basic functions and constants.
 *
 * @package     api
 * @subpackage  dbsession
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
 * @version     $Id: sessions.php,v 1.5 2007/11/19 11:18:45 roufneck Exp $
 */

if (props_getkey('config.db.sessions')) {
    // Start database sessions
    $props_session = new props_session();
}

/**
 * This class is an adaptation of John Herren's code from the "Trick out your
 * session handler" article ({@link http://devzone.zend.com/node/view/id/141})
 * and Chris Shiflett's code from Chapter 8, Shared Hosting - Pg 78-80, of his
 * book - "Essential PHP Security" ({@link http://phpsecurity.org/code/ch08-2})
 */
class props_session
{
    /**
     * Constructor of class
     *
     * @return  void
     */
    function props_session()
    {
        // get session lifetime
        $this->sessionLifetime = get_cfg_var('session.gc_maxlifetime');

        // register the new handler
        session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
        );
        register_shutdown_function('session_write_close');
    }

    /**
     * Regenerates the session id.
     *
     * <b>Call this method whenever you do a privilege change!</b>
     *
     * @return  void
     */
    function regenerate_id()
    {
        // Save the old session id
        $old_session_id = session_id();

        // Regenerate the session id. this function will create a new session,
        // with a new id and containing the data from the old session
        session_regenerate_id();

        // Do some magic to delete the old session data
        if (!props_getkey('config.db.sessions')) {
            // Save current session data
            $session_data = $_SESSION;

            // Store the new session id
            $new_session_id = session_id();

            // Go back to the old session and destroy it
            session_id($old_session_id);
            session_destroy();

            // Start the new session id
            session_id($new_session_id);
            session_start();
            $_SESSION = $session_data;
        }

        // Make sure the old session is destroyed so it can't be used for hacking
        props_session::destroy($old_session_id);
    }

    /**
     * Get the number of online users
     *
     * @return  integer  approximate number of users curently online
     */
    function get_users_online()
    {
        // Call the garbage collector
        $this->gc($this->sessionLifetime);

        // Counts the rows from the database
        $result = sql_query("SELECT COUNT(session_id) as count FROM props_sessions");
        $row = sql_fetch_assoc($result);

        // Return the number of found rows
        return $row['count'];
    }

    /**
     * Custom open() function
     *
     * @access private
     */
    function open($save_path, $session_name)
    {
        return TRUE;
    }

    /**
     * Custom close() function
     *
     * @access private
     */
    function close()
    {
        return TRUE;
    }

    /**
     * Custom read() function
     *
     * @access private
     */
    function read($session_id)
    {
        // Reads session data associated with the session id but only if the
        // HTTP_USER_AGENT is the same as the one who had previously written to
        // this session and if session has not expired.
        $q  = "SELECT session_data FROM props_sessions WHERE "
            . "  session_id = '".$session_id."' AND "
            . "  http_user_agent = '".md5($_SERVER['HTTP_USER_AGENT'])."' AND "
            . "  session_expire > '".time()."'";
        $result = sql_query($q);

        // If anything was found
        if (is_resource($result) && sql_num_rows($result) > 0) {
            // Return found data
            $row = sql_fetch_assoc($result);
            // Don't bother with the unserialization, PHP handles this automatically.
            return $row['session_data'];
        }

        // If there was an error return an epmty string - this HAS to be an
        // empty string
        return '';
    }

    /**
     *  Custom write() function
     *
     *  @access private
     */
    function write($session_id, $session_data)
    {
        $user_id = (isset($_SESSION['PROPS_USER']['user_id'])) ? $_SESSION['PROPS_USER']['user_id'] : 0;

        // Insert OR update session data
        $q = "INSERT INTO props_sessions SET "
            ."  session_id = '".$session_id."', "
            ."  session_data = '".sql_escape_string($session_data)."', "
            ."  session_expire = '".(time() + $this->sessionLifetime)."', "
            ."  http_user_agent = '".md5($_SERVER['HTTP_USER_AGENT'])."', "
            ."  user_id = '".$user_id."' "
            ."ON DUPLICATE KEY UPDATE "
            ."  session_data = '".sql_escape_string($session_data)."', "
            ."  session_expire = '".(time() + $this->sessionLifetime)."', "
            ."  user_id = '".$user_id."'";
        $result = sql_query($q);

        // If anything happened
        if ($result) {
            // note that after this type of queries, mysql_affected_rows() returns
            // - 1 if the row was inserted
            // - 2 if the row was updated

            // Return true if the row was updated
            if (@mysql_affected_rows() > 1) {
                return TRUE;
            // If the row was inserted return an empty string
            } else {
                return '';
            }
        }

        // if something went wrong, return false
        return FALSE;

/* for MySQL < 4.1
        // First checks if there is a session with this id
        $result = sql_query("SELECT * FROM props_sessions WHERE session_id = '".$session_id."'");

        // If there is
        if (sql_num_rows($result) > 0) {

            // Update the existing session's data and set new expiry time
            $q = "UPDATE props_sessions SET "
                ."  session_data = '".sql_escape_string($session_data)."', "
                ."  session_expire = '".(time() + $this->sessionLifetime)."', "
                ."  user_id = '".$user_id."' "
                ."WHERE session_id = '".$session_id."'";
            $result = sql_query($q);

            // If anything happened return true
            if (sql_affected_rows()) {
                return TRUE;
            }

        // Insert a new record if this session id is not in the database.
        } else {
            $q = "INSERT INTO props_sessions SET "
                ."  session_id = '".$session_id."', "
                ."  session_data = '".sql_escape_string($session_data)."', "
                ."  session_expire = '".(time() + $this->sessionLifetime)."', "
                ."  http_user_agent = '".md5($_SERVER['HTTP_USER_AGENT'])."', "
                ."  user_id = '".$user_id."' ";
            $result = sql_query($q);

            // Return an empty string if anything happened
            if (sql_affected_rows()) {
                return '';
            }
        }

        // If something went wrong, return false
        return FALSE;
/**/
    }

    /**
     *  Custom destroy() function
     *
     *  @access private
     */
    function destroy($session_id)
    {
        if (props_getkey('config.db.sessions')) {
            // Delete current session_id and all older sessions
            $user_id = (isset($_SESSION['PROPS_USER']['user_id'])) ? $_SESSION['PROPS_USER']['user_id'] : 0;
            $q = ($user_id > 0) ?  " OR user_id = '$user_id'": '';
            $result = sql_query("DELETE FROM props_sessions WHERE session_id = '".$session_id."'".$q);

            // If anything happened return true
            if (sql_affected_rows()) {
                return TRUE;
            }
        }

        // If something went wrong, return false
        return FALSE;
    }

    /**
     *  Custom gc() function (garbage collector)
     *
     *  @access private
     */
    function gc($maxlifetime)
    {
        // Delete expired sessions from database
        $result = sql_query("DELETE FROM props_sessions WHERE session_expire < '".(time() - $maxlifetime)."'");
    }
}

?>
