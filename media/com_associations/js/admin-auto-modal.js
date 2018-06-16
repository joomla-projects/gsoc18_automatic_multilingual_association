/**
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

(function() {
  "use strict";

  Joomla.gatherCheckedBoxes = function() {
    var checkedBoxes = document.querySelectorAll("td.row-selected input[type='checkbox']");
    var values = [];

    checkedBoxes.forEach(function(box) {
      values.push(box.value);
    });

    var functionName = 'fillAssocLanguagesField';

    window.parent[functionName](values);
    window.parent['closeModal']();
  };

  document.addEventListener('DOMContentLoaded', function() {
    var applyBtn = document.getElementById('applyBtn');
    applyBtn.addEventListener('click', function(e) {
      e.preventDefault();
      Joomla.gatherCheckedBoxes();
    })
  });
})();