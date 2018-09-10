/**
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
Joomla = window.Joomla || {};

((Joomla, document) => {
  'use strict';

  const onClick = (event) => {
    event.preventDefault();
    const checkedBoxes = [].slice.call(document.querySelectorAll("td.row-selected input[type='checkbox']"));
    const assocLanguages = document.querySelector("input[name='assocLanguages']");
    const languageIds = [];

    if (checkedBoxes.length) {
      checkedBoxes.forEach((box) => {
        languageIds.push(box.value);
      });
    }

    if (languageIds.length) {
      languageIds.forEach((languageId, index) => {
        if (index === 0) {
          assocLanguages.value = languageId;
        } else {
          assocLanguages.value += (`:${languageId}`);
        }
      });
    }

    Joomla.submitform('autoassoc.autocreate');
  };

  const onBoot = () => {
    const applyBtn = document.getElementById('applyBtn');

    if (applyBtn) {
      applyBtn.addEventListener('click', onClick);
    }

    // Cleanup
    document.removeEventListener('DOMContentLoaded', onBoot, true);
  };

  document.addEventListener('DOMContentLoaded', onBoot, true);
})(window.Joomla);
