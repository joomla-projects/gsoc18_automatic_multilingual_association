(function() {
  'use strict';

  // Get save buttons
  var saveButtons = ['save-group-children-apply', 'save-group-children-save', 'save-group-children-save-new', 'save-group-children-save-copy'];

  document.addEventListener('DOMContentLoaded', function () {
    saveButtons.forEach(function(buttonId) {
      var button = document.getElementById(buttonId);
      var task = button.onclick;
      button.removeAttribute('onclick');
      button.addEventListener('click', function(e) {
        // e.preventDefault();
        var assocModal = document.getElementById('associationAddAssociations');
        $('#associationAddAssociations').modal('show');

        $('#associationAddAssociations').on('hidden.bs.modal', function(e) {
          task();
        });
      });
    });
  });
})();