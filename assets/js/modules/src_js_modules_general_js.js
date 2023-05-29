(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_general_js"],{

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

/***/ })

}]);
//# sourceMappingURL=src_js_modules_general_js.js.map