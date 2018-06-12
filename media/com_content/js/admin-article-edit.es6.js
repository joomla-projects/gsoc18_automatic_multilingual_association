/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

import jQuery from 'jquery';

(() => {
  'use strict';

  // Get save buttons
  const saveButtons = ['save-group-children-apply', 'save-group-children-save', 'save-group-children-save-new', 'save-group-children-save-copy'];

  document.addEventListener('DOMContentLoaded', () => {
    saveButtons.forEach((buttonId) => {
      const button = document.getElementById(buttonId);
      const task = button.onclick;
      button.removeAttribute('onclick');
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const assocModal = jQuery('#associationAddAssociations');
        assocModal.modal('show');
        assocModal.on('hidden.bs.modal', () => {
          task();
        });
      });
    });
  });
})();
