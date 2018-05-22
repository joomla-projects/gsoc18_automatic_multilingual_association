<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;

$modalId = 'Article_auto_associations';
$modalTitle = 'Multilingual Associations';
$url = 'index.php?option=com_content&amp;view=autoassociations&amp;layout=default&amp;tmpl=component&amp;' . \JSession::getFormToken() . '=1';


echo \JHtml::_(
	'bootstrap.renderModal',
	'ModalSelect' . $modalId,
	array(
		'title'       => $modalTitle,
		'url'         => $url,
		'height'      => '400px',
		'width'       => '800px',
		'bodyHeight'  => 70,
		'modalWidth'  => 80,
		'footer'      => '<a role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
			. \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
	)
);
