(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_search_js"],{

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

/***/ "./src/js/modules/search.js":
/*!**********************************!*\
  !*** ./src/js/modules/search.js ***!
  \**********************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var general = __webpack_require__(/*! ./general */ "./src/js/modules/general.js");
general.init();
var search = {
  init: function init() {
    this.handle_searchbar_interaction({
      search_toggler_selector: WPFTFEJS.search_toggler_selector,
      search_bar_selector: WPFTFEJS.search_bar_selector
    });
  },
  handle_searchbar_interaction: function handle_searchbar_interaction(params) {
    var toggler = document.querySelector(params.search_toggler_selector);
    var elementToToggle = document.querySelector(params.search_bar_selector);
    if (toggler && elementToToggle) {
      toggler.addEventListener('mousedown', function () {
        general.toggleHeight(elementToToggle);
        elementToToggle.classList.toggle('active');
      });
    }
  }
};
module.exports = search;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_search_js.js.map