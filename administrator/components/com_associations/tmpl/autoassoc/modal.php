<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('script', 'com_associations/admin-autoassoc-modal.js', array('relative' => true));
HTMLHelper::_('script', 'system/fields/modal-fields.min.js', array('version' => 'auto', 'relative' => true));

$listOrder        = $this->escape($this->state->get('list.ordering'));
$listDirn         = $this->escape($this->state->get('list.direction'));
$canManageCheckin = JFactory::getUser()->authorise('core.manage', 'com_checkin');
$colSpan          = 4;

?>
<button id="applyBtn" type="button" class="hidden" onclick="void(0);"></button>
<button id="closeBtn" type="button" class="hidden" onclick="void(0);"></button>

<div class="container-popup">
	<form action="<?php echo JRoute::_('index.php?option=com_associations&view=autoassoc&layout=modal&id=' . $this->state->get('itemId')
		. '&itemtype=' . $this->state->get('itemtype')
	); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<table class="table table-striped" id="languagesList">
						<thead>
						<tr>
							<th style="width:1%" class="text-center">
								<?php echo JHtml::_('grid.checkall'); ?>
							</th>
							<th style="width:5%" class="nowrap text-center">
								<?php echo JText::_('JSTATUS'); ?>
							</th>
							<th style="width:10%" class="nowrap text-center">
								<?php echo JText::_('JGRID_HEADING_LANGUAGE'); ?>
							</th>
							<th class="nowrap text-center">
								<?php echo JText::_('JGLOBAL_TITLE'); ?>
							</th>
							<?php if (!empty($this->typeFields['catid'])) : ?>
								<th class="nowrap text-center">
									<?php echo JText::_('JCATEGORY'); ?>
								</th>
							<?php endif; ?>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="<?php echo $colSpan; ?>">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php foreach ($this->items as $i => $item) :?>
							<?php $hasAssociation = isset($item->item_title); ?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="text-center">
									<?php echo JHtml::_('grid.id', $i, $item->lang_id, $hasAssociation ? true : false); ?>
								</td>
								<td class="text-center">
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'languages.', false); ?>
								</td>
								<td class="text-center">
									<?php echo $this->escape($item->language); ?>
								</td>
								<td class="nowrap has-context text-center">
									<?php if ($hasAssociation) : ?>
										<?php echo $item->item_title; ?>
										<?php if (!is_null($this->typeFields['alias'])) : ?>
											<span class="small">
												<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
											</span>
										<?php endif; ?>
										<?php if (!empty($this->typeFields['catid'])) : ?>
											<div class="small">
												<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category); ?>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</td>
								<td class="nowrap has-context text-center">
									<?php if (!empty($this->typeFields['catid'])) : ?>
										<?php
										if ($hasAssociation)
										{
											echo $this->escape($item->category);
										}
										else
										{
											Factory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);

											$catId		= 0;
											$langId     = $item->lang_id;
											$modalId 	= 'Category_' . $item->lang_id;
											$catName    = 'CategoryValue_' . $item->lang_id;
											$modalTitle = Text::_('COM_CATEGORIES_CHANGE_CATEGORY');
											$title   	= Text::_('COM_CATEGORIES_SELECT_A_CATEGORY');

											$linkCategories = 'index.php?option=com_categories&amp;view=categories&amp;layout=modal'
												. '&amp;tmpl=component&amp;' . Session::getFormToken() . '=1&amp;extension='
												. $this->extensionName . '&amp;forcedLanguage=' . $item->language;
											$linkCategory   = 'index.php?option=com_categories&amp;view=category&amp;layout=modal'
												. '&amp;tmpl=component&amp;' . Session::getFormToken() . '=1&amp;extension='
												. $this->extensionName . '&amp;forcedLanguage=' . $item->language;

											$urlSelect = $linkCategories . '&amp;function=jSelectCategory_' . $langId;
											$urlNew    = $linkCategory . '&amp;task=category.add';
											$urlEdit   = $linkCategory . '&amp;task=category.edit&amp;id=\' + document.getElementById("'
												. $catId . '_id").value + \'';

											Factory::getDocument()->addScriptDeclaration("
											function jSelectCategory_" . $langId . "(id, title, object) {
												window.processModalSelect('Category', '" . $langId . "', id, title, '', object);
											}"
											);

											$html = '<span class="input-group"><input class="form-control" id="' . $langId
												. '_name" type="text" value="' . $title . '" disabled="disabled" size="35">'
												. '<span class="input-group-append">'
												. '<a'
												. ' class="btn btn-primary hasTooltip' . ($catId ? ' sr-only' : '') . '"'
												. ' id="' . $langId . '_select"'
												. ' data-toggle="modal"'
												. ' role="button"'
												. ' href="#ModalSelect' . $modalId . '"'
												. ' title="' . \JHtml::tooltipText('COM_CATEGORIES_CHANGE_CATEGORY') . '">'
												. '<span class="icon-file" aria-hidden="true"></span> ' . \JText::_('JSELECT')
												. '</a>'
												. '<a'
												. ' class="btn btn-secondary hasTooltip' . ($catId ? ' sr-only' : '') . '"'
												. ' id="' . $langId . '_new"'
												. ' data-toggle="modal"'
												. ' role="button"'
												. ' href="#ModalNew' . $modalId . '"'
												. ' title="' . \JHtml::tooltipText('COM_CATEGORIES_NEW_CATEGORY') . '">'
												. '<span class="icon-new" aria-hidden="true"></span> ' . \JText::_('JACTION_CREATE')
												. '</a>'
												. '<a'
												. ' class="btn btn-secondary hasTooltip' . ($catId ? '' : ' sr-only') . '"'
												. ' id="' . $langId . '_edit"'
												. ' data-toggle="modal"'
												. ' role="button"'
												. ' href="#ModalEdit' . $modalId . '"'
												. ' title="' . \JHtml::tooltipText('COM_CATEGORIES_EDIT_CATEGORY') . '">'
												. '<span class="icon-edit" aria-hidden="true"></span> ' . \JText::_('JACTION_EDIT')
												. '</a>'
												. '<a'
												. ' class="btn btn-secondary' . ($catId ? '' : ' sr-only') . '"'
												. ' id="' . $langId . '_clear"'
												. ' href="#"'
												. ' onclick="window.processModalParent(\'' . $langId . '\'); return false;">'
												. '<span class="icon-remove" aria-hidden="true"></span>' . \JText::_('JCLEAR')
												. '</a></span></span>';

											$html .= HTMLHelper::_(
												'bootstrap.renderModal',
												'ModalSelect' . $modalId,
												array(
													'title'      => $modalTitle,
													'url'        => $urlSelect,
													'height'     => '400px',
													'width'      => '800px',
													'bodyHeight' => 70,
													'modalWidth' => 80,
													'footer'     => '<a role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
														. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
												)
											);

											$html .= HTMLHelper::_(
												'bootstrap.renderModal',
												'ModalNew' . $modalId,
												array(
													'title'       => \JText::_('COM_CATEGORIES_NEW_CATEGORY'),
													'backdrop'    => 'static',
													'keyboard'    => false,
													'closeButton' => false,
													'url'         => $urlNew,
													'height'      => '400px',
													'width'       => '800px',
													'bodyHeight'  => 70,
													'modalWidth'  => 80,
													'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
														. ' onclick="window.processModalEdit(this, \'' . $langId . '\', \'add\', \'category\', \'cancel\', \'item-form\'); return false;">'
														. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
														. '<a role="button" class="btn btn-primary" aria-hidden="true"'
														. ' onclick="window.processModalEdit(this, \'' . $langId . '\', \'add\', \'category\', \'save\', \'item-form\'); return false;">'
														. Text::_('JSAVE') . '</a>'
														. '<a role="button" class="btn btn-success" aria-hidden="true"'
														. ' onclick="window.processModalEdit(this, \'' . $langId . '\', \'add\', \'category\', \'apply\', \'item-form\'); return false;">'
														. Text::_('JAPPLY') . '</a>',
												)
											);

											$html .= \JHtml::_(
												'bootstrap.renderModal',
												'ModalEdit' . $modalId,
												array(
													'title'       => \JText::_('COM_CATEGORIES_EDIT_CATEGORY'),
													'backdrop'    => 'static',
													'keyboard'    => false,
													'closeButton' => false,
													'url'         => $urlEdit,
													'height'      => '400px',
													'width'       => '800px',
													'bodyHeight'  => 70,
													'modalWidth'  => 80,
													'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
														. ' onclick="window.processModalEdit(this, \'' . $langId . '\', \'edit\', \'category\', \'cancel\', \'item-form\'); return false;">'
														. \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
														. '<a role="button" class="btn btn-primary" aria-hidden="true"'
														. ' onclick="window.processModalEdit(this, \'' . $langId . '\', \'edit\', \'category\', \'save\', \'item-form\'); return false;">'
														. \JText::_('JSAVE') . '</a>'
														. '<a role="button" class="btn btn-success" aria-hidden="true"'
														. ' onclick="window.processModalEdit(this, \'' . $langId . '\', \'edit\', \'category\', \'apply\', \'item-form\'); return false;">'
														. \JText::_('JAPPLY') . '</a>',
												)
											);

											$html .= '<input type="hidden" id="' . $langId . '_id" class="required modal-value" data-required="true"'
												. ' name="' . $catName . '" data-text="'
												. htmlspecialchars(\JText::_('COM_CATEGORIES_SELECT_A_CATEGORY', true), ENT_COMPAT, 'UTF-8')
												. '" value="' . $catId . '">';

											echo $html;
										}
										?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<input type="hidden" name="task" value="">
					<input type="hidden" name="boxchecked" value="0">
					<input type="hidden" name="assocLanguages" value="">
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>
		</div>
	</form>
</div>