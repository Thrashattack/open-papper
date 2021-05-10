
    {poll whichpoll='latest' format='
            <form action="./" method="post">
            <input type="hidden" name="cmd" value="polls-vote" />
            <input type="hidden" name="poll_id" value="%i" />
            <fieldset>
                <legend>Current Poll</legend>
                <div class="smalltext">%q</div>
                <table style="border: none;">
                    %o
                </table>
                <div class="center smalltext"><input class="button" type="submit" id="op" name="op" value="Submit Vote" /><br />
                <a class="center smalltext" href="%u">View Results</a></div>
            </fieldset>
            </form>'
        optionformat='<tr><td><input class="checkbox" type="radio" name="poll_option" value="%n" /></td><td valign="top" class="smalltext">%t</td></tr>'
    }
