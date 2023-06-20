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
        let closeButton = document.querySelector(modal.closeButtonSelector);
        if (closeButton) {
            closeButton.addEventListener('mousedown', () => {
                modal.closeModal();
            });
        }
        document.querySelector(modal.modalSelector).addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('wpft-modal')) {
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
        document.querySelector(modal.modalSelector).classList.remove('open')
    },

    /*openModal: function () {
       document.querySelector(modal.modalSelector).classList.add('open')
   },*/

    /* cloneTargetAndOpenModal: function (elements) {
        (elements || []).forEach((elem) => {
            //jQuery(elem).remove();
            jQuery(elem).clone().appendTo(modal.modalContent);
            modal.openModal();
        });
    },*/
}
module.exports = modal;