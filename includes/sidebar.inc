
    {if user_logged_in='true'}
        <fieldset>
            <legend>User links</legend>

            {if var='cmd' comparison='eq' value='displaystory'}
                {if admin_has_priv='admincontent-story_edit'}
                <ul>
                    <li><a href="{getkey var='config.url.admin'}?module=admincontent&amp;function=story_edit&amp;story_id={story_id}">Edit story</a></li>
                </ul>
                {endif}
            {endif}

            {if user_has_priv='content-submit'}
                <ul>
                    <li><a href="{gen_url cmd='content-submit'}">Submit content</a></li>
                </ul>
            {endif}
            {bookmarks prepend='<ul>' format='<li><a href="%u">%n</a></li>' append='</ul>'}
        </fieldset>
    {endif}

    <fieldset>
        <legend>Search</legend>
        <form action="{base_url}" method="get">
            <p>
                <input name="cmd" type="hidden" value="archives-search" />
                <input class="large" type="text" name="search_string" value="{request var='search_string'}" />&nbsp;
                <input class="button" type="submit" id="op" name="op" value="search" />
            </p>
        </form>
    </fieldset>

    <fieldset>
        <legend>Top Stories</legend>
        <div style="float: right; margin-top: -1.3em;"><a class="rss" target="_blank" href="{gen_url cmd='rssfeed' format='rss'}">rss</a></div>
        {storylist minweight='60' order='weight DESC, published_stamp DESC' endrow='10' format='<li><a href="%u" style="text-decoration: none;">%h</a></li>' prepend='<ul>' append='</ul>' altoutput='No stories'}
    </fieldset>
