<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.tabstate');

JFactory::getDocument()->addScriptDeclaration(
	'
	function changeImg(field, flag_id) {
		jQuery("#" + field).on("change", function() {
			var flag = this.value;
			if (flag && flag != "root") {
				jQuery("#" + flag_id + " img").attr("src", "' . JUri::root(true)
	. '" + "/media/mod_languages/images/" + flag + ".gif").attr("alt", flag);
			}
			else
			{
				jQuery("#" + flag_id + " img").removeAttr("src").removeAttr("alt");
			}
		});
	}
	
	jQuery(document).ready(function() {
		changeImg("jform_image", "flag");
		changeImg("jform_params_fallback_lang", "fallback_lang_flag");
});'
);
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

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'automatic_association', JText::_('COM_LANGUAGES_FIELDSET_AUTOMATIC_ASSOCIATION_LABEL')); ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('fallback_lang', 'params'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('fallback_lang', 'params'); ?>
				<span id="fallback_lang_flag">
					<?php if (($fallbacklang = $this->form->getValue('fallback_lang', 'params')) != 'root') : ?>
						<?php echo JHtml::_(
							'image', 'mod_languages/' . $fallbacklang . '.gif', $fallbacklang, null, true
						); ?>
					<?php else : ?>
						<?php echo JHtml::_('image', '', '', null, true); ?>
					<?php endif; ?>
				</span>
			</div>
		</div>
		<?php echo $this->form->renderField('automatic_state', 'params'); ?>
		<?php echo $this->form->renderField('change_state', 'params'); ?>
		<?php echo $this->form->renderField('frontend_information', 'params'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</fieldset>
	<input type="hidden" name="task" value="">
	<?php echo JHtml::_('form.token'); ?>
</form>
