/**
 * JavaScript functions
 *
 * @package     JavaScript
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
 * @version     $Id: props.js,v 1.1 2007/10/26 08:23:10 roufneck Exp $
 */

/**
 * Show/hide #element
 */
function toggle_element(element_name)
{
    var el = document.getElementById(element_name);
    if (!el) {
        return false;
    }

    switch (el.style.display)
    {
        // show
        case 'none':
            el.style.display = 'block';
            break;

        // hide
        case 'block':
        default:
            el.style.display = 'none';
            break;
    }
}

/**
 * Confirm message
 */
function confirmSubmit(msg)
{
    var d = document.createElement("div");
    d.id = "overlay";
    document.body.appendChild(d);
    d.style.display = "block";

    if (confirm(msg)) {
        d.style.display = "none";
        return true;
    } else {
        d.style.display = "none";
        return false;
    }
}

/**
 * enables or disables the pulldown menus depending on whether the "Enabled" checkbox is checked
 */
function disableFormOptions(checkbox, fields)
{
    obj = document.getElementById(checkbox);
    var change = true;
    if (obj.checked) {
        change = false;
    }
    var oArray = fields.split(",");

    for(i=0; i < oArray.length; i++)  {
        if (el = document.getElementById(oArray[i])) {
            el.disabled = change;
        }
    }
}

var debugOpen = '';
function debugFocus(debugField)
{
    if (debugOpen) {

        document.getElementById(debugOpen).style.visibility = 'hidden';
    }


    if (debugOpen == debugField) {

        document.getElementById(debugField).style.visibility = 'hidden';

        debugOpen = '';
    } else {
        document.getElementById(debugField).style.visibility = 'visible';
        debugOpen = debugField;
    }
}
