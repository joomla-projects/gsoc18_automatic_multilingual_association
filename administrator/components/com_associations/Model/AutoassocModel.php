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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseQuery;
use Joomla\Component\Associations\Administrator\Helper\AssociationsHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\LanguageHelper;

/**
 * Methods supporting a list of article records.
 *
 * @since __DEPLOY_VERSION__
 */
class AutoassocModel extends ListModel
{
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
		$app = Factory::getApplication();

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
		$this->setState('itemId', $this->getUserStateFromRequest($this->context . '.id', 'id', 0, 'int'));
		$this->setState('forcedLanguage', $forcedLanguage);

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

		// Get extension name and type name.
		list($extensionName, $typeName) = explode('.', $this->state->get('itemtype'), 2);

		$extension = AssociationsHelper::getSupportedExtension($extensionName);
		$types = $extension->get('types');

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

		if (!array_key_exists('fields', $details))
		{
			return false;
		}

		$fields = $details['fields'];

		$tablename    = $details['tables']['a'];
		$itemId       = $this->state->get('itemId');
		$context      = $extensionName . '.item';
		$pk           = explode('.', $fields['id'])[1];
		$titleField   = explode('.', $fields['title'])[1];
		$langField    = explode('.', $fields['language'])[1];
		$associations = AssociationsHelper::getAssociationList($extensionName, $typeName, $itemId);

		if ($typeName === 'category')
		{
			$context = 'com_categories.item';
		}

		$tmpQuery = $db->getQuery(true);

		$tmpQuery->select($db->quoteName('c.' . $langField))
			->from($db->quoteName($tablename, 'c'))
			->where($db->quoteName('c.' . $pk) . ' = ' . (int) $itemId);

		$db->setQuery($tmpQuery);
		$ignored = $db->loadResult();

