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
 * @version     $Id: props.admin.js,v 1.8 2008/01/07 08:48:33 roufneck Exp $
 */

/**
 * Focus login field
 */
function login_focus()
{
    if (document.getElementById('username').value == '') {
        document.getElementById('username').focus();
    } else {
        document.getElementById('password').focus();
    }
}

/**
 * Clear text
 */
function clearText(obj)
{
    if (obj.defaultValue == obj.value) {
            obj.value = '';
    } else {
        obj.value = obj.defaultValue;
    }
}

/**
 * Show/hide sidebar
 */
function toggle_sidebar()
{
    var sidebar = document.getElementById('sidebar');
    var main = document.getElementById('main');
    var toggle = document.getElementById('toggle');
    var handle = document.getElementById('toggle-handle');

    switch (sidebar.style.display)
    {
        // show
        case 'none':
            main.style.width = '76%';
            sidebar.style.display = 'block';
            toggle.style.width = '5%';
            handle.style.backgroundPosition = '0% 50%';
            toggle.style.left = '15%';
            handle.innerHTML = '&laquo;';
            break;

        // hide
        case 'block':
        default:
            main.style.width = '93%';
            sidebar.style.display = 'none';
            toggle.style.width = '20px';
            handle.style.backgroundPosition = '100% 50%';
            toggle.style.left = '0';
            handle.innerHTML = '&raquo;';
            break;
    }
}

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
function confirmmsg(url,msg) {
    if(confirm(msg))
        { location = url; }
}

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

function toggleTableRows(e) {
    var el = document.getElementById(e);

    if (el.style.display == "none") {
        el.style.display = "table-row-group";
    } else {
        el.style.display = "none";
    }
}

function toggleCheckbox(checkbox, e) {
    var el = document.getElementById(e);
    alert(e + ": " + checkbox.checked);

    var checkboxes = 0;

    for (counter = 0; counter < el.permission_array.length; counter++)
    {
        el.permission_array[counter].checked = checkbox.checked;
    }
}

/**
 *  Select tab and disable others
 *
 *  tabs must have an id like: t:name:number
 *  content id's must be like: c:name:number
 *
 *  the css class for tabs is: tab-name
 *      and for selected tabs: tab-name-selected
 *
 *  ex:
 *
 *  <div id="storytabs">
 *      <span id="t:story:0" onClick="selectTab(this)" class="tab-story-selected">Story</span>
 *      <span id="t:story:1" onClick="selectTab(this)" class="tab-story">Extras</span>
 *      <span id="t:story:2" onClick="selectTab(this)" class="tab-story">Threads</span>
 *  </div>
 *
 *  <div id="storycontent">
 *      <div id="c:story:0">content 0</div>
 *      <div id="c:story:1">content 1</div>
 *      <div id="c:story:2">content 2</div>
 *  </div>
 */
function selectTab(o)
{
    var oArray = o.id.split(":");
    var tabName = oArray[1];
    var tabNumb = oArray[2];
    var i = 0;
    while (eTab = document.getElementById("t:"+tabName+":"+i)) {
        eContent = document.getElementById("c:"+tabName+":"+i)
        if (i == tabNumb) {
            //alert(i+"="+tabNumb);
            eTab.className = "tab-select";
            eContent.style.display = "block";
        } else {
            eTab.className = "tab";
            eContent.style.display = "none";
        }
        i++;
    }
}

// This function is called when the form is submitted.
// It selects all threadcodes in both listboxes, so that their values are passed via POST.
//
function submitStory()
{
    for (i = 0; i < document.getElementById("selected_threadcodes").length; i++) {
        document.getElementById("selected_threadcodes").options[i].selected = true;
    }

    for (i = 0; i < document.getElementById("available_threadcodes").length; i++) {
        document.getElementById("available_threadcodes").options[i].selected = true;
    }

    var orderString = "";
    var objects = document.getElementsByTagName('li');
    for(var no=0;no<objects.length;no++){
        if(objects[no].className=='thumbnail_box' || objects[no].className=='thumbnail_highlighted'){
            if(orderString.length>0)orderString = orderString + ',';
            orderString = orderString + objects[no].id;
        }
    }

    document.getElementById("media").value = orderString;
}

function sortSelectBox(obj, threadcode)
{
    arrTexts = new Array();
    for(i = 0; i < obj.length; i++)  {
        arrTexts[i] = obj.options[i].text;
    }

    arrTexts.sort();

    for(i=0; i < obj.length; i++)  {
        if (arrTexts[i] == threadcode) {
            obj.options[i] = new Option(arrTexts[i], arrTexts[i], false, true);
        } else {
            obj.options[i] = new Option(arrTexts[i], arrTexts[i], false, false);
        }
    }
    return;
}


