/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
function changeImg(fieldId, flagId) {
  document.getElementById(fieldId).addEventListener('change', function(event) {
    var flagSelectedValue = event.currentTarget.value.toLowerCase().replace('-', '_');
    var flagimage = document.getElementById(flagId).querySelector('img');
    var src = Joomla.getOptions('system.paths').rootFull + '/media/mod_languages/images/' + flagSelectedValue + '.gif';

    if (flagSelectedValue) {
      flagimage.setAttribute('src', src);
      flagimage.setAttribute('alt', flagSelectedValue);
    } else {
      flagimage.removeAttribute('src');
      flagimage.setAttribute('alt', '');
    }
  }, false);
}

document.addEventListener('DOMContentLoaded', function () {
  changeImg('jform_image', 'flag');
  changeImg('jform_fallback_lang', 'fallback_lang_flag');
});
