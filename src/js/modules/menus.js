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
        toggler.addEventListener('mousedown', () => {
            menus.toggleHeight(elementToToggle);
            elementToToggle.classList.toggle('active');
        });
    },
    toggleHeight: function (element) {
        if (!element.style.height || element.style.height == '0px') {
            //element.style.height = Array.prototype.reduce.call(element.childNodes, function(p, c) {return p + (c.offsetHeight || 0);}, 0) + 'px';
            element.style.height = element.scrollHeight + 'px'
        } else {
            element.style.height = '0px';
        }
    }
}
module.exports = menus;