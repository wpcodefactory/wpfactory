(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_css-class-manager_js"],{

/***/ "./src/js/modules/css-class-manager.js":
/*!*********************************************!*\
  !*** ./src/js/modules/css-class-manager.js ***!
  \*********************************************/
/***/ ((module) => {

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
var CSSClassManager = {
  init: function init() {
    document.addEventListener("click", this.manageClasses);
    document.addEventListener("mouseover", this.manageClasses);
    document.addEventListener("mouseleave", this.manageClasses, true);
  },
  manageClasses: function manageClasses(event) {
    if (event.target.classList && event.target.classList.contains("css-modifier")) {
      var _event$target$dataset, _event$target$dataset2, _event$target$dataset3;
      /*if(event.type==='mouseout'){
          console.log(event.target)
      }*/
      var cssClassesStr = (_event$target$dataset = event.target.dataset.classes) !== null && _event$target$dataset !== void 0 ? _event$target$dataset : 'active';
      var cssClasses = cssClassesStr.split(",");
      var gettersSelector = event.target.dataset.classGetters;
      var losersSelector = event.target.dataset.classLosers;
      var addEvent = (_event$target$dataset2 = event.target.dataset.addEvent) !== null && _event$target$dataset2 !== void 0 ? _event$target$dataset2 : 'click';
      var removeEvent = (_event$target$dataset3 = event.target.dataset.removeEvent) !== null && _event$target$dataset3 !== void 0 ? _event$target$dataset3 : 'click';

      //let action = addEvent===event.type ? 'add' : (removeEvent===event.type ? 'remove' : '')

      if (addEvent === event.type) {
        CSSClassManager.changeClasses({
          event: event,
          selector: gettersSelector,
          cssClasses: cssClasses,
          action: 'add'
        });
      }
      if (removeEvent === event.type) {
        CSSClassManager.changeClasses({
          event: event,
          selector: losersSelector,
          cssClasses: cssClasses,
          action: 'remove'
        });
      }

      //CSSClassManager.changeClasses(losersSelector,cssClasses,'remove');
      /*if (gettersSelector) {
          document.querySelectorAll(gettersSelector).forEach(element => {
              if (toggle) {
                  if(!cssClasses.every(function(className) {
                      return Array.from(element.classList).includes(className);
                  })){
                    }
              } else {
                  element.classList.add(...cssClasses)
              }
          });
      }
      if (losersSelector) {
          document.querySelectorAll(losersSelector).forEach(element => {
              element.classList.remove(...cssClasses)
          });
      }*/
    }
  },

  changeClasses: function changeClasses(args) {
    var toggle = false;
    var selector = args.selector;
    var cssClasses = args.cssClasses;
    var action = args.action;
    //alert('555');
    document.querySelectorAll(selector).forEach(function (element) {
      var _element$classList;
      //window[method_prefix + method_name](arg1, arg2);
      (_element$classList = element.classList)[action].apply(_element$classList, _toConsumableArray(cssClasses));

      /*if ('add' === action) {
          if (toggle) {
              if (!cssClasses.every(function (className) {
                  return Array.from(element.classList).includes(className);
              })) {
                  element.classList.add(...cssClasses);
              } else {
                  element.classList.remove(...cssClasses);
              }
          } else {
              element.classList.add(...cssClasses)
          }
      } else if ('remove' === action) {
          if (toggle) {
              if (cssClasses.every(function (className) {
                  return Array.from(element.classList).includes(className);
              })) {
                  element.classList.remove(...cssClasses);
              } else {
                  element.classList.add(...cssClasses);
              }
          } else {
              element.classList.remove(...cssClasses);
          }
      }*/
    });
  }
};

module.exports = CSSClassManager;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_css-class-manager_js.js.map