"use strict";

function main() {
    let all = document.getElementById("dau-disable-all");
    let plugin = document.getElementById('dau-disable-plugin');
    let theme = document.getElementById('dau-disable-theme');
    let core = document.getElementById('dau-disable-core');
    let hide = document.getElementById('dau-hide-notification');
    let scrollBar = document.getElementById("dau-console");

    all.addEventListener('change', () => {
        if(all.checked) {
            plugin.disabled = true;
            theme.disabled = true;
            core.disabled = true;
            hide.disabled = true;
        } else {
            plugin.disabled = false;
            theme.disabled = false;
            core.disabled = false;
            hide.disabled = false;
        }
    });

    hide.addEventListener('change', () => {
        if(hide.checked) {
            plugin.disabled = true;
            theme.disabled = true;
            core.disabled = true;
            all.disabled = true;
        } else {
            plugin.disabled = false;
            theme.disabled = false;
            core.disabled = false;
            all.disabled = false;
        }
    });

    core.addEventListener('change', () => {
        if(core.checked) {
            all.disabled = true;
            hide.disabled = true;
        } else if(!theme.checked && !core.checked && !plugin.checked) {
            all.disabled = false;
            hide.disabled = false;
        }
    });

    theme.addEventListener('change', () => {
        if(theme.checked) {
            all.disabled = true;
            hide.disabled = true;
        } else if(!theme.checked && !core.checked && !plugin.checked) {
            all.disabled = false;
            hide.disabled = false;
        }
    });

    plugin.addEventListener('change', () => {
        if(plugin.checked) {
            all.disabled = true;
            hide.disabled = true;
        } else if(!theme.checked && !core.checked && !plugin.checked) {
            all.disabled = false;
            hide.disabled = false;
        }
    });

    if(all.checked == true) {
        plugin.disabled = true;
        theme.disabled = true;
        core.disabled = true;
        hide.disabled = true;
    } else if(hide.checked == true) {
        plugin.disabled = true;
        theme.disabled = true;
        core.disabled = true;
        all.disabled = true;
    } else if(theme.checked == true || plugin.checked == true || core.checked == true) {
        all.disabled = true;
        hide.disabled = true;
    }

    scrollBar.scrollTop = scrollBar.scrollHeight - scrollBar.clientHeight;

}

if(document.getElementById('dau') !== null) {
    main();
}


