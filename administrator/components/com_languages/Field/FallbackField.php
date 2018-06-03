<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Languages\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ContentlanguageField;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Provides a list of fallback languages
 *
 * @since  __DEPLOY_VERSION__
 */
class FallbackField extends ContentlanguageField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public $type = 'Fallback';

	/**
	 * Method to get the field options for fallback languages.
	 *
	 * @return  array  The options the field is going to show.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$options   = parent::getOptions();
		$params    = ComponentHelper::getParams('com_languages');
		$reference = $params->get($this->fieldname);
		$item_lang = $this->form->getData()->get('lang_code');

		foreach ($options as $key => $option)
		{
			if ($option->value === $item_lang)
			{
				unset($options[$key]);
			}
			elseif ($item_lang === $reference && $option->value !== '')
			{
				unset($options[$key]);
			}
		}

		return $options;
	}
}
