
    <div class="title">
        <h1>
            <a href="{base_url}" title="Return to home page">{publication_name}</a>
        </h1>
    </div>

    <ul id="navBar">
        <li><a href="{gen_url}">Home</a></li>
        {sectionlist format='<li><a href="%u">%N</a></li>'}
    </ul>

    <div id="userBar">
        <div id="userBarLeft">
            {current_datetime dateformat='%A, %e %B %Y'}
        </div>
        <div id="userBarRight">
        {if user_logged_in='false'}
            <a href="{gen_url cmd='users-login'}">Login</a>&nbsp;|&nbsp;
            <a href="{gen_url cmd='users-register'}">Create FREE account</a>&nbsp;|&nbsp;
            <a href="{gen_url cmd='users-passwordrecovery'}">Forgot password?</a>&nbsp;|&nbsp;
            <a href="{gen_url cmd='archives-advanced'}">Advanced search</a>
        {else}
            <a href="{gen_url cmd='logout'}">Logout [{user_name}]</a>&nbsp;|&nbsp;
            <a href="{gen_url cmd='users-preferences'}">Preferences</a>&nbsp;|&nbsp;
            <a href="{gen_url cmd='archives-advanced'}">Advanced search</a>
        {endif}
        </div>
    </div>
