<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_content&view=autoassociations'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<table class="table table-striped" id="articleList">
					<thead>
					<tr>
						<th style="width:1%" class="text-center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th style="width:40%" class="nowrap text-center">
							<?php echo JHtml::_('searchtools.sort', 'Languages', 'l.lang_code', $listDirn, $listOrder); ?>
						</th>
						<th style="min-width:100px" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'c.title', $listDirn, $listOrder); ?>
						</th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="11">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td>
								<?php echo JHtml::_('grid.id', $i, $item->lang_id); ?>
							</td>
							<td class="text-center">
								<?php echo $this->escape($item->lang_code); ?>
							</td>
							<td>
								<?php echo $this->escape($item->title); ?>
							</td>
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