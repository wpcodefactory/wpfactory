const general = {
    init: function () {

    },
    toggleHeight: function (element) {
        if (!element.style.height || element.style.height == '0px') {
            element.style.height = element.scrollHeight + 'px'
        } else {
            element.style.height = '0px';
        }
    }
}
module.exports = general;