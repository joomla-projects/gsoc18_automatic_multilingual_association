<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('script', 'com_associations/admin-autoassoc-modal.js', array('relative' => true));

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
							<th class="nowrap text-center">
								<?php echo JText::_('JCATEGORY'); ?>
							</th>
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
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'languages.', true); ?>
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
									<?php if ($hasAssociation && !empty($this->typeFields['catid'])) : ?>
										<?php echo $this->escape($item->category); ?>
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