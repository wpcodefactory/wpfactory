(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_codemirror-field_js"],{

/***/ "./src/js/modules/codemirror-field.js":
/*!********************************************!*\
  !*** ./src/js/modules/codemirror-field.js ***!
  \********************************************/
/***/ ((module) => {

var codeMirrorField = {
  init: function init() {
    setTimeout(function () {
      codeMirrorField.handleCodeMirror();
    }, 1250);
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
        highlightMatches: true,
        firstLineNumber: 20
      });
      editor.setSize(null, 500);
    }
  }
};
module.exports = codeMirrorField;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_codemirror-field_js.js.map