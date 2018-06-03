<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.tabstate');

HTMLHelper::_('script', 'com_languages/admin-language-edit-change-flag.js', ['relative' => true, 'version' => 'auto']);

$assoc = JLanguageAssociations::isEnabled();

$fallbacklang = $this->item->fallback_lang;
?>

<form action="<?php echo JRoute::_('index.php?option=com_languages&view=language&layout=edit&lang_id=' . (int) $this->item->lang_id); ?>" method="post" name="adminForm" id="language-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.item_title', $this); ?>

	<fieldset>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS')); ?>
			<?php echo $this->form->renderField('title'); ?>
			<?php echo $this->form->renderField('title_native'); ?>
			<?php echo $this->form->renderField('lang_code'); ?>
			<?php echo $this->form->renderField('sef'); ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('image'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('image'); ?>
					<span id="flag">
						<?php echo JHtml::_('image', 'mod_languages/' . $this->form->getValue('image') . '.gif', $this->form->getValue('image'), null, true); ?>
					</span>
				</div>
			</div>
			<?php if ($this->canDo->get('core.edit.state')) : ?>
				<?php echo $this->form->renderField('published'); ?>
			<?php endif; ?>

			<?php echo $this->form->renderField('access'); ?>
			<?php echo $this->form->renderField('description'); ?>
			<?php echo $this->form->renderField('lang_id'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'metadata', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS')); ?>
		<?php echo $this->form->renderFieldset('metadata'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'site_name', JText::_('COM_LANGUAGES_FIELDSET_SITE_NAME_LABEL')); ?>
		<?php echo $this->form->renderFieldset('site_name'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if ($assoc) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'automatic_association', JText::_('COM_LANGUAGES_FIELDSET_AUTOMATIC_ASSOCIATION_LABEL')); ?>
			<?php if ($this->item->lang_code !== $this->reference_lang) : ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('fallback_lang'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('fallback_lang'); ?>
						<span id="fallback_lang_flag">
							<?php if (!empty($fallbacklang)) : ?>
								<?php echo JHtml::_(
									'image', 'mod_languages/' . str_replace('-', '_', strtolower($fallbacklang)) . '.gif', $fallbacklang, null, true
								); ?>
							<?php else : ?>
								<img alt="">
							<?php endif; ?>
						</span>
					</div>
				</div>
			<?php else : ?>
				<div class="hidden">
					<?php echo $this->form->renderField('fallback_lang'); ?>
				</div>
			<?php endif; ?>

			<?php echo $this->form->renderFieldset('automatic_association'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</fieldset>
	<input type="hidden" name="task" value="">
	<?php echo JHtml::_('form.token'); ?>
</form>
