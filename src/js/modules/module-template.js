const moduleTemplate = {
    init: function () {


        setTimeout(function () {
            moduleTemplate.handleCodeMirror()
        }, 1000);

    },
    handleCodeMirror: function () {
        let myTextArea = document.querySelector(".cf-container-carbon_fields_container_template .cf-textarea__input");
        if (myTextArea) {
            var editor = CodeMirror.fromTextArea(myTextArea, {
                mode: {name: 'twig', base: 'text/html'},
                theme: 'default',
                tabSize: 4,
                indentUnit: 4,
                lineNumbers: true,
                lineWrapping:true,
                styleActiveSelected: true,
                styleActiveLine: true,
                indentWithTabs: true,
                matchBrackets: true,
                highlightMatches: true,
            });
        }


    }
}
module.exports = moduleTemplate;