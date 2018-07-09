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
$modalId    = 'associationAddAssociations';
$modalTitle = 'Multilingual Associations';
$modalUrl   = 'index.php?option=com_associations&amp;view=autoassoc&amp;layout=modal&amp;tmpl=component&amp;itemtype=com_content.article';

if (!is_null($itemId))
{
	$modalUrl .= '&amp;id=' . $itemId;
}

echo \JHtml::_(
	'bootstrap.renderModal',
	$modalId,
	array(
		'title'       => $modalTitle,
		'url'         => $modalUrl,
		'height'      => '400px',
		'width'       => '800px',
		'bodyHeight'  => 70,
		'modalWidth'  => 80,
		'footer'      => '<a type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true"'
			. ' onclick="jQuery(\'#associationAddAssociations iframe\').contents().find(\'#closeBtn\').click();">'
			. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
			. '<button type="button" class="btn btn-primary" aria-hidden="true"'
			. ' onclick="jQuery(\'#associationAddAssociations iframe\').contents().find(\'#rememberBtn\').click();">'
			. 'Create & Remember</button>'
			. '<button type="button" class="btn btn-success" aria-hidden="true"'
			. ' onclick="jQuery(\'#associationAddAssociations iframe\').contents().find(\'#applyBtn\').click();">'
			. 'Create</button>',
	)
);
