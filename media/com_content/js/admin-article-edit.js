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
  var saveButtons = ['save-group-children-apply', 'save-group-children-save', 'save-group-children-save-new', 'save-group-children-save-copy'];

  document.addEventListener('DOMContentLoaded', function () {
    var associationsEditOptions = Joomla.getOptions('system.associations.edit'), formControl = associationsEditOptions.formControl || 'jform',
      formControlLanguage     = document.getElementById(formControl + '_language');
    var selectedLanguage = formControlLanguage.value;
    var remember = document.querySelector("input[name='remember']");

    if (remember.value === '0') {
      document.querySelector("input[name='itemLanguage']").value = selectedLanguage;
      if (selectedLanguage !== '*') {
        window.overrideSaveButtons(saveButtons, selectedLanguage);
      }

      if (formControlLanguage) {
        formControlLanguage.addEventListener('change', function(event) {
          selectedLanguage = event.target.value;
          document.querySelector("input[name='itemLanguage']").value = selectedLanguage;

          if (selectedLanguage === '*') {
            saveButtons.forEach(function(buttonId) {
              var button = document.getElementById(buttonId);
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
    }
  });

  window.overrideSaveButtons = function(buttons, language) {
    var assocModal = $('#associationAddAssociations');
    var modal = document.getElementById('associationAddAssociations');
    var url, substitution;
    url = substitution = modal.getAttribute('data-url');
    if (url.search(/&itemLanguage/) !== -1) {
      substitution = substitution.replace(/(&itemLanguage=)[-\w]+$/g, "$1" + language);
    } else {
      substitution += ('&itemLanguage=' + language);
    }

    modal.setAttribute('data-url', substitution);

    var ifram = modal.getAttribute('data-iframe');
    url = url.replace(/&/g, '&amp;');
    substitution = substitution.replace(/&/g, '&amp;');
    modal.setAttribute('data-iframe', ifram.replace(url, substitution));

    buttons.forEach(function(buttonId) {
      var button = document.getElementById(buttonId);
      if (button) {
        if (button.onclick) {
          button.setAttribute('buttonTask', button.onclick);
          button.removeAttribute('onclick');
        }
        button.addEventListener('click', function() {
          assocModal.modal('show');
          assocModal.on('hidden.bs.modal', function() {
            var buttonTask = new Function(button.getAttribute('buttonTask') + 'onclick();');
            buttonTask();
          });
        });
      }
    });
  };

  window.fillFields = function(languageIds, rememberDecision) {
    var assocLanguages = document.querySelector("input[name='assocLanguages']");
    var remember = document.querySelector("input[name='remember']");
    var decision = document.querySelector("input[name='decision']");
    languageIds.forEach(function(languageId, index) {
      if (index === 0) {
        assocLanguages.value = languageId;
        if (rememberDecision) {
          decision.value = languageId;
        }
      } else {
        assocLanguages.value += ':' + languageId;
        if (rememberDecision) {
          decision.value += ':' + languageId;
        }
      }
    });
    if (rememberDecision) {
      remember.value = '1';
    } else {
      decision.value = '';
      remember.value = '0';
    }
  };

  window.closeModal = function() {
    var assocModal = $('#associationAddAssociations');
    assocModal.modal('hide');
  };
})();