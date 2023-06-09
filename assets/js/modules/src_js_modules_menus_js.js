(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_menus_js"],{

/***/ "./src/js/modules/general.js":
/*!***********************************!*\
  !*** ./src/js/modules/general.js ***!
  \***********************************/
/***/ ((module) => {

var general = {
  init: function init() {},
  toggleHeight: function toggleHeight(element) {
    if (!element.style.height || element.style.height == '0px') {
      element.style.height = element.scrollHeight + 'px';
    } else {
      element.style.height = '0px';
    }
  }
};
module.exports = general;

/***/ }),

/***/ "./src/js/modules/menus.js":
/*!*********************************!*\
  !*** ./src/js/modules/menus.js ***!
  \*********************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var general = __webpack_require__(/*! ./general */ "./src/js/modules/general.js");
general.init();
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
    if (toggler && elementToToggle) {
      toggler.addEventListener('mousedown', function () {
        general.toggleHeight(elementToToggle);
        elementToToggle.classList.toggle('active');
      });
    }
  }
};
module.exports = menus;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_menus_js.js.map