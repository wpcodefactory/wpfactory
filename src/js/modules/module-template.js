const moduleTemplate = {
    init: function () {


        setTimeout(function () {
            moduleTemplate.handleCodeMirror()
        }, 1000);

    },
    handleCodeMirror: function () {
        let myTextArea = document.querySelector(".cf-container-carbon_fields_container_template .cf-textarea__input");
        if (myTextArea) {
            CodeMirror.fromTextArea(myTextArea, {
                mode: {name: 'twig', base: 'text/html'},
                theme: 'default',
                tabSize: 4,
                lineNumbers: true,
                styleActiveSelected: true,
                styleActiveLine: true,
                indentWithTabs: true,
                matchBrackets: true,
                highlightMatches: true,
                gutters:'test'
            });
        }


    }
}
module.exports = moduleTemplate;