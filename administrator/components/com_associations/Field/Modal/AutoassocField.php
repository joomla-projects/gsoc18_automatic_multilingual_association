<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Associations\Administrator\Field\Modal;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Supports a modal item picker.
 *
 * @since  __DEPLOY_VERSION__
 */
class AutoassocField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   __DEPLOY_VERSION__
	 */
	protected $type = 'Modal_Autoassoc';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getInput()
	{
		Factory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);

		// The active category id field.
		$value = (int) $this->value > 0 ? (int) $this->value : '';

		// Get the active language id, language code and extension name.
		$langId    = $this->element['lang_id'];
		$language  = $this->element['language'];
		$extension = $this->element['extension'];

		// Create the modal id according to the language.
		$modalId 	= 'Category_' . $this->id;

		$catName    = 'CategoryValue_' . $langId;
		$modalTitle = Text::_('COM_CATEGORIES_CHANGE_CATEGORY');
		$title   	= Text::_('COM_CATEGORIES_SELECT_A_CATEGORY');

		$linkCategories = 'index.php?option=com_categories&amp;view=categories&amp;layout=modal'
			. '&amp;tmpl=component&amp;' . Session::getFormToken() . '=1&amp;extension='
			. $extension . '&amp;forcedLanguage=' . $language;
		$linkCategory   = 'index.php?option=com_categories&amp;view=category&amp;layout=modal'
			. '&amp;tmpl=component&amp;' . Session::getFormToken() . '=1&amp;extension='
			. $extension . '&amp;forcedLanguage=' . $language;

		$urlSelect = $linkCategories . '&amp;function=jSelectCategory_' . $this->id;
		$urlNew    = $linkCategory . '&amp;task=category.add';
		$urlEdit   = $linkCategory . '&amp;task=category.edit&amp;id=\' + document.getElementById("'
			. $this->id . '_id").value + \'';

		if (!isset($scriptSelect[$this->id]))
		{
			Factory::getDocument()->addScriptDeclaration("
			function jSelectCategory_" . $this->id . "(id, title, object) {
				window.processModalSelect('Category', '" . $this->id . "', id, title, '', object);
			}"
			);

			$scriptSelect[$this->id] = true;
		}

			$html = '<span class="input-group"><input class="form-control" id="' . $this->id
			. '_name" type="text" value="' . $title . '" disabled="disabled" size="35">'
			. '<span class="input-group-append">'
			. '<a'
			. ' class="btn btn-primary hasTooltip' . ($value ? ' sr-only' : '') . '"'
			. ' id="' . $this->id . '_select"'
			. ' data-toggle="modal"'
			. ' role="button"'
			. ' href="#ModalSelect' . $modalId . '"'
			. ' title="' . HTMLHelper::tooltipText('COM_CATEGORIES_CHANGE_CATEGORY') . '">'
			. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
			. '</a>'
			. '<a'
			. ' class="btn btn-secondary hasTooltip' . ($value ? ' sr-only' : '') . '"'
			. ' id="' . $this->id . '_new"'
			. ' data-toggle="modal"'
			. ' role="button"'
			. ' href="#ModalNew' . $modalId . '"'
			. ' title="' . HTMLHelper::tooltipText('COM_CATEGORIES_NEW_CATEGORY') . '">'
			. '<span class="icon-new" aria-hidden="true"></span> ' . Text::_('JACTION_CREATE')
			. '</a>'
			. '<a'
			. ' class="btn btn-secondary hasTooltip' . ($value ? '' : ' sr-only') . '"'
			. ' id="' . $this->id . '_edit"'
			. ' data-toggle="modal"'
			. ' role="button"'
			. ' href="#ModalEdit' . $modalId . '"'
			. ' title="' . HTMLHelper::tooltipText('COM_CATEGORIES_EDIT_CATEGORY') . '">'
			. '<span class="icon-edit" aria-hidden="true"></span> ' . Text::_('JACTION_EDIT')
			. '</a>'
			. '<a'
			. ' class="btn btn-secondary' . ($value ? '' : ' sr-only') . '"'
			. ' id="' . $this->id . '_clear"'
			. ' href="#"'
			. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
			. '<span class="icon-remove" aria-hidden="true"></span>' . Text::_('JCLEAR')
			. '</a></span></span>';

		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			'ModalSelect' . $modalId,
			array(
				'title'      => $modalTitle,
				'url'        => $urlSelect,
				'height'     => '400px',
				'width'      => '800px',
				'bodyHeight' => 70,
				'modalWidth' => 80,
				'footer'     => '<a role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
					. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
			)
		);

		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			'ModalNew' . $modalId,
			array(
				'title'       => Text::_('COM_CATEGORIES_NEW_CATEGORY'),
				'backdrop'    => 'static',
				'keyboard'    => false,
				'closeButton' => false,
				'url'         => $urlNew,
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => 70,
				'modalWidth'  => 80,
				'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'cancel\', \'item-form\'); return false;">'
					. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
					. '<a role="button" class="btn btn-primary" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'save\', \'item-form\'); return false;">'
					. Text::_('JSAVE') . '</a>'
					. '<a role="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'apply\', \'item-form\'); return false;">'
					. Text::_('JAPPLY') . '</a>',
			)
		);

		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			'ModalEdit' . $modalId,
			array(
				'title'       => Text::_('COM_CATEGORIES_EDIT_CATEGORY'),
				'backdrop'    => 'static',
				'keyboard'    => false,
				'closeButton' => false,
				'url'         => $urlEdit,
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => 70,
				'modalWidth'  => 80,
				'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'cancel\', \'item-form\'); return false;">'
					. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
					. '<a role="button" class="btn btn-primary" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'save\', \'item-form\'); return false;">'
					. Text::_('JSAVE') . '</a>'
					. '<a role="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'apply\', \'item-form\'); return false;">'
					. Text::_('JAPPLY') . '</a>',
			)
		);

		$html .= '<input type="hidden" id="' . $this->id . '_id" class="required modal-value" data-required="true"'
			. ' name="' . $catName . '" data-text="'
			. htmlspecialchars(Text::_('COM_CATEGORIES_SELECT_A_CATEGORY', true), ENT_COMPAT, 'UTF-8')
			. '" value="' . $value . '">';

		return $html;
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getLabel()
	{
		return '';
	}
}
