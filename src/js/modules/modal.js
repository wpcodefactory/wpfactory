//const general = require("./general");
const modal = {
    modalSelector: '.wpft-modal',
    modalContent: '.wpft-modal-content',
    closeButtonSelector: '.wpft-modal-close-btn',

    init: function () {
        this.handleModalClose();
        this.handleTriggers();
    },

    handleModalClose: function () {
        document.addEventListener("click", function (event) {
            if (
                event.target.matches(modal.closeButtonSelector) ||
                event.target.matches(modal.modalSelector)
            ) {
                modal.closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') { // Escape key
                modal.closeModal();
            }
        });
    },

    openTargetModal: function (elements) {
        (elements || []).forEach((elem) => {
            elem.classList.add('open');
        });
    },

    handleTriggers: function () {
        // Add a click event on buttons to open a specific modal
        (document.querySelectorAll('.js-modal-trigger') || []).forEach((trigger) => {
            const modal = trigger.dataset.target;
            const target = document.querySelectorAll(modal);
            trigger.addEventListener('click', () => {
                this.openTargetModal(target);
            });
        });
    },

    closeModal: function () {
        //console.log(document.querySelector(modal.modalSelector));
        (document.querySelectorAll(modal.modalSelector) || []).forEach((element) => {
            /*const modal = trigger.dataset.target;
            const target = document.querySelectorAll(modal);
            trigger.addEventListener('click', () => {
                this.openTargetModal(target);
            });*/
            //console.log(element);
            element.classList.remove('open');
        });
        //document.querySelector(modal.modalSelector).classList.remove('open')
    }
}
module.exports = modal;