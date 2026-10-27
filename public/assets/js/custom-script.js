/*================================================================================
	Item Name: Materialize - Material Design Admin Template
	Version: 3.0
	Author: GeeksLabs
	Author URL: http://www.themeforest.net/user/geekslabs
================================================================================

NOTE:
------
PLACE HERE YOUR OWN JS CODES AND IF NEEDED.
WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR CUSTOM SCRIPT IT'S BETTER LIKE THIS. */

document.addEventListener('DOMContentLoaded', function () {
    // Init Materialize Collapsible untuk semua ul.collapsible (termasuk sidebar card)
    document.querySelectorAll('ul.collapsible').forEach(function (el) {
        if (M.Collapsible && !M.Collapsible.getInstance(el)) {
            M.Collapsible.init(el, {
                accordion: el.getAttribute('data-collapsible') === 'accordion'
            });
        }
    });

    // Init Materialize FormSelect untuk semua <select>
    document.querySelectorAll('select:not([data-material-select])').forEach(function (el) {
        el.setAttribute('data-material-select', '1');
        if (M.FormSelect) {
            M.FormSelect.init(el);
        }
    });

    // Init Materialize Modal
    document.querySelectorAll('.modal').forEach(function (el) {
        if (M.Modal && !M.Modal.getInstance(el)) {
            M.Modal.init(el);
        }
    });
});