		if (!empty($associations))
		{
			$categoriesExtraSql = (($tablename === '#__categories') ? ' AND c2.extension = ' . $db->quote($extensionName) : '');

			$query->select($db->quoteName('c2.' . $pk, 'item_id'))
				->select($db->quoteName('c2.' . $titleField, 'item_title'))
				->from($db->quoteName($tablename, 'c'))
				->join('INNER', $db->quoteName('#__associations', 'a') . ' ON (a.id = c.' . $db->quoteName($pk)
					. ' AND a.context =' . $db->quote($context) . ' AND c.' . $db->quoteName($pk) . ' = ' . (int) $itemId . ')'
				)
				->join('INNER', $db->quoteName('#__associations', 'a2') . ' ON a.key = a2.key')
				->join('INNER', $db->quoteName($tablename, 'c2') . ' ON (a2.id = c2.' . $db->quoteName($pk) . ' AND c2.' . $db->quoteName($pk)
					. ' != ' . $db->quote($itemId) . $categoriesExtraSql . ')'
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

			// Use catid field?
			if (!empty($fields['catid']))
			{
				$catField = explode('.', $fields['catid'])[1];

				// Join over the categories.
				$query->join('LEFT', $db->quoteName('#__categories', 'ca')
					. ' ON ' . $db->quoteName('c2.' . $catField) . ' = ca.id AND ca.extension = ' . $db->quote($extensionName)
				)
					->select($db->quoteName('ca.title', 'category'))
					->select($db->quoteName('ca.id', 'catid'));
			}

			// Use menutype field?
			if (!empty($fields['menutype']))
			{
				$menuField = explode('.', $fields['menutype'])[1];

				// Join over the menu types.
				$query->join('LEFT', $db->quoteName('#__menu_types', 'mt') . ' ON '
					. $db->quoteName('c2.' . $menuField) . ' = ' . $db->quoteName('mt.menutype'))
					->select($db->quoteName('mt.title', 'menutype'));

				$query->join('LEFT', $db->quoteName('#__menu', 'm') . ' ON '
					. $db->quoteName('m.id') . ' = ' . $db->quoteName('c2.id'))
					->select($db->quoteName('m.title', 'parent'));
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

	/**
	 * Method to automatically create associations of an item in chosen languages.
	 *
	 * @param   array  $langIds     An array of language ids.
	 *
	 * @return  boolean Return true on success, false on failure.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function autoCreate($langIds)
	{
		$app = Factory::getApplication();

		// Get extension name and type name.
		list($extensionName, $typeName) = explode('.', $app->input->get('itemtype'), 2);

		$extension = AssociationsHelper::getSupportedExtension($extensionName);
		$types     = $extension->get('types');
		$itemId    = $app->input->get('id');

		if (array_key_exists($typeName, $types))
		{
			$type = $types[$typeName];
		}

		if (is_null($type))
		{
			return false;
		}

		// Get details and table of the type
		$details            = $type->get('details');
		$table              = $type->get('table');
		$associationContext = $type->get('associationContext');

		if (array_key_exists('fields', $details))
		{
			$fields = $details['fields'];
		}

		if (!array_key_exists('fields', $details))
		{
			return false;
		}

		// Sanitize ids.
		$langIds = array_unique($langIds);
		$langIds = ArrayHelper::toInteger($langIds);

		// Remove any values of zero.
		if (array_search(0, $langIds, true) !== false)
		{
			unset($langIds[array_search(0, $langIds, true)]);
		}

		// If no languages is selected.
		if (empty($langIds))
		{
			$this->setError(Text::_('COM_ASSOCIATIONS_NO_ITEM_SELECTED'));

			return false;
		}

		// Get associations.
		$associations = AssociationsHelper::getAssociationList($extensionName, $typeName, $itemId);

		// Get language table
		$languageTable = Table::getInstance('Language', 'Joomla\\CMS\\Table\\');

		// Set a flag to find whether associations are changed.
		$assocChanged = false;

		while (!empty($langIds))
		{
			// Pop the first ID off the stack.
			$pk = array_shift($langIds);

			$table->reset();
			$languageTable->reset();

			if (!$languageTable->load($pk) || !$table->load($itemId))
			{
				if ($error = $languageTable->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}

				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
			}

			// Get current language
			$langCode = $languageTable->lang_code;

			// Get item language
			if (!isset($itemLang))
			{
				$itemLang = $table->language;
			}

			// If the item doesn't have associations in current language
			if (!isset($associations[$langCode]))
			{
				// Alter the title & alias
				$title          = explode('.', $fields['title'], 2)[1];
				$alias          = explode('.', $fields['alias'], 2)[1];
				$table->$title .= ' [' . $langCode . ']';
				$table->$alias .= ' ' . $langCode;

				// Alter the language
				$table->language = $langCode;

				// Reset the ID
				$table->id = 0;

				// Set the item unpublished
				$table->published = 0;

				if ($extensionName == 'com_menus')
				{
					// Reset the home if it's a menu item.
					$table->home = 0;

					// Get menutype name and menu parent
					$jform      = $app->input->post->get('jform', array(), 'array');
					$menuType   = $jform['MenuType_' . $pk];
					$menuParent = $jform['MenuParent_' . $pk];

					if (!empty($menuType) && !empty($menuParent))
					{
						$table->menutype  = $menuType;
						$table->parent_id = $menuParent;
					}
					else
					{
						$this->setError(Text::_('COM_ASSOCIATIONS_ERROR_MENU_PARAMETERS_MISSED'));

						return false;
					}

					// Set location
					$table->setLocation($table->parent_id, 'last-child');
				}
				else
				{
					// Reset the hits if it's not a menu item.
					$table->hits = 0;

					if (!empty($fields['catid']))
					{
						// Get category id
						$catId = $app->input->post->get('CategoryValue_' . $pk, 0, 'INT');

						if ($catId !== 0)
						{
							$table->catid = $catId;
						}
						else
						{
							$this->setError(Text::_('COM_ASSOCIATIONS_ERROR_NO_CATEGORY_SELECTED'));

							return false;
						}
					}
				}

				// Get the featured state if it's an article
				if ($extensionName == 'com_content')
				{
					$featured = $table->featured;
				}

				// Check the row.
				if (!$table->check())
				{
					$this->setError($table->getError());

					return false;
				}

				// Store the row.
				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}

				// Rebuild the tree path.
				if ($extensionName == 'com_menus')
				{
					if (!$table->rebuildPath($table->id))
					{
						$this->setError($table->getError());

						return false;
					}
				}

				// Get the new item ID
				$newId = $table->getId();

				// Check if the article was featured and update the #__content_frontpage table
				if (isset($featured) && $featured == 1)
				{
					$db = $this->getDbo();
					$query = $db->getQuery(true)
						->insert($db->quoteName('#__content_frontpage'))
						->values($newId . ', 0');
					$db->setQuery($query);
					$db->execute();
				}

				// Add new item to associations
				$associations[$langCode]['id'] = (int) $table->getId();
				$assocChanged = true;
			}
			else
			{
				$associations[$langCode]['id'] = (int) $associations[$langCode]['id'];
			}
		}

		if ($assocChanged)
		{
			// Get associations key for edited item
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('key'))
				->from($db->quoteName('#__associations'))
				->where($db->quoteName('context') . ' = ' . $db->quote($associationContext))
				->where($db->quoteName('id') . ' = ' . (int) $itemId);
			$db->setQuery($query);
			$oldKey = $db->loadResult();

			// Deleting old associations for the associated items
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__associations'))
				->where('(' . $db->quoteName('context') . ' = ' . $db->quote($associationContext) . ') AND ('
					. $db->quoteName('key') . ' = ' . $db->quote($oldKey) . ')'
				);

			$db->setQuery($query);
			$db->execute();

			if ($itemLang !== '*')
			{
				$associations[$itemLang]['id'] = (int) $itemId;
			}

			// Add new associations
			if (count($associations) > 1)
			{
				// Adding new association for these items
				$key   = md5(json_encode($associations));
				$query = $db->getQuery(true)
					->insert('#__associations');

				foreach ($associations as $association)
				{
					$query->values(((int) $association['id']) . ',' . $db->quote($associationContext) . ',' . $db->quote($key));
				}

				$db->setQuery($query);
				$db->execute();
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_associations.autoassoc', 'autoassoc', array('control' => 'jform', 'load_data' => $loadData));

		return !empty($form) ? $form : false;
	}

	/**
	 * Method to preprocess the form.
	 *
	 * @param   \JForm  $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import.
	 *
	 * @return  void
	 *
	 * @see     \JFormField
	 * @since   1.6
	 * @throws  \Exception if there is an error in the form event.
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		// Get extension and content languages.
		$extensionName = explode('.', $this->state->get('itemtype'), 2)[0];
		$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

		// Association category items.
		if (count($languages) > 1)
		{
			$addform = new \SimpleXMLElement('<form />');

			foreach ($languages as $language)
			{
				$fieldset = $addform->addChild('fieldset');
				$fieldset->addAttribute('name', 'ParamCategory_' . $language->lang_code);

				$field = $fieldset->addChild('field');
				$field->addAttribute('name', 'Autoassoc_' . $language->lang_id);
				$field->addAttribute('type', 'modal_autoassoc');
				$field->addAttribute('lang_id', $language->lang_id);
				$field->addAttribute('language', $language->lang_code);
				$field->addAttribute('label', '');
				$field->addAttribute('translate_label', 'false');
				$field->addAttribute('extension', $extensionName);
				$field->addAttribute('select', 'true');
				$field->addAttribute('new', 'true');
				$field->addAttribute('edit', 'true');

				$fieldset = $addform->addChild('fieldset');
				$fieldset->addAttribute('name', 'ParamMenu_' . $language->lang_code);
				$fieldset->addAttribute('addfieldprefix', 'Joomla\Component\Menus\Administrator\Field');

				$field = $fieldset->addChild('field');
				$field->addAttribute('name', 'MenuType_' . $language->lang_id);
				$field->addAttribute('type', 'menu');
				$field->addAttribute('label', '');
				$field->addAttribute('clientid', '0');

				$field = $fieldset->addChild('field');
				$field->addAttribute('name', 'MenuParent_' . $language->lang_id);
				$field->addAttribute('type', 'MenuParent');
				$field->addAttribute('label', '');
				$field->addAttribute('clientid', '0');
			}

			$form->load($addform, false);
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}
}
