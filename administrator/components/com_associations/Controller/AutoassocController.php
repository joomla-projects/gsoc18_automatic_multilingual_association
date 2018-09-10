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

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;

/**
 * Automatically associations controller class.
 *
 * @since  __DEPLOY_VERSION__
 */
class AutoassocController extends FormController
{
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 *
	 * @since  3.7.0
	 */
	protected $view_list = 'autoassoc';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel|bool
	 *
	 * @since  3.7.0
	 */
	public function getModel($name = 'Autoassoc', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Automatically create associations.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function autoCreate()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$model          = $this->getModel();
		$assocLanguages = $this->input->post->get('assocLanguages', '', 'string');
		$langIds        = explode(':', $assocLanguages);
		$recordId       = $this->input->getInt('id');

		// Attempt to create associations.
		if (!$model->autoCreate($langIds))
		{
			// Redirect back to the list screen.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');

			// Save failed, so go back to the list screen and display a notice.
			$this->setRedirect(
				\JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToItemAppend($recordId), false
				)
			);

			return false;
		}

		$this->setMessage(Text::_('COM_ASSOCIATIONS_ASSOCIATIONS_SUCCESSFULLY_CREATED'));

		// Redirect back to the list screen.
		$this->setRedirect(
			\JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToItemAppend($recordId), false
			)
		);

		return true;
	}
}