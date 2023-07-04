const CSSClassManager = {
    init: function () {
        document.addEventListener("click", this.manageClasses);
        document.addEventListener("mouseover", this.manageClasses);
        document.addEventListener("mouseleave", this.manageClasses,true);
    },
    manageClasses: function (event) {

        if (event.target.classList && event.target.classList.contains("css-modifier")) {
            if(event.type==='mouseout'){
                console.log(event.target)
            }
            let cssClassesStr = event.target.dataset.classes ?? 'active';
            let cssClasses = cssClassesStr.split(",");
            let gettersSelector = event.target.dataset.classGetters;
            let losersSelector = event.target.dataset.classLosers;
            let addEvent = event.target.dataset.addEvent ?? 'click';
            let removeEvent = event.target.dataset.removeEvent ?? 'click';


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
    changeClasses: function (args) {
        let toggle = false;
        let selector = args.selector;
        let cssClasses = args.cssClasses;
        let action = args.action;
        //alert('555');
        document.querySelectorAll(selector).forEach(element => {
            //window[method_prefix + method_name](arg1, arg2);
            element.classList[action](...cssClasses);

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
    },

}
module.exports = CSSClassManager;