<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;

$itemId     = $displayData->get('Item')->id;
$modalId = 'Article_auto_associations';
$modalTitle = 'Multilingual Associations';
$modalUrl = 'index.php?option=com_associations&amp;view=auto&amp;layout=modal&amp;tmpl=component&amp;itemtype=com_content.article&amp;id=' . $itemId;

echo \JHtml::_(
	'bootstrap.renderModal',
	'ModalSelect' . $modalId,
	array(
		'title'       => $modalTitle,
		'url'         => $modalUrl,
		'height'      => '400px',
		'width'       => '800px',
		'bodyHeight'  => 70,
		'modalWidth'  => 80,
		'footer'      => '<a role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
			. \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
	)
);
