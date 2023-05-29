let general = require('./general');
general.init()

const menus = {
    init: function () {
        this.handle_handheld_navigation(
            {
                handheld_toggler_selector: WPFTFEJS.handheld_toggler_selector,
                handheld_menu_selector: WPFTFEJS.handheld_menu_selector,
            }
        );
    },
    handle_handheld_navigation: function (params) {
        const toggler = document.querySelector(params.handheld_toggler_selector);
        const elementToToggle = document.querySelector(params.handheld_menu_selector);
        if (toggler && elementToToggle) {
            toggler.addEventListener('mousedown', () => {
                general.toggleHeight(elementToToggle);
                elementToToggle.classList.toggle('active');
            });
        }
    }
}
module.exports = menus;