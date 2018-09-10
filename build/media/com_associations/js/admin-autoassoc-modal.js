/**
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

(function() {
  'use strict';

  document.addEventListener('DOMContentLoaded', function() {
    var applyBtn = document.getElementById('applyBtn');

    applyBtn.addEventListener('click', function(e) {
      e.preventDefault();
      var checkedBoxes = [].slice.call(document.querySelectorAll("td.row-selected input[type='checkbox']"));
      var languageIds = [];

      checkedBoxes.forEach(function(box) {
        languageIds.push(box.value);
      });

      var assocLanguages = document.querySelector("input[name='assocLanguages']");
      languageIds.forEach(function(languageId, index) {
        if (index === 0) {
          assocLanguages.value = languageId;
        } else {
          assocLanguages.value += (':' + languageId);
        }
      });

      Joomla.submitform('autoassoc.autocreate');
    });
  });
})();
