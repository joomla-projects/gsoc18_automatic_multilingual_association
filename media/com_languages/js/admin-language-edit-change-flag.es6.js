/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
function changeImg(fieldId, flagId) {
  document.getElementById(fieldId).addEventListener('change', (event) => {
    const flagSelectedValue = event.currentTarget.value.toLowerCase().replace('-', '_');
    const flagimage = document.getElementById(flagId).querySelector('img');
    const src = `${Joomla.getOptions('system.paths').rootFull}/media/mod_languages/images/${flagSelectedValue}.gif`;

    if (flagSelectedValue) {
      flagimage.setAttribute('src', src);
      flagimage.setAttribute('alt', flagSelectedValue);
    } else {
      flagimage.removeAttribute('src');
      flagimage.setAttribute('alt', '');
    }
  }, false);
}

document.addEventListener('DOMContentLoaded', () => {
  changeImg('jform_image', 'flag');
  changeImg('jform_fallback_lang', 'fallback_lang_flag');
});
