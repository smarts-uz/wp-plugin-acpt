
// A collection of general purpose, reusable functions

/**
 *
 * @param action
 * @param data
 * @return {Promise<void>}
 */
async function wpAjaxRequest(action, data)
{
    let formData;
    const baseAjaxUrl = (typeof ajaxurl === 'string') ? ajaxurl : '/wp-admin/admin-ajax.php';

    formData = new FormData();
    formData.append('action', action);
    formData.append('data', JSON.stringify(data));

    return fetch(baseAjaxUrl, {
        method: 'POST',
        body: formData
    });
}

function debounce(func, timeout = 300){
    let timer;

    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

/**
 *
 * @param string
 * @returns {*}
 */
function useTranslation(string) {
    if(typeof document.adminjs === 'undefined'){
        return string;
    }

    if(typeof document.adminjs.translations === 'undefined'){
        return string;
    }

    if(typeof document.adminjs.translations.translations === 'undefined'){
        return string;
    }

    const translations = document.adminjs.translations.translations;

    if(typeof translations === 'undefined'){
        return string;
    }

    if(typeof translations[string] !== 'undefined' && translations[string] !== ''){
        return translations[string]
            .replace(/&amp;/g, "&")
            .replace(/&lt;/g, "<")
            .replace(/&gt;/g, ">")
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'")
            ;
    }

    return string;
};

/**
 * WP default TinyMCE settings
 */
function tinyMCEWordpressDefaultSettings(id, rows = 8) {

    return {
        selector: id,
        theme: "modern",
        skin: "lightgray",
        content_css: [
            `${document.globals.site_url}/wp-includes/css/dashicons.min.css`,
            `${document.globals.site_url}/wp-includes/js/tinymce/skins/wordpress/wp-content.css`,
        ],
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        browser_spellcheck: true,
        fix_list_elements: true,
        entities: "38,amp,60,lt,62,gt",
        entity_encoding: "raw",
        keep_styles: false,
        cache_suffix: "wp-mce-49110-20201110",
        height: (parseInt(rows)*25),
        rows: rows,
        resize: true,
        branding: false,
        menubar: false,
        statusbar: true,
        plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
        wpautop: false,
        indent: true,
        toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv,dfw",
        toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
        toolbar3: "",
        toolbar4: "",
        tabfocus_elements: ":prev,:next",
        body_class: "content post-type-movie post-status-publish page-template-default locale-it-it",
        wp_autoresize_on: false,
        wp_wordcount: true,
        add_unload_trigger: false,
        setup: function (ed) {
            ed.on('load', function(args) {
                const id = ed.id;
                const height = (parseInt(rows)*25);
                const iframe = document.getElementById(id + '_ifr');
                iframe.style.height = height + 'px';
            });
        }
    };
}