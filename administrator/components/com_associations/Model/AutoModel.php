<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Associations\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Component\Associations\Administrator\Helper\AssociationsHelper;

/**
 * Methods supporting a list of article records.
 *
 * @since __DEPLOY_VERSION__
 */
class AutoModel extends ListModel
{
	/**
	 * Override parent constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * @param   MVCFactoryInterface  $factory  The factory.
	 *
	 * @see     \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		// @TODO change filter fields.
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'title',
				'ordering',
				'itemtype',
				'language',
				'menutype',
				'menutype_title',
				'state',
				'category_id',
				'category_title',
				'access',
				'access_level',
			);
		}

		parent::__construct($config, $factory);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function populateState($ordering = 'ordering', $direction = 'asc')
	{
		$app = \JFactory::getApplication();

		$forcedLanguage = $app->input->get('forcedLanguage', '', 'cmd');

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Adjust the context to support forced languages.
		if ($forcedLanguage)
		{
			$this->context .= '.' . $forcedLanguage;
		}

		$this->setState('itemtype', $this->getUserStateFromRequest($this->context . '.itemtype', 'itemtype', '', 'string'));
		$this->setState('referenceId', $this->getUserStateFromRequest($this->context . '.id', 'id', 0, 'int'));

		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		$this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'cmd'));
		$this->setState(
			'filter.category_id', $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '', 'cmd')
		);
		$this->setState('filter.menutype', $this->getUserStateFromRequest($this->context . '.filter.menutype', 'filter_menutype', '', 'string'));
		$this->setState('filter.access', $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'string'));
		$this->setState('filter.level', $this->getUserStateFromRequest($this->context . '.filter.level', 'filter_level', '', 'cmd'));

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('itemtype');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.menutype');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.level');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery|boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getListQuery()
	{
		$type = null;

		list($extensionName, $typeName) = explode('.', $this->state->get('itemtype'), 2);

		$extension   = AssociationsHelper::getSupportedExtension($extensionName);
		$types       = $extension->get('types');

		if (array_key_exists($typeName, $types))
		{
			$type = $types[$typeName];
		}

		if (is_null($type))
		{
			return false;
		}

		// Create a new query object.
		$db       = $this->getDbo();
		$query    = $db->getQuery(true);

		$details = $type->get('details');

		if (!array_key_exists('support', $details))
		{
			return false;
		}

		$support = $details['support'];

		if (!array_key_exists('fields', $details))
		{
			return false;
		}

		$fields = $details['fields'];

		$tablename    = $details['tables']['a'];
		$referenceId  = $this->state->get('referenceId');
		$context      = $extensionName . '.item';
		$pk           = explode('.', $fields['id'])[1];
		$titleField   = explode('.', $fields['title'])[1];
		$langField    = explode('.', $fields['language'])[1];
		$associations = AssociationsHelper::getAssociationList($extensionName, $typeName, $referenceId);

		if ($typeName === 'category')
		{
			$context = 'com_categories.item';
		}

		$tmpQuery = $db->getQuery(true);

		$tmpQuery->select($db->quoteName('c.' . $langField))
			->from($db->quoteName($tablename, 'c'))
			->where($db->quoteName('c.' . $pk) . ' = ' . (int) $referenceId);

		$db->setQuery($tmpQuery);
		$ignored = $db->loadResult();

		if (!empty($associations))
		{
			$categoriesExtraSql = (($tablename === '#__categories') ? ' AND c2.extension = ' . $db->quote($extensionName) : '');

			$query->select($db->quoteName('c2.' . $pk, 'item_id'))
				->select($db->quoteName('c2.' . $titleField, 'item_title'))
				->from($db->quoteName($tablename, 'c'))
				->join('INNER', $db->quoteName('#__associations', 'a') . ' ON (a.id = c.' . $db->quoteName($pk)
					. ' AND a.context =' . $db->quote($context) . ' AND c.' . $db->quoteName($pk) . ' = ' . (int) $referenceId . ')'
				)
				->join('INNER', $db->quoteName('#__associations', 'a2') . ' ON a.key = a2.key')
				->join('INNER', $db->quoteName($tablename, 'c2') . ' ON (a2.id = c2.' . $db->quoteName($pk) . ' AND c2.' . $db->quoteName($pk)
					. ' != ' . $db->quote($referenceId) . $categoriesExtraSql . ')'
				);

			$query->select($db->quoteName('l.lang_id', 'lang_id'))
				->select($db->quoteName('l.lang_code', 'language'))
				->select($db->quoteName('l.published', 'published'))
				->select($db->quoteName('l.title', 'language_title'))
				->select($db->quoteName('l.image', 'language_image'))
				->join(
					'RIGHT', $db->quoteName('#__languages', 'l') . ' ON (' . $db->quoteName('c2.' . $langField)
					. ' = ' . $db->quoteName('l.lang_code') . ' AND c.' . $db->quoteName($langField) . ' != l.lang_code)'
				)
				->where('l.lang_code != ' . $db->quote($ignored));

			// Use alias field ?
			if (!empty($fields['alias']))
			{
				$aliasField = explode('.', $fields['alias'])[1];

				$query->select($db->quoteName('c2.' . $aliasField, 'alias'));
			}

			// Use catid field ?
			if (!empty($fields['catid']))
			{
				$catField = explode('.', $fields['catid'])[1];

				$query->join('LEFT', $db->quoteName('#__categories', 'ca')
					. ' ON ' . $db->quoteName('c2.' . $catField) . ' = ca.id AND ca.extension = ' . $db->quote($extensionName)
				)
					->select($db->quoteName('ca.alias', 'category'));
			}

			// If component item type supports menu type, select the menu type also.
			if (!empty($fields['menutype']))
			{
				$menutypeField = explode('.', $fields['menutype'])[1];

				$query->select($db->quoteName('mt.title', 'menutype_title'))
					->join(
						'LEFT', $db->quoteName('#__menu_types', 'mt') . ' ON ' . $db->quoteName('mt.menutype')
						. ' = ' . $db->quoteName('c2.' . $menutypeField)
					);
			}

			if ($tablename === '#__categories')
			{
				$query->where($db->quoteName('c.extension') . ' = ' . $db->quote($extensionName));
			}
		}
		else
		{
			$query->select($db->quoteName('l.lang_id', 'lang_id'))
				->select($db->quoteName('l.lang_code', 'language'))
				->select($db->quoteName('l.published', 'published'))
				->select($db->quoteName('l.title', 'language_title'))
				->select($db->quoteName('l.image', 'language_image'))
				->from($db->quoteName('#__languages', 'l'))
				->where($db->quoteName('l.lang_code') . ' != ' . $db->quote($ignored));
		}

		return $query;
	}
}
