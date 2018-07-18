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
use Joomla\CMS\Application\ApplicationHelper;

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
		$options         = parent::getOptions();
		$params          = ComponentHelper::getParams('com_languages');
		$defaultLanguage = $params->get(ApplicationHelper::getClientInfo(0)->name, 'en-GB');
		$ignoredLanguage = $this->form->getData()->get('lang_code');

		foreach ($options as $key => $option)
		{
			if ($option->value === $defaultLanguage)
			{
				$option->text = 'Reference [' . $option->text . ']';
			}

			if ($option->value === $ignoredLanguage)
			{
				unset($options[$key]);
			}
		}

		return $options;
	}
}
