<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Associations\Administrator\View\Autoassoc;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Associations;
use Joomla\Component\Associations\Administrator\Helper\AssociationsHelper;
use Joomla\Registry\Registry;

/**
 * View class for automatic associations
 *
 * @since  __DEPLOY_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var   array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    \Joomla\CMS\Pagination\Pagination
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    object
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $state;

	/**
	 * Selected item type properties.
	 *
	 * @var    Registry
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public $itemType = null;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');

		if (!Associations::isEnabled())
		{
			$link = \JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . AssociationsHelper::getLanguagefilterPluginId());
			Factory::getApplication()->enqueueMessage(\JText::sprintf('COM_ASSOCIATIONS_ERROR_NO_ASSOC', $link), 'warning');
		}
		else
		{
			$type = null;

			list($extensionName, $typeName) = explode('.', $this->state->get('itemtype'), 2);

			$extension = AssociationsHelper::getSupportedExtension($extensionName);

			$types = $extension->get('types');

			if (array_key_exists($typeName, $types))
			{
				$type = $types[$typeName];
			}

			$this->itemType = $type;

			if (is_null($type))
			{
				Factory::getApplication()->enqueueMessage(\JText::_('COM_ASSOCIATIONS_ERROR_NO_TYPE'), 'warning');
			}
			else
			{
				$this->extensionName = $extensionName;
				$this->typeName      = $typeName;
				$this->typeSupports  = array();
				$this->typeFields    = array();

				$details = $type->get('details');

				if (array_key_exists('support', $details))
				{
					$support = $details['support'];
					$this->typeSupports = $support;
				}

				if (array_key_exists('fields', $details))
				{
					$fields = $details['fields'];
					$this->typeFields = $fields;
				}

				$this->items      = $this->get('Items');
				$this->pagination = $this->get('Pagination');
			}

			// Check for errors.
			if (count($errors = $this->get('Errors')))
			{
				throw new \Exception(implode("\n", $errors), 500);
			}

			parent::display($tpl);
		}
	}
}
