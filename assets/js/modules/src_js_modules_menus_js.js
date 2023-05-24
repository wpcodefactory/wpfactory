(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_menus_js"],{

/***/ "./src/js/modules/menus.js":
/*!*********************************!*\
  !*** ./src/js/modules/menus.js ***!
  \*********************************/
/***/ ((module) => {

var menus = {
  init: function init() {
    this.handle_handheld_navigation({
      handheld_toggler_selector: WPFTFEJS.handheld_toggler_selector,
      handheld_menu_selector: WPFTFEJS.handheld_menu_selector
    });
  },
  handle_handheld_navigation: function handle_handheld_navigation(params) {
    var toggler = document.querySelector(params.handheld_toggler_selector);
    var elementToToggle = document.querySelector(params.handheld_menu_selector);
    toggler.addEventListener('mousedown', function () {
      menus.toggleHeight(elementToToggle);
      elementToToggle.classList.toggle('active');
    });
  },
  toggleHeight: function toggleHeight(element) {
    if (!element.style.height || element.style.height == '0px') {
      //element.style.height = Array.prototype.reduce.call(element.childNodes, function(p, c) {return p + (c.offsetHeight || 0);}, 0) + 'px';
      element.style.height = element.scrollHeight + 'px';
    } else {
      element.style.height = '0px';
    }
  }
};
module.exports = menus;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_menus_js.js.map