function add_threadcode()
{
    var new_threadcode = document.storyform.new_threadcode.value.replace(/([^0-9A-Za-z ])/g,"");
    document.storyform.new_threadcode.value = new_threadcode;

    // Output error if threadcode is empty
    if (new_threadcode == '') {
        //alert("You must enter a thread code.");
        return;
    }

    atc = document.getElementById("available_threadcodes");

    // Output error if threadcode already exists in available threadcodes
    for (i=0; i < atc.length; i++){
        if (atc.options[i].value.toLowerCase() == new_threadcode.toLowerCase()) {
            alert("This thread code already exists.");
            return;
        }
    }

    // Output error if threadcode already exists in selected threadcodes
    stc = document.getElementById("selected_threadcodes");
    for (i=0; i < stc.length; i++){
        if (atc.options[i].value.toLowerCase() == new_threadcode.toLowerCase()) {
            alert("This thread code already exists.");
            return;
        }
    }

    // Add the threadcode to the available threadcodes
    atc.options[atc.options.length] = new Option(new_threadcode, new_threadcode);

    sortSelectBox(atc, new_threadcode);

    // Clear the threadcode input field
    document.storyform.new_threadcode.value = '';
}



// This function transfers an option from the given array to the
function move_threadcodes(from, to)
{
    from = document.getElementById(from);
    to = document.getElementById(to);

    // if nothing selected, return
    if (from.selectedIndex == -1) {
        //alert('You must first select an option to move from one menu to the other.');
        return;
    }

    // loop through all options and copy the selected ones to the target array
    for (i=0; i < from.length; i++) {

        if (from.options[i].selected) {

            var exists = false;

            // Output error if threadcode already exists
            for (j=0; j < to.length; j++) {
                if (to.options[j].text == from.options[i].text) {
                    alert("This thread code already exists.");
                    exists = true;
                }
            }

            if (exists != true) {
                // copy the option to the target
                to.options[to.options.length] = new Option(from.options[i].text, from.options[i].text);
            }

            // remove the option from the source
            from.options[i] = null;

            // Continue with the next option, which now
            // has the same number as the removed option.
            i--;
        }
    }

    // refresh the listboxes
    sortSelectBox(from);
    sortSelectBox(to);
}

// Useful function which converts text to uppercase
function transformTag(obj)
{
    obj.value = obj.value.replace(/([^0-9A-Za-z ])/g,"");
}

// Remove media
function removeMedia(el)
{
    parentObj = document.getElementById(el).parentNode;
    childObj = document.getElementById(el);

    parentObj.removeChild(childObj);
    //var d = document.getElementById('story-images');
    //var element = document.getElementById('image-'+imageID);
    //d.removeChild(element);

    //var el = document.getElementById('media-'+mediaID);
    //el.parentNode.removeChild(el);

    //obj.parentNode.parentNode.removeChild(obj.parentNode);

    initMedia();
}

// Add media
function addMedia(obj)
{
    if (opener.document.getElementById(obj.id)) {
        alert('Item already exists');
    } else {
        var el_main = opener.document.getElementById('media-container');

        // Create list element
        el_Media = opener.document.createElement('li');
        el_Media.id = obj.id;
        el_Media.className = 'thumbnail_box';
        //el_Media.onclick = function(){this.parentNode.parentNode.removeChild(this.parentNode);};

        // Add mediaThumb
        el_IMG = opener.document.createElement('div');
        el_IMG.className = 'thumbnail_image';
        el_IMG.style.backgroundImage = "url('" + obj.src + "')";

        // Add remove button
        el_Label = opener.document.createElement('div');
        el_Label.className = 'thumbnail_label';
        el_Label.onclick = function(){this.parentNode.parentNode.removeChild(this.parentNode);};
        el_Label.appendChild(opener.document.createTextNode("Remove"));

        // Add it to the document
        el_Media.appendChild(el_IMG);
        el_Media.appendChild(el_Label);
        el_main.appendChild(el_Media);

        // Update sort functions
        opener.junkdrawer.restoreListOrder("media-container");
        opener.dragsort.makeListSortable(opener.document.getElementById("media-container"), opener.saveOrder);
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

function selectUser(userID, userName)
{
    opener.document.getElementById('user_id').value = userID;
    opener.document.getElementById('user_name').innerHTML = userName;
    window.close();
}
