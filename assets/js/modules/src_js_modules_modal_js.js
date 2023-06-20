(self["webpackChunk"] = self["webpackChunk"] || []).push([["src_js_modules_modal_js"],{

/***/ "./src/js/modules/modal.js":
/*!*********************************!*\
  !*** ./src/js/modules/modal.js ***!
  \*********************************/
/***/ ((module) => {

//const general = require("./general");
var modal = {
  modalSelector: '.wpft-modal',
  modalContent: '.wpft-modal-content',
  closeButtonSelector: '.wpft-modal-close-btn',
  init: function init() {
    this.handleModalClose();
    this.handleTriggers();
  },
  handleModalClose: function handleModalClose() {
    var closeButton = document.querySelector(modal.closeButtonSelector);
    if (closeButton) {
      closeButton.addEventListener('mousedown', function () {
        modal.closeModal();
      });
    }
    document.querySelector(modal.modalSelector).addEventListener('mousedown', function (e) {
      if (e.target.classList.contains('wpft-modal')) {
        modal.closeModal();
      }
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        // Escape key
        modal.closeModal();
      }
    });
  },
  openTargetModal: function openTargetModal(elements) {
    (elements || []).forEach(function (elem) {
      elem.classList.add('open');
    });
  },
  handleTriggers: function handleTriggers() {
    var _this = this;
    // Add a click event on buttons to open a specific modal
    (document.querySelectorAll('.js-modal-trigger') || []).forEach(function (trigger) {
      var modal = trigger.dataset.target;
      var target = document.querySelectorAll(modal);
      trigger.addEventListener('click', function () {
        _this.openTargetModal(target);
      });
    });
  },
  closeModal: function closeModal() {
    document.querySelector(modal.modalSelector).classList.remove('open');
  }

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
};

module.exports = modal;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_modal_js.js.map