(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_module-template_js"],{

/***/ "./src/js/modules/module-template.js":
/*!*******************************************!*\
  !*** ./src/js/modules/module-template.js ***!
  \*******************************************/
/***/ ((module) => {

var moduleTemplate = {
  init: function init() {
    setTimeout(function () {
      moduleTemplate.handleCodeMirror();
    }, 1000);
  },
  handleCodeMirror: function handleCodeMirror() {
    var myTextArea = document.querySelector(".cf-container-carbon_fields_container_template .cf-textarea__input");
    if (myTextArea) {
      var editor = CodeMirror.fromTextArea(myTextArea, {
        mode: {
          name: 'twig',
          base: 'text/html'
        },
        theme: 'default',
        tabSize: 4,
        indentUnit: 4,
        lineNumbers: true,
        lineWrapping: true,
        styleActiveSelected: true,
        styleActiveLine: true,
        indentWithTabs: true,
        matchBrackets: true,
        highlightMatches: true
      });
    }
  }
};
module.exports = moduleTemplate;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_module-template_js.js.map