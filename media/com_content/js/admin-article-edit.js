/**
 * PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
 * OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
 **/

/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function() {
  'use strict';

  // Get save buttons
  var saveButtons = ['save-group-children-apply', 'save-group-children-save', 'save-group-children-save-new'];

  document.addEventListener('DOMContentLoaded', function () {
    saveButtons.forEach(function(buttonId) {
      var button = document.getElementById(buttonId);
      var task = button.onclick;
      var assocModal = $('#associationAddAssociations');

      button.removeAttribute('onclick');
      button.addEventListener('click', function(e) {
        e.preventDefault();
        assocModal.modal('show');
        assocModal.on('hidden.bs.modal', function() {
          task();
        });
      });
    });
  });

  window.fillAssocLanguagesField = function(languageIds) {
    var assocLanguages = document.querySelector("input[name='assocLanguages']");

    languageIds.forEach(function(languageId, index) {
      if (index === 0) {
        assocLanguages.value = languageId;
      } else {
        assocLanguages.value += ':' + languageId;
      }
    });
  };

  window.closeModal = function() {
    var assocModal = $('#associationAddAssociations');
    assocModal.modal('hide');
  }
})();