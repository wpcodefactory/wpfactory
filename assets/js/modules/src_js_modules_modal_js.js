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
    document.addEventListener("click", function (event) {
      if (event.target.matches(modal.closeButtonSelector) || event.target.matches(modal.modalSelector)) {
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
    //console.log(document.querySelector(modal.modalSelector));
    (document.querySelectorAll(modal.modalSelector) || []).forEach(function (element) {
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
};

module.exports = modal;

/***/ })

}]);
//# sourceMappingURL=src_js_modules_modal_js.js.map