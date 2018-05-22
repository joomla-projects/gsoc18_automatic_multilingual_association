<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Auto-Association Model.
 *
 * @since  1.6
 */
class AutoassociationsModel extends ListModel
{
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields from the languages table.
		$query->select('l.lang_id, l.lang_code')
			->from($db->quoteName('#__languages') . ' AS l');

		// Join over the articles.
		$query->select('c.id, c.title, c.language')
			->join('LEFT', $db->quoteName('#__content') . 'AS c ON c.language = l.lang_code');

		return $query;
	}
}