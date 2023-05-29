let general = require('./general');
general.init()

const search = {
    init: function () {
        this.handle_searchbar_interaction(
            {
                search_toggler_selector: WPFTFEJS.search_toggler_selector,
                search_bar_selector: WPFTFEJS.search_bar_selector,
            }
        );
    },
    handle_searchbar_interaction: function (params) {
        const toggler = document.querySelector(params.search_toggler_selector);
        const elementToToggle = document.querySelector(params.search_bar_selector);
        if (toggler && elementToToggle) {
            toggler.addEventListener('mousedown', () => {
                general.toggleHeight(elementToToggle);
                elementToToggle.classList.toggle('active');
            });
        }
    }
}
module.exports = search;