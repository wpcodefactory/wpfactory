(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_slider_js"],{

/***/ "./src/js/modules/slider.js":
/*!**********************************!*\
  !*** ./src/js/modules/slider.js ***!
  \**********************************/
/***/ ((module) => {

var slider = {
  init: function init() {
    var splidesObjs = this.setupSplides();
    this.initSplides(splidesObjs);
    this.handleSplideSync(splidesObjs);
  },
  initSplides: function initSplides(splides) {
    (splides || []).forEach(function (item) {
      item.splideObj.mount();
    });
  },
  handleSplideSync: function handleSplideSync(splides) {
    (splides || []).forEach(function (item) {
      (item.target || []).forEach(function (elementTarget) {
        for (var i = 0; i < splides.length; i++) {
          var splideObj = splides[i];
          if (elementTarget === splideObj.element) {
            item.splideObj.sync(splideObj.splideObj);
          }
        }
      });
    });
  },
  setupSplides: function setupSplides() {
    var splides = document.querySelectorAll('.splide');
    var splidesArr = [];
    if (splides.length) {
      for (var i = 0; i < splides.length; i++) {
        var splideElement = splides[i];
        //3.1 if no options are defined by 'data-splide' attribute: take these default options
        var splideDefaultOptions = {
          type: 'slide',
          autoHeight: true,
          pagination: false,
          perMove: 1,
          breakpoints: {
            764: {
              perPage: 1
            }
          },
          arrowPath: "M 20 17.5 L 20 0 L 40 20 L 20 40 L 20 22.5 L 0 22.5 L 0 17.5 Z M 20 17.5 "
        };
        /**
         * 3.2 if options are defined by 'data-splide' attribute: default options will we overridden
         * see documentation: https://splidejs.com/guides/options/#by-data-attribute
         **/
        var splide = new Splide(splideElement, splideDefaultOptions);
        splidesArr.push({
          splideObj: splide,
          //classList: splideElement.classList,
          element: splideElement,
          target: document.querySelectorAll(splideElement.dataset.syncTarget)
        });
      }
      return splidesArr;
    }
  }
};
module.exports = slider;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_slider_js.js.map