
{if key='wysiwyg.tiny_mce.compress' comparison='eq' value='1'}
    <!-- BEGIN: TINY_MCE COMPRESSED -->
    <script type="text/javascript" src="{getkey var='config.url.scripts'}tiny_mce/tiny_mce_gzip.js"></script>
    <script type="text/javascript">
        tinyMCE_GZ.init({
            plugins: "{getkey var='wysiwyg.tiny_mce.plugins'}",
            themes : "advanced",
            languages : "en",
            disk_cache : true,
            debug : false
        });
    </script>
{else}
    <!-- BEGIN: TINY_MCE -->
    <script type="text/javascript" src="{getkey var='config.url.scripts'}tiny_mce/tiny_mce.js"></script>
{endif}
    <script language="javascript" type="text/javascript">
        tinyMCE.init({
            mode : "exact",
            elements : "body_content",
            plugins: "{getkey var='wysiwyg.tiny_mce.plugins'}",
            theme: "advanced",
            theme_advanced_toolbar_align : "left",
            theme_advanced_toolbar_location: "top",
            theme_advanced_buttons1: "{getkey var='wysiwyg.tiny_mce.buttons1'}",
            theme_advanced_buttons2: "{getkey var='wysiwyg.tiny_mce.buttons2'}",
            theme_advanced_buttons3: "",
            verify_html: false,
            cleanup_on_startup: false,
            relative_urls: false,
            invalid_elements: ""
        });
    </script>
    <!-- END: TINY_MCE -->
