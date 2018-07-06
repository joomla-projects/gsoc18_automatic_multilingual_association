/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

import jQuery from 'jquery';

((() => {
  // Get save buttons
  const saveButtons = ['save-group-children-apply', 'save-group-children-save', 'save-group-children-save-new', 'save-group-children-save-copy'];

  document.addEventListener('DOMContentLoaded', () => {
    const associationsEditOptions = Joomla.getOptions('system.associations.edit');
    const formControl = associationsEditOptions.formControl || 'jform';
    const formControlLanguage = document.getElementById(`${formControl}_language`);
    let selectedLanguage = formControlLanguage.value;

    document.querySelector("input[name='itemLanguage']").value = selectedLanguage;
    if (selectedLanguage !== '*') {
      window.overrideSaveButtons(saveButtons, selectedLanguage);
    }

    if (formControlLanguage) {
      formControlLanguage.addEventListener('change', (event) => {
        selectedLanguage = event.target.value;
        document.querySelector("input[name='itemLanguage']").value = selectedLanguage;

        if (selectedLanguage === '*') {
          saveButtons.forEach((buttonId) => {
            const button = document.getElementById(buttonId);
            if (button) {
              if (!button.onclick) {
                button.setAttribute('onclick', button.getAttribute('buttonTask'));
              }
            }
          });
        } else {
          window.overrideSaveButtons(saveButtons, selectedLanguage);
        }
      });
    }
  });

  window.overrideSaveButtons = (buttons, language) => {
    const assocModal = jQuery('#associationAddAssociations');
    const modal = document.getElementById('associationAddAssociations');
    let url;
    let substitution;
    url = modal.getAttribute('data-url');
    substitution = url;
    if (url.search(/&itemLanguage/) !== -1) {
      substitution = substitution.replace(/(&itemLanguage=)[-\w]+$/g, `$1${language}`);
    } else {
      substitution += (`&itemLanguage=${language}`);
    }

    modal.setAttribute('data-url', substitution);

    const ifram = modal.getAttribute('data-iframe');
    url = url.replace(/&/g, '&amp;');
    substitution = substitution.replace(/&/g, '&amp;');
    modal.setAttribute('data-iframe', ifram.replace(url, substitution));

    buttons.forEach((buttonId) => {
      const button = document.getElementById(buttonId);
      if (button) {
        if (button.onclick) {
          button.setAttribute('buttonTask', button.onclick);
          button.removeAttribute('onclick');
        }
        button.addEventListener('click', () => {
          assocModal.modal('show');
          assocModal.on('hidden.bs.modal', () => {
            const buttonTask = new Function(`${button.getAttribute('buttonTask')}onclick();`);
            buttonTask();
          });
        });
      }
    });
  };

  window.fillFields = (languageIds, remember) => {
    const assocLanguages = document.querySelector("input[name='assocLanguages']");
    const decision = document.querySelector("input[name='decision']");
    languageIds.forEach((languageId, index) => {
      if (index === 0) {
        assocLanguages.value = languageId;
      } else {
        assocLanguages.value += `:${languageId}`;
      }
    });
    if (remember) {
      decision.value = 'true';
    } else {
      decision.value = '';
    }
  };

  window.closeModal = () => {
    const assocModal = jQuery('#associationAddAssociations');
    assocModal.modal('hide');
  };
}))();
