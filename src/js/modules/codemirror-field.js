const codeMirrorField = {
    init: function () {
        setTimeout(function () {
            codeMirrorField.handleCodeMirror()
        }, 1250);
    },
    handleCodeMirror: function () {
        let myTextArea = document.querySelector(".cf-container-carbon_fields_container_template .cf-textarea__input");
        if (myTextArea) {
            let editor = CodeMirror.fromTextArea(myTextArea, {
                mode: {name: 'twig', base: 'text/html'},
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
            });
        }
    }
}
module.exports = codeMirrorField;