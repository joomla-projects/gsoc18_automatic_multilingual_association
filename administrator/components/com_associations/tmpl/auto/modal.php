<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('jquery.framework');
JHtml::_('behavior.multiselect');

$listOrder        = $this->escape($this->state->get('list.ordering'));
$listDirn         = $this->escape($this->state->get('list.direction'));
$canManageCheckin = JFactory::getUser()->authorise('core.manage', 'com_checkin');
$colSpan          = 4;

// @TODO add scripts
?>
<form action="<?php echo JRoute::_('index.php?option=com_associations&view=auto&layout=modal'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				<table class="table table-striped" id="languagesList">
					<thead>
					<tr>
						<th style="width:1%" class="text-center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th style="width:5%" class="nowrap text-center">
							<?php echo JHtml::_(
								'searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder
							); ?>
						</th>
						<th style="width:20%" class="nowrap text-center">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'l.lang_code', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap text-center">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
						</th>
						<?php if (!empty($this->typeFields['menutype'])) : ?>
							<th style="width:10%" class="nowrap text-center">
								<?php echo JHtml::_('searchtools.sort', 'COM_ASSOCIATIONS_HEADING_MENUTYPE', 'menutype_title', $listDirn, $listOrder); $colSpan++; ?>
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
						<tr class="row<?php echo $i % 2; ?>">
							<td class="text-center">
								<?php echo JHtml::_('grid.id', $i, $item->lang_id); ?>
							</td>
							<td class="text-center">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'languages.', true); ?>
							</td>
							<td class="text-center">
								<?php echo $this->escape($item->language); ?>
							</td>
							<td class="nowrap has-context text-center">
								<?php if (!is_null($item->item_title)) : ?>
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
							<?php if (!empty($this->typeFields['menutype'])) : ?>
								<td class="small">
									<?php echo $this->escape($item->menutype_title); ?>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
