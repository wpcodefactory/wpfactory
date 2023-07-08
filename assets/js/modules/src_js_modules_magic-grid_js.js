(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_magic-grid_js"],{

/***/ "./src/js/modules/magic-grid.js":
/*!**************************************!*\
  !*** ./src/js/modules/magic-grid.js ***!
  \**************************************/
/***/ ((module) => {

var magicGrid = {
  gridSelector: '.reviews-container',
  init: function init() {
    var magicGrid = new MagicGrid({
      container: magicGrid.gridSelector,
      // Required. Can be a class, id, or an HTMLElement.
      items: 8,
      // For a grid with 20 items. Required for dynamic content.
      animate: true // Optional.
    });

    magicGrid.listen();
  }
};
module.exports = magicGrid;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_magic-grid_js.js.map