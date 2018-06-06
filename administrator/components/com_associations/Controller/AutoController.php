<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Associations\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;

/**
 * Automatically creating associations controller class.
 *
 * @since  __DEPLOY_VERSION__
 */
class AutoController extends FormController
{
	/**
	 * Method to automatically create associated articles.
	 *
	 * @param   BaseDatabaseModel   $model  The model of the component being processed.
	 *
	 * @return  boolean True is successful, false otherwise and internal error is set.
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function autocreate($model = null)
	{
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		if (!Associations::isEnabled())
		{
			// @TODO Add Error Messages
			$this->setMessage(Text::_(''));

			return false;
		}

		// Set the model
		$model = $this->getModel('Auto', 'Administrator', array());

		// Preset the redirect
		$this->setRedirect(\JRoute::_('index.php?option=com_content&view=articles' . $this->getRedirectToListAppend(), false));

		return parent::autocreate($model);
	}
